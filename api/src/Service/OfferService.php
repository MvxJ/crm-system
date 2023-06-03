<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class OfferService
{
    private EntityManagerInterface $entityManager;
    private OfferRepository $offerRepository;

    public function __construct(EntityManagerInterface $entityManager, OfferRepository $offerRepository)
    {
        $this->entityManager = $entityManager;
        $this->offerRepository = $offerRepository;
    }

    public function addOffer(Request $request): int
    {
        $content = json_decode($request->getContent(), true);
        $offer = new Offer();

        $offer->setTitle($content['title']);
        $offer->setDescription($content['description']);
        $offer->setPrice($content['price']);

        if (array_key_exists('download_speed', $content)) {
            $offer->setDownloadSpeed($content['download_speed']);
        }

        if (array_key_exists('upload_speed', $content)) {
            $offer->setUploadSpeed($content['upload_speed']);
        }

        if (array_key_exists('new_users', $content)) {
            $offer->setForNewUsers($content['new_users']);
        }

        if (array_key_exists('discount', $content)) {
            $offer->setPercentageDiscount($content['discount']);
        }

        $this->entityManager->persist($offer);
        $this->entityManager->flush();

        return $offer->getId();
    }

    public function editOffer(Offer $offer, Request $request): void
    {
        $content = json_decode($request->getContent(), true);

        if (array_key_exists('title', $content)) {
            $offer->setTitle($content['title']);
        }

        if (array_key_exists('description', $content)) {
            $offer->setDescription($content['description']);
        }

        if (array_key_exists('price', $content)) {
            $offer->setPrice($content['price']);
        }

        if (array_key_exists('download_speed', $content)) {
            $offer->setDownloadSpeed($content['download_speed']);
        }

        if (array_key_exists('upload_speed', $content)) {
            $offer->setUploadSpeed($content['upload_speed']);
        }

        if (array_key_exists('new_users', $content)) {
            $offer->setForNewUsers($content['new_users']);
        }

        if (array_key_exists('discount', $content)) {
            $offer->setPercentageDiscount($content['discount']);
        }

        $this->entityManager->persist($offer);
        $this->entityManager->flush();
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
                'price' => $offer->getPrice()
            ];
        }

        return [
            'items' => $offers,
            'totalItems' => $totalItems,
            'page' => $page,
            'limit' => $itemsPerPage
        ];
    }

    public function deleteOffer(Offer $offer): void
    {
        $this->entityManager->remove($offer);
        $this->entityManager->flush();
    }
}