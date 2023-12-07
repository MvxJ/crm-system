<?php

namespace App\Service;

use App\Entity\ServiceRequest;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\ServiceRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class ServiceRequestService
{
    private ServiceRequestRepository $serviceRequestRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private ContractRepository $contractRepository;
    private UserRepository $userRepository;

    public function __construct(
       ServiceRequestRepository $serviceRequestRepository,
       EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        ContractRepository $contractRepository,
        UserRepository $userRepository
    ) {
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
    }

    public function getServiceRequests(Request $request): ?array
    {
        $serviceRequestsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $user = $request->get('userId', 'all');
        $customer = $request->get('customerId', 'all');
        $status = $request->get('status', 'all');
        $maxResults = $this->serviceRequestRepository->countServiceRequests($status, $user, $customer);
        $serviceRequests = $this->serviceRequestRepository->getServiceRequestsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $status,
            $user,
            $customer
        );

        if (count($serviceRequests) == 0) {
            return null;
        }

        /** @var ServiceRequest $serviceRequest */
        foreach ($serviceRequests as $serviceRequest) {
            $serviceRequestsArray[] = $this->createServiceRequestArray($serviceRequest);
        }

        return [
            'serviceRequests' => $serviceRequestsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getServiceRequestDetails(Uuid $serviceRequestId): ?array
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return null;
        }

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function getCustomerServiceRequests(Request $request, string $userEmail): ?array
    {
        $serviceRequestsArray = [];
        $maxResults = $this->serviceRequestRepository->countServiceRequestsByCustomer($userEmail);
        $serviceRequests = $this->serviceRequestRepository->findServiceRequestsWithPaginationByCustomer($userEmail);

        if (count($serviceRequests) == 0) {
            return null;
        }

        /** @var ServiceRequest $serviceRequest */
        foreach ($serviceRequests as $serviceRequest) {
            $serviceRequestsArray[] = $this->createServiceRequestArray($serviceRequest);
        }

        return [
            'serviceRequests' => $serviceRequestsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getCustomerServiceRequestDetails(Uuid $serviceRequestId, string $customerEmail): ?array
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest || $serviceRequest->getCustomer()->getEmail() != $customerEmail) {
            return null;
        }

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function addServiceRequestByAdmin(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);
        $serviceRequest = new ServiceRequest();
        $serviceRequest = $this->objectCreator($serviceRequest, $content);
        $serviceRequest->setCreatedDate(new \DateTime());
        $serviceRequest->setStatus(ServiceRequest::STATUS_OPENED);

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function addServiceRequestByCustomer(Request $request, string $customerEmail): ?array
    {
        $content = json_decode($request->getContent(), true);
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        if (!$customer) {
            return null;
        }

        $serviceRequest = new ServiceRequest();
        $serviceRequest = $this->objectCreator($serviceRequest, $content);
        $serviceRequest->setCustomer($customer);
        $serviceRequest->setCreatedDate(new \DateTime());
        $serviceRequest->setStatus(ServiceRequest::STATUS_OPENED);

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function editServiceRequest(Uuid $serviceRequestId, Request $request): ?array
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $serviceRequest = $this->objectCreator($serviceRequest, $content);

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function cancelServiceRequestByCustomer(Uuid $serviceRequestId, string $userEmail): bool
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest || $userEmail != $serviceRequest->getCustomer()->getEmail()) {
            return false;
        }

        $serviceRequest->setStatus(ServiceRequest::STATUS_CANCELLED);
        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return true;
    }

    public function deleteServiceRequest(Uuid $serviceRequestId): bool
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return false;
        }

        $serviceRequest->setIsClosed(true);
        $serviceRequest->setCloseDate(new \DateTime());
        $serviceRequest->setStatus(ServiceRequest::STATUS_CLOSED);

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(ServiceRequest $serviceRequest, array $content): ?ServiceRequest
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(ServiceRequest::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $serviceRequest->setCustomer($customer);
                }

                elseif ($setterMethod == 'setUser') {
                    $user = $this->userRepository->findOneBy(['id' => $fieldValue]);

                    if (!$user) {
                        return null;
                    }

                    $serviceRequest->setUser($user);
                }

                elseif ($setterMethod == 'setContract') {
                    $contract = $this->contractRepository->findOneBy(['id' => $fieldValue]);

                    if (!$contract) {
                        return null;
                    }

                    $serviceRequest->setContract($contract);
                }

                elseif ($setterMethod == 'setIsClosed') {
                    $serviceRequest->setIsClosed($fieldValue);

                    if (!$fieldValue) {
                        $serviceRequest->setCloseDate(null);
                    } else {
                        $serviceRequest->setCloseDate(new \DateTime());
                    }
                }

                elseif (method_exists($serviceRequest, $setterMethod)) {
                    $serviceRequest->$setterMethod($fieldValue);
                }
            }
        }

        return $serviceRequest;
    }

    private function createServiceRequestArray(ServiceRequest $serviceRequest, bool $details = false): array
    {
        $serviceRequestArray = [
            'id' => $serviceRequest->getId(),
            'customer' => [
                'id' => $serviceRequest->getCustomer()->getId(),
                'email' => $serviceRequest->getCustomer()->getEmail(),
                'name' => $serviceRequest->getCustomer()->getFirstName(),
                'surname' => $serviceRequest->getCustomer()->getLastName()
            ],
            'closed' => $serviceRequest->getIsClosed(),
            'createdDate' => $serviceRequest->getCreatedDate(),
            'status' => $serviceRequest->getStatus()
        ];

        $serviceRequestArray['contract'] = [
            'id' => $serviceRequest->getContract()->getId(),
            'number' => $serviceRequest->getContract()->getNumber()
        ];
        
        if ($serviceRequest->getUser() != null) {
            $serviceRequestArray['user'] = [
                'id' => $serviceRequest->getUser()->getId(),
                'email' => $serviceRequest->getUser()->getEmail(),
                'name' => $serviceRequest->getUser()->getName(),
                'surname' => $serviceRequest->getUser()->getSurname(),
                'username' => $serviceRequest->getUser()->getUsername()
            ];
        } else {
            $serviceRequestArray['user'] = null;
        }

        if ($details) {
            $serviceRequestArray['localization'] = [
                'address' => $serviceRequest->getContract()->getAddress(),
                'city' => $serviceRequest->getContract()->getCity(),
                'zipCode' => $serviceRequest->getContract()->getZipCode()
            ];
            $serviceRequestArray['description'] = $serviceRequest->getDescription();
            $serviceRequestArray['serviceVisits'] = count($serviceRequest->getServiceVisits());
            $serviceRequestArray['comments'] = count($serviceRequest->getComments());
        }

        return $serviceRequestArray;
    }
}