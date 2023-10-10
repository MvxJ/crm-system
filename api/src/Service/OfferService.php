<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Offer;
use App\Repository\ModelRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

            return $this->createOfferArray($offer);
        }

        return null;
    }

    public function editOffer(int $id, Request $request): ?array
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

            return $this->createOfferArray($offer);
        }

        return null;
    }

    public function getOffers(Request $request): array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $totalItems = count($this->offerRepository->findAll());
        $results = $this->offerRepository->getOffersWithPagination((int)$itemsPerPage, (int)$page);
        $offers = [];

        /** @var Offer $offer */
        foreach ($results as $offer) {
            $offers[] = [
                'id' => $offer->getId(),
                'title' => $offer->getTitle(),
                'description' => $offer->getDescription(),
                'price' => $offer->getPrice(),
                'type' => $offer->getType(),
                'forNewUsers' => $offer->isForNewUsers(),
                'forStudents' => $offer->isForStudents(),
                'validDue' => $offer->getValidDue(),
                'discount' => $offer->getDiscount(),
                'discountType' => $offer->getDiscountType()
            ];
        }

        return [
            'offers' => $offers,
            'maxResults' => $totalItems,
        ];
    }

    public function deleteOffer(Offer $offer): void
    {
        $this->entityManager->remove($offer);
        $this->entityManager->flush();
    }

    public function getOffer(int $id): ?array
    {
        $offer = $this->offerRepository->findOneBy(['id' => $id]);

        if (!$offer) {
            return null;
        }

        return $this->createOfferArray($offer);
    }

    private function objectCreator(array $content, Offer $offer): ?Offer
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Offer::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($fieldName == 'devices') {
                    $models = $this->modelRepository->findAllBy(['id' => $fieldValue]);
                } elseif (method_exists($offer, $setterMethod)) {
                    $offer->$setterMethod($fieldValue);
                }
            }
        }

        return $offer;
    }

    private function createOfferArray(Offer $offer): array
    {
        return [
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
            'validDue' => $offer->getValidDue()
        ];
    }
}