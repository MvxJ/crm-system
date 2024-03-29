<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\CustomerSettings;
use App\Entity\Role;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class CustomerService
{
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $entityManager;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private MailerService $mailerService;

    public function __construct(
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        MailerService $mailerService
    ) {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->mailerService = $mailerService;
    }

    public function getCustomers(Request $request): array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $searchQuery = $request->get('searchTerm', null);
        $totalItems = count($this->customerRepository->findAll());
        $results = $this->customerRepository->getCustomersWithPagination((int)$itemsPerPage, (int)$page, $searchQuery);
        $customers = [];

        /** @var Customer $customer */
        foreach ($results as $customer) {
            $customers[] = $this->createCustomerArray($customer);
        }

        return [
            'customers' => $customers,
            'maxResults' => $totalItems
        ];
    }

    public function getCustomer(Uuid $customerId): ?array
    {
        $customer  = $this->customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer) {
            return null;
        }

        return $this->createCustomerArray($customer, true);
    }

    public function deleteCustomer(Uuid $customerId): bool
    {
        $customer = $this->customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer) {
            return false;
        }

        $customer->setIsDisabled(true);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return true;
    }

    public function addCustomer(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);
        $customer = new Customer();
        $customer = $this->objectCreator($customer, $content);

        if (!$customer) {
            return null;
        }

        $roleCustomer = $this->roleRepository->findOneBy(['role' => Role::ROLE_CUSTOMER]);
        $settings = new CustomerSettings();

        $customer->addRole($roleCustomer);
        $customer->setSettings($settings);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $this->mailerService->sendConfirmationEmail(
            'api_register_confirm',
            $customer,
            $content['password']
        );

        return $this->createCustomerArray($customer, true);
    }

    public function editCustomer(Uuid $customerId, Request $request): ?array
    {
        $customer = $this->customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $customer = $this->objectCreator($customer, $content);

        if (!$customer) {
            return null;
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $this->createCustomerArray($customer, true);
    }

    public function getCustomerProfile(string $customerEmail): ?array
    {
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        if (!$customer) {
            return null;
        }

        return $this->createCustomerArray($customer, true);
    }

    public function editCustomerProfile(string $customerEmail, Request $request): ?array
    {
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        if (!$customer) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $customer = $this->objectCreator($customer, $content);

        if (!$customer) {
            return null;
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $this->createCustomerArray($customer, true);
    }

    public function changePassword(string $customerEmail, Request $request): bool
    {
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        if (!$customer) {
            return false;
        }

        $content = json_decode($request->getContent(), true);

        if (!array_key_exists('newPassword', $content) || !array_key_exists('oldPassword', $content)) {
            return false;
        }

        if (!$this->userPasswordHasher->isPasswordValid($customer, $content['oldPassword'])) {
            return false;
        }

        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $content['newPassword']));

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return true;
    }

    private function createCustomerArray(Customer $customer, ?bool $details = false): array
    {
        $customerArray = [
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'secondName' => $customer->getSecondName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phoneNumber' => $customer->getPhoneNumber(),
            'isVerified' => (bool)$customer->isVerified(),
            'isActive' => (bool)!$customer->isDisabled(),
            'socialSecurityNumber' => $customer->getSocialSecurityNumber()
        ];

        if ($details) {
            $customerArray['birthDate'] = $customer->getBirthDate();
            $customerArray['twoFactorAuth'] = $customer->isEmailAuthEnabled();
            $customerArray['smsNotification'] = $customer->getSettings() ? $customer->getSettings()->isSmsNotifications() : null;
            $customerArray['emailNotification'] = $customer->getSettings() ? $customer->getSettings()->getEmailNotifications(): null;
            $customerArray['numberOfContracts'] = count($customer->getContracts());
            $customerArray['numberOfDevices'] = count($customer->getDevices());
            $customerArray['numberOfServiceRequests'] = count($customer->getServiceRequests());
            $customerArray['numberOfBills'] = count($customer->getBills());
            $customerArray['numberOfPayments'] = count($customer->getPayments());
            $customerArray['numberOfMessages'] = count($customer->getMessages());
        }

         if ($customer->getSettings() != null && $details) {
            $customerSettings = $customer->getSettings();

                if ($customerSettings->getContactAddress()) {
                    $contactAddress  = $customer->getSettings()->getContactAddress();

                    $customerArray['addresses']['contact'] = [
                        "id" => $contactAddress->getId(),
                        "country" => $contactAddress->getCountry(),
                        "city" => $contactAddress->getCity(),
                        "zipCode" => $contactAddress->getZipCode(),
                        "address" => $contactAddress->getAddress(),
                        "phoneNumber" => $contactAddress->getPhoneNumber(),
                        "emailAddress" => $contactAddress->getEmailAddress(),
                        "companyName" => $contactAddress->getCompanyName(),
                        "taxId" => $contactAddress->getTaxId()
                    ];
                }

                if ($customerSettings->getBillingAddress()) {
                    $billingAddress  = $customer->getSettings()->getBillingAddress();

                    $customerArray['addresses']['contact'] = [
                        "id" => $billingAddress->getId(),
                        "country" => $billingAddress->getCountry(),
                        "city" => $billingAddress->getCity(),
                        "zipCode" => $billingAddress->getZipCode(),
                        "address" => $billingAddress->getAddress(),
                        "phoneNumber" => $billingAddress->getPhoneNumber(),
                        "emailAddress" => $billingAddress->getEmailAddress(),
                        "companyName" => $billingAddress->getCompanyName(),
                        "taxId" => $billingAddress->getTaxId()
                    ];
                }
         }

        return  $customerArray;
    }

    private function objectCreator(Customer $customer, array $content): ?Customer
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Customer::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setPassword') {
                    $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $fieldValue));
                } elseif ($setterMethod == 'setBirthDate') {
                    $customer->setBirthDate(new \DateTime($fieldValue));
                } elseif (method_exists($customer, $setterMethod)) {
                    $customer->$setterMethod($fieldValue);
                }
            }
        }

        return $customer;
    }
}