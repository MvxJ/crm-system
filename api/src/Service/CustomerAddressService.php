<?php

namespace App\Service;

use App\Entity\CustomerAddress;
use App\Repository\CustomerAddressRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerAddressService
{
    private CustomerAddressRepository $addressRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;

    public function __construct(
        CustomerAddressRepository $addressRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
    }

    public function addAddress(Request $request): ?array
    {
        $address = new CustomerAddress();
        $content = json_decode($request->getContent(), true);
        $address = $this->objectCreator($address, $content);

        if (!$address) {
            return null;
        }

        return $this->createAddressArray($address);
    }

    public function editAddress(int $addressId, Request $request): ?array
    {
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if (!$address) {
            return null;;
        }

        $content = json_decode($request->getContent(), true);
        $address = $this->objectCreator($address, $content);

        if (!$address) {
            return null;
        }

        return $this->createAddressArray($address);
    }

    public function deleteAddress(int $id): bool
    {
        $address = $this->addressRepository->findOneBy(['id' => $id]);

        if (!$address) {
            return false;
        }

        $customer = $address->getCustomer();

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return true;
    }

    public function getAddress(int $addressId): ?array
    {
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if (!$address) {
            return null;
        }

        return $this->createAddressArray($address);
    }

    public function getCustomerAddresses(Request $request, ?string $userEmail = null): array
    {
        $customerAddresses = [];

        return $customerAddresses;
    }

    private function createAddressArray(CustomerAddress $address): array
    {
        return [
            'id' => $address->getId(),
            'country' => $address->getCountry(),
            'city' => $address->getCity(),
            'zipCode' => $address->getZipCode(),
            'address' => $address->getAddress(),
            'type' => $address->getType(),
            'phoneNumber' => $address->getPhoneNumber(),
            'emailAddress' => $address->getEmailAddress(),
            'companyName' => $address->getCompanyName(),
            'taxId' => $address->getTaxId()
        ];
    }

    private function objectCreator(CustomerAddress $address, array $content): ?CustomerAddress
    {
        return $address;
    }
}