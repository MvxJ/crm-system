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

    public function editAddress(int $addressId, Request $request, ?string $userEmail = null): ?array
    {
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if (!$address || ($userEmail != null && $userEmail != $address->getCustomer()->getEmail())) {
            return null;
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

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return true;
    }

    public function deleteAddressByCustomer(int $id, string $email): bool
    {
        $address = $this->addressRepository->findOneBy(['id' => $id]);

        if (!$address || $address->getCustomer()->getEmail() != $email) {
            return false;
        }

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

    public function getCustomerAddress(int $addressId, string $userEmail): ?array
    {
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if (!$address || $address->getCustomer()->getEmail() != $userEmail) {
            return null;
        }

        return $this->createAddressArray($address);
    }

    public function getCustomerAddresses(Request $request, ?string $userEmail = null): array
    {
        $customerAddresses = [];

        if (!$userEmail) {
            $userId = $request->get('customerId', 'all');
            $addresses = $this->addressRepository->findAddressesByCustomerId($userId);
        } else {
            $addresses = $this->addressRepository->findCustomerAddresses($userEmail);
        }

        /** @var CustomerAddress $address */
        foreach ($addresses as $address) {
            $customerAddresses[] = $this->createAddressArray($address);
        }

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
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(CustomerAddress::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $address->setCustomer($customer);
                } elseif (method_exists($address, $setterMethod)) {
                    $address->$setterMethod($fieldValue);
                }
            }
        }

        return $address;
    }
}