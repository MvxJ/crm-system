<?php

namespace App\Service;

use App\Entity\ServiceRequest;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\ServiceRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ServiceRequestService
{
    private ServiceRequestRepository $serviceRequestRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private ContractRepository $contractRepository;

    public function __construct(
       ServiceRequestRepository $serviceRequestRepository,
       EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        ContractRepository $contractRepository
    ) {
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->contractRepository = $contractRepository;
    }

    public function getServiceRequests(Request $request): ?array
    {
        return null;
    }

    public function getServiceRequestDetails(int $serviceRequestId): ?array
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return null;
        }

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function getCustomerServiceRequests(Request $request, string $userEmail): ?array
    {
        return null;
    }

    public function getCustomerServiceRequestDetails(int $serviceRequestId, string $customerEmail): ?array
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

        //TODO:: Object creator => create object from request content

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
        $serviceRequest->setCustomer($customer);

        //TODO:: Object creator => create object from request content

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function editServiceRequest(int $serviceRequestId, Request $request): ?array
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        //TODO:: Object creator;

        $this->entityManager->persist($serviceRequest);
        $this->entityManager->flush();

        return $this->createServiceRequestArray($serviceRequest, true);
    }

    public function deleteServiceRequest(int $serviceRequestId): bool
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $serviceRequestId]);

        if (!$serviceRequest) {
            return false;
        }

        $this->entityManager->remove($serviceRequest);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(ServiceRequest $serviceRequest, array $content): ?ServiceRequest
    {
        return $serviceRequest;
    }

    private function createServiceRequestArray(ServiceRequest $serviceRequest, bool $details = false): array
    {
        $serviceRequestArray = [
            'id' => $serviceRequest->getId(),
            'customer' => [
                'id' => $serviceRequest->getCustomer()->getId(),
                'email' => $serviceRequest->getCustomer()->getEmail()
            ],
            'user' => [
                'id' => $serviceRequest->getUser()->getId(),
                'email' => $serviceRequest->getUser()->getEmail()
            ],
            'closed' => $serviceRequest->getIsClosed(),
            'createdDate' => $serviceRequest->getCreatedDate()
        ];

        if ($details) {
            $serviceRequestArray['contract'] = [
                'id' => $serviceRequest->getContract()->getId(),
                'number' => $serviceRequest->getContract()->getNumber()
            ];
            $serviceRequestArray['localization'] = [
                'address' => $serviceRequest->getContract()->getAddress(),
                'city' => $serviceRequest->getContract()->getCity(),
                'zipCode' => $serviceRequest->getContract()->getZipCode()
            ];
            $serviceRequestArray['description'] = $serviceRequest->getDescription();
        }

        return $serviceRequestArray;
    }
}