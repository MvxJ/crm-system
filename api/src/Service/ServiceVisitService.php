<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\ServiceVisit;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\ServiceRequestRepository;
use App\Repository\ServiceVisitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceVisitService
{
    private ServiceVisitRepository $serviceVisitRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private UserRepository $userRepository;
    private ServiceRequestRepository $serviceRequestRepository;

    public function __construct(
        ServiceVisitRepository $serviceVisitRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        UserRepository $userRepository,
        ServiceRequestRepository $serviceRequestRepository
    ) {
        $this->serviceVisitRepository = $serviceVisitRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
    }

    public function getServiceVisitList(Request $request): ?array
    {
        $serviceVisitsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $user = $request->get('userId', 'all');
        $customer = $request->get('customerId', 'all');
        $serviceRequestId = $request->get('serviceRequestId', 'all');
        $maxResults = $this->serviceVisitRepository->countServiceVisits($user, $customer, $serviceRequestId);
        $serviceVisits = $this->serviceVisitRepository->getServiceVisitsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $user,
            $customer,
            $serviceRequestId
        );

        if (count($serviceVisits) == 0) {
            return null;
        }

        /** @var ServiceVisit $serviceVisit */
        foreach ($serviceVisits as $serviceVisit) {
            $serviceVisitsArray[] = $this->createServiceVisitArray($serviceVisit);
        }

        return [
            'serviceVisits' => $serviceVisitsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getServiceVisitDetail(int $id): ?array
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return null;
        }

        return $this->createServiceVisitArray($serviceVisit, true);
    }

    public function getCustomerServiceVisitList(Request $request, string $customerEmail): ?array
    {
        $serviceVisitsArray = [];
        $maxResults = $this->serviceVisitRepository->countServiceVisitsByCustomer($customerEmail);
        $serviceVisits = $this->serviceVisitRepository->findServiceVisitsByCustomer($customerEmail);

        if (count($serviceVisits) == 0) {
            return null;
        }

        /** @var ServiceVisit $serviceVisit */
        foreach ($serviceVisits as $serviceVisit) {
            $serviceVisitsArray[] = $this->createServiceVisitArray($serviceVisit);
        }

        return [
            'serviceVisits' => $serviceVisitsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getCustomerServiceVisitDetail(int $id, string $customerEmail): ?array
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit || $serviceVisit->getCustomer()->getEmail() != $customerEmail) {
            return null;
        }

        return $this->createServiceVisitArray($serviceVisit);
    }

    public function addServiceVisit(Request $request): ?array
    {
        $serviceVisit = new ServiceVisit();
        $content = json_decode($request->getContent(), true);
        $serviceVisit = $this->objectCreator($serviceVisit, $content);

        if (!$serviceVisit) {
            return null;
        }

        $serviceVisit->setCreatedDate(new \DateTime());

        $this->entityManager->persist($serviceVisit);
        $this->entityManager->flush();

        return $this->createServiceVisitArray($serviceVisit, true);
    }

    public function editServiceVisit(int $id, Request $request): ?array
    {
        $serviceVisit = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $serviceVisit = $this->objectCreator($serviceVisit, $content);

        if (!$serviceVisit) {
            return null;
        }

        $serviceVisit->setEditDate(new \DateTime());

        $this->entityManager->persist($serviceVisit);
        $this->entityManager->flush();

        return $this->createServiceVisitArray($serviceVisit, true);
    }

    public function deleteServiceVisit(int $id): bool
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return false;
        }

        $this->entityManager->remove($serviceVisit);
        $this->entityManager->flush();

        return true;
    }

    public function cancelServiceVisit(int $id, string $userEmail): bool
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit || $serviceVisit->getCustomer()->getEmail() != $userEmail) {
            return false;
        }

        $serviceVisit->setCancelled(true);

        $this->entityManager->persist($serviceVisit);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(ServiceVisit $serviceVisit, array $content): ?ServiceVisit
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(ServiceVisit::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $serviceVisit->setCustomer($customer);
                }

                elseif ($setterMethod == 'setUser') {
                    $user = $this->userRepository->findOneBy(['id' => $fieldValue]);

                    if (!$user) {
                        return null;
                    }

                    $serviceVisit->setUser($user);
                }

                elseif ($setterMethod == 'setServiceRequest') {
                    $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $fieldValue]);

                    if (!$serviceRequest) {
                        return null;
                    }

                    $serviceVisit->setServiceRequest($serviceRequest);
                }

                elseif ($setterMethod == 'setIsClosed') {
                    $serviceVisit->setIsFinished($fieldValue);
                }

                elseif ($setterMethod == 'setDate' || $setterMethod == 'setStartTime' || $setterMethod == 'setEndTime') {
                    $serviceVisit->$setterMethod(new \DateTime($fieldValue));
                }


                elseif (method_exists($serviceVisit, $setterMethod)) {
                    $serviceVisit->$setterMethod($fieldValue);
                }
            }
        }

        return $serviceVisit;
    }

    private function createServiceVisitArray(ServiceVisit $serviceVisit, bool $details = false): array
    {
        $serviceVisitArray = [
            'id' => $serviceVisit->getId(),
            'title' => $serviceVisit->getTitle(),
            'date' => $serviceVisit->getDate(),
            'start' => $serviceVisit->getStartTime(),
            'end' => $serviceVisit->getEndTime(),
            'user' => [
                'id' => $serviceVisit->getUser()->getId(),
                'username' => $serviceVisit->getUser()->getUsername()
            ],
            'cancelled' => $serviceVisit->isCancelled()
        ];

        if ($details) {
            $serviceVisitArray['description'] = $serviceVisit->getDescription();
            $serviceVisitArray['customer'] = [
                'id' => $serviceVisit->getCustomer()->getId(),
                'email' => $serviceVisit->getCustomer()->getEmail()
            ];
            $serviceVisitArray['createdAt'] = $serviceVisit->getCreatedDate();
            $serviceVisitArray['finished'] = $serviceVisit->getIsFinished();
            $serviceVisitArray['edited'] = $serviceVisit->getEditDate();
        }

        return $serviceVisitArray;
    }
}