<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Device;
use App\Entity\Model;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class ModelService
{
    private ModelRepository $modelRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ModelRepository $modelRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->modelRepository = $modelRepository;
        $this->entityManager = $entityManager;
    }

    public function getModels(Request $request): ?array
    {
        $modelsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $orderBy = $request->get('orderBy', 'manufacturer');
        $order = $request->get('order', 'ASC');
        $type = $request->get('type', 'all');
        $searchTerm = $request->get('searchTerm', null);
        $maxModels = $this->modelRepository->countModels($type, $searchTerm);
        $models = $this->modelRepository->getModelsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $orderBy,
            $order,
            $type,
            $searchTerm
        );

        if (count($models) == 0) {
            return [];
        }

        /** @var Model $model */
        foreach ($models as $model) {
            $modelsArray[] = [
                'id' => $model->getId(),
                'isDeleted' => $model->isIsDeleted(),
                'manufacturer' => $model->getManufacturer(),
                'name' => $model->getName(),
                'type' => $model->getType(),
                'price' => $model->getPrice(),
            ];
        }

        return [
            'models' => $modelsArray,
            'maxResults' => $maxModels
        ];
    }

    public function editModel(int $modelId, Request $request): ?array
    {
        $model = $this->modelRepository->findOneBy(['id' => $modelId]);

        if (!$model) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $model = $this->objectCreator($content, $model);

        $this->entityManager->persist($model);
        $this->entityManager->flush();

        return [
            'id' => $model->getId(),
            'isDeleted' => $model->isIsDeleted(),
            'manufacturer' => $model->getManufacturer(),
            'name' => $model->getName(),
            'type' => $model->getType(),
            'price' => $model->getPrice(),
            'description' => $model->getDescription(),
            'params' => $model->getParams()
        ];
    }

    public function addModel(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);
        $model = new Model();
        $model = $this->objectCreator($content, $model);

        $this->entityManager->persist($model);
        $this->entityManager->flush();

        return [
            'id' => $model->getId(),
            'isDeleted' => $model->isIsDeleted(),
            'manufacturer' => $model->getManufacturer(),
            'name' => $model->getName(),
            'type' => $model->getType(),
            'price' => $model->getPrice(),
            'description' => $model->getDescription(),
            'params' => $model->getParams()
        ];
    }

    public function deleteModel(int $modelId): bool
    {
        $model = $this->modelRepository->findOneBy(['id' => $modelId]);

        if (!$model) {
            return false;
        }

        if ($model->isIsDeleted()) {
            return false;
        }

        $model->setIsDeleted(true);

        $this->entityManager->persist($model);
        $this->entityManager->flush();

        return true;
    }

    public function getModelDetails(Uuid $modelId): ?array
    {
        $model = $this->modelRepository->findOneBy(['id' => $modelId]);

        if (!$model) {
            return null;
        }

        return [
            'id' => $model->getId(),
            'isDeleted' => $model->isIsDeleted(),
            'manufacturer' => $model->getManufacturer(),
            'name' => $model->getName(),
            'type' => $model->getType(),
            'price' => $model->getPrice(),
            'description' => $model->getDescription(),
            'params' => $model->getParams(),
            'availableDevices' => count($model->getFreeDevices())
        ];
    }

    public function getModelDevices(int $modelId, Request $request): ?array
    {
        $model = $this->modelRepository->findOneBy(['id' => $modelId]);

        if (!$model) {
            return null;
        }

        $devices = $model->getDevices();

        if (count($devices) == 0) {
            return [];
        }

        $devicesArray = [];

        /** @var Device $device */
        foreach ($devices as $device) {
            $devicesArray[] = [
                'id' => $device->getId(),
                'mac' => $device->getMacAddress(),
                'serialNo' => $device->getSerialNumber(),
                'status' => $device->getStatus(),
                'userId' => $device->getUser() ? $device->getUser()->getId() : null,
                'boughtDate' => $device->getBoughtDate(),
                'soldDate' => $device->getSoldDate(),
            ];
        }

        return $devicesArray;
    }

    private function objectCreator(array $content, Model $model): Model
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Model::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if (method_exists($model, $setterMethod)) {
                    $model->$setterMethod($fieldValue);
                }
            }
        }

        return $model;
    }
}