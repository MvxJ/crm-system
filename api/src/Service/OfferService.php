<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Entity\Offer;
use App\Repository\ModelRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class OfferService
{
    private EntityManagerInterface $entityManager;
    private OfferRepository $offerRepository;
    private ModelRepository $modelRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        OfferRepository $offerRepository,
        ModelRepository $modelRepository
    ) {
        $this->entityManager = $entityManager;
        $this->offerRepository = $offerRepository;
        $this->modelRepository = $modelRepository;
    }

    public function addOffer(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);
        $offer = new Offer();
        $offer = $this->objectCreator($content, $offer);

        if ($offer) {
            $this->entityManager->persist($offer);
            $this->entityManager->flush();

            return $this->createOfferArray($offer, true);
        }

        return null;
    }

    public function editOffer(Uuid $id, Request $request): ?array
    {
        $offer = $this->offerRepository->findOneBy(['id' => $id]);

        if (!$offer) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $offer = $this->objectCreator($content, $offer);

        if ($offer) {
            $this->entityManager->persist($offer);
            $this->entityManager->flush();

            return $this->createOfferArray($offer, true);
        }

        return null;
    }

    public function getOffers(Request $request): array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $type = $request->get('type', 'all');
        $searchTerm = $request->get('searchTerm', null);
        $totalItems = $this->offerRepository->countOffers($type, $searchTerm);
        $results = $this->offerRepository->getOffersWithPagination(
            (int)$itemsPerPage,
            (int)$page,
            $order,
            $orderBy,
            $type,
            $searchTerm
        );
        $offers = [];

        /** @var Offer $offer */
        foreach ($results as $offer) {
            $offers[] = $this->createOfferArray($offer, false);
        }

        return [
            'offers' => $offers,
            'maxResults' => $totalItems,
        ];
    }

    public function deleteOffer(Uuid $offerId): bool
    {
        $offer = $this->offerRepository->findOneBy(['id' => $offerId]);

        if (!$offer) {
            return false;
        }

        $offer->setDeleted(true);

        $this->entityManager->persist($offer);
        $this->entityManager->flush();

        return true;
    }

    public function getOffer(Uuid $id): ?array
    {
        $offer = $this->offerRepository->findOneBy(['id' => $id]);

        if (!$offer) {
            return null;
        }

        return $this->createOfferArray($offer, true);
    }

    public function removeOfferDevice(Uuid $offerId, Uuid $deviceId): ?array
    {
        $offer = $this->offerRepository->findOneBy(['id' => $offerId]);

        if (!$offer) {
            return null;
        }

        $device = $this->modelRepository->findOneBy(['id' => $deviceId]);

        if (!$device) {
            return null;
        }

        $offer->removeDevice($device);
        $this->entityManager->persist($offer);
        $this->entityManager->flush();

        return $this->createOfferArray($offer);
    }

    private function objectCreator(array $content, Offer $offer): ?Offer
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Offer::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($fieldName == 'devices') {
                    $models = [];

                    foreach ($fieldValue as $uuid) {
                        $models[] = $this->modelRepository->findOneBy(['id' => $uuid]);
                    }

                    /** @var Model $model */
                    foreach ($models as $model) {
                        $offer->addDevice($model);
                    }
                } elseif ($fieldName == 'validDue') {
                    $offer->setValidDue(new \DateTime($fieldValue));
                } elseif (method_exists($offer, $setterMethod)) {
                    $offer->$setterMethod($fieldValue);
                }
            }
        }

        return $offer;
    }

    private function createOfferArray(Offer $offer, bool $details = false): array
    {
        $offerArray = [
            'id' => $offer->getId(),
            'title' => $offer->getTitle(),
            'description' => $offer->getDescription(),
            'duration' => $offer->getDuration(),
            'downloadSpeed' => $offer->getDownloadSpeed(),
            'uploadSpeed' => $offer->getUploadSpeed(),
            'numberOfCanals' => $offer->getNumberOfCanals(),
            'type' => $offer->getType(),
            'price' => $offer->getPrice(),
            'discount' => $offer->getDiscount(),
            'discountType' => $offer->getDiscountType(),
            'forNewUsers' => $offer->isForNewUsers(),
            'forStudents' => $offer->isForStudents(),
            'validDue' => $offer->getValidDue(),
            'isDeleted' => $offer->isDeleted()
        ];

        if (count($offer->getDevices()) > 0 && $details = true) {
            $devices = $offer->getDevices();

            /** @var Model $device */
            foreach ($devices as $device) {
                $offerArray['devices'][] = [
                    'manufacturer' => $device->getManufacturer(),
                    'name' => $device->getName(),
                    'id' => $device->getId(),
                    'type' => $device->getType(),
                ];
            }
        }

        return $offerArray;
    }
}