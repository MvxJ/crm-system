<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\CustomerAddress;
use App\Entity\CustomerSettings;
use App\Repository\CustomerAddressRepository;
use App\Repository\CustomerRepository;
use App\Repository\CustomerSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerSettingsService
{
    private CustomerSettingsRepository $settingsRepository;
    private EntityManagerInterface $entityManager;
    private CustomerAddressRepository $customerAddressRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        CustomerSettingsRepository $settingsRepository,
        EntityManagerInterface $entityManager,
        CustomerAddressRepository $customerAddressRepository,
        CustomerRepository $customerRepository
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->entityManager = $entityManager;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->customerRepository = $customerRepository;
    }

    public function editSettings(int $customerId, Request $request, ?string $userEmail = null): ?array
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer || $this->canUserAccessSettings($customer, $userEmail)) {
            return null;
        }

        $content = json_decode($request->getContent(), true);

        if (!$customer->getSettings()) {
            $settings = new CustomerSettings();
            $settings = $this->objectCreator($settings, $content);
            if ($settings) {
                $customer->setSettings($settings);
            }
        } else {
            $settings = $this->objectCreator($customer->getSettings(), $content);
        }

        if (array_key_exists('2fa', $content)) {
            $customer->setEmailAuthEnabled((bool)$content['2fa']);
        }

        $this->entityManager->persist($settings);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $this->createSettingsArray($settings);
    }

    public function getCustomerSettings(int $customerId, ?string $customerEmail = null): ?array
    {
        $customer = $this->customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer || $this->canUserAccessSettings($customer, $customerEmail)) {
            return null;
        }

        if (!$customer->getSettings()) {
            return [];
        }

        return $this->createSettingsArray($customer->getSettings());
    }

    private function objectCreator(CustomerSettings $settings, array $content): ?CustomerSettings
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(CustomerSettings::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setBillingAddress' || $setterMethod == 'setContactAddress') {
                    $address = $this->customerAddressRepository->findOneBy(['id' => $fieldValue]);

                    if (!$address) {
                        return null;
                    }

                    $settings->$setterMethod($address);
                } elseif (method_exists($settings, $setterMethod)) {
                    $settings->$setterMethod($fieldValue);
                }
            }
        }

        return $settings;
    }

    private function canUserAccessSettings(Customer $customer, ?string $customerEmail = null): bool
    {
        return !($customerEmail != null && $customerEmail == $customer->getEmail());
    }

    private function createSettingsArray(CustomerSettings $settings): array
    {
        return [
            'emailNotification' => $settings->getEmailNotifications(),
            'smsNotification' => $settings->isSmsNotifications(),
            'contactAddressId' => $settings->getContactAddress()->getId(),
            'billingAddressId' => $settings->getBillingAddress()->getId()
        ];
    }
}