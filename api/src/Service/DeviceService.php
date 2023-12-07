<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Device;
use App\Repository\CustomerRepository;
use App\Repository\DeviceRepository;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class DeviceService
{
    private DeviceRepository $deviceRepository;
    private EntityManagerInterface$entityManager;
    private CustomerRepository $customerRepository;
    private ModelRepository $modelRepository;

    public function __construct(
        DeviceRepository $deviceRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        ModelRepository $modelRepository
    ) {
        $this->entityManager = $entityManager;
        $this->deviceRepository = $deviceRepository;
        $this->customerRepository = $customerRepository;
        $this->modelRepository = $modelRepository;
    }

    public function getDeviceDetails(Uuid $deviceId): ?array
    {
        $device = $this->deviceRepository->findOneBy(['id' => $deviceId]);

        if (!$device) {
            return null;
        }

        return [
            'id' => $device->getId(),
            'model' => [
                'id' => $device->getModel()->getId(),
                'manufacturer' => $device->getModel()->getManufacturer(),
                'name' => $device->getModel()->getName(),
                'price' => $device->getModel()->getPrice(),
                'type' => $device->getModel()->getType(),
            ],
            'serialNo' => $device->getSerialNumber(),
            'macAddress' => $device->getMacAddress(),
            'status' => $device->getStatus(),
            'boughtDate' => $device->getBoughtDate(),
            'soldDate' => $device->getSoldDate(),
            'user' => $device->getUser() ? [
                'id' => $device->getUser()->getId(),
                'name' => $device->getUser()->getFirstName(),
                'lastName' => $device->getUser()->getLastName()
            ] : null
        ];
    }

    public function getDeviceList(Request $request): ?array
    {
        $devicesArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'ASC');
        $orderBy = $request->get('orderBy', 'id');
        $status = $request->get('status', 'all');
        $customerId = $request->get('customerId', 'all');
        $maxDevices = $this->deviceRepository->countDevices($status, $customerId);
        $devices = $this->deviceRepository->findDevicesWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $orderBy,
            $order,
            $status,
            $customerId
        );

        /** @var Device $device */
        foreach ($devices as $device) {
            $devicesArray[] = [
                'id' => $device->getId(),
                'macAddress' => $device->getMacAddress(),
                'serialNo' => $device->getSerialNumber(),
                'status' => $device->getStatus(),
                'type' => $device->getModel()->getType(),
                'model' => $device->getModel()->getName()
            ];
        }

        return [
            'devices' => $devicesArray,
            'maxResults' => $maxDevices
        ];
    }

    public function addDevice(Request $request): ?array
    {
        $device = new Device();
        $content = json_decode($request->getContent(), true);

        $device = $this->objectCreator($content, $device);

        if (!$device) {
            return null;
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();

        return [
            'id' => $device->getId(),
            'macAddress' => $device->getMacAddress(),
            'serialNo' => $device->getSerialNumber(),
            'status' => $device->getStatus(),
            'userId' => $device->getUser() ? $device->getUser()->getId() : null,
            'boughtDate' => $device->getBoughtDate(),
            'soldDate' => $device->getSoldDate(),
            'model' => [
                'id' => $device->getModel()->getId(),
                'manufacturer' => $device->getModel()->getManufacturer(),
                'name' => $device->getModel()->getName(),
                'type' => $device->getModel()->getType()
            ]
        ];
    }

    public function editDevice(Uuid $deviceId, Request $request): ?array
    {
        $device = $this->deviceRepository->findOneBy(['id' => $deviceId]);

        if (!$device) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $device = $this->objectCreator($content, $device);

        if (!$device) {
            return null;
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();

        return [
            'id' => $device->getId(),
            'macAddress' => $device->getMacAddress(),
            'serialNo' => $device->getSerialNumber(),
            'status' => $device->getStatus(),
            'userId' => $device->getUser() ? $device->getUser()->getId() : null,
            'boughtDate' => $device->getBoughtDate(),
            'soldDate' => $device->getSoldDate(),
            'model' => [
                'id' => $device->getModel()->getId(),
                'manufacturer' => $device->getModel()->getManufacturer(),
                'name' => $device->getModel()->getName(),
                'type' => $device->getModel()->getType()
            ]
        ];
    }

    public function deleteDevice(Uuid $deviceId): bool
    {
        $device = $this->deviceRepository->findOneBy(['id' => $deviceId]);

        if (!$device) {
            return false;
        }

        if ($device->getUser()) {
            /** @var Customer $customer */
            $customer = $device->getUser();
            $customer->removeDevice($device);
        }

        $this->entityManager->remove($device);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(array $content, Device $device): ?Device
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Device::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if (method_exists($device, $setterMethod)) {

                    if ($setterMethod == 'setModel') {
                        $model = $this->modelRepository->findOneBy(['id' => (int)$fieldValue]);

                        if (!$model) {
                            return null;
                        }

                        $device->$setterMethod($model);
                    } elseif ($setterMethod == 'setUser') {
                        $customer = $this->customerRepository->findOneBy(['id' => (int)$fieldValue]);

                        if (!$customer) {
                            return null;
                        }

                        $device->$setterMethod($customer);
                    } elseif ($setterMethod == 'setBoughtDate' || $setterMethod == 'setSoldDate') {
                        $date = new \DateTime($fieldValue);
                        $device->$setterMethod($date);
                    } else {
                        $device->$setterMethod($fieldValue);
                    }
                }
            }
        }

        return $device;
    }
}