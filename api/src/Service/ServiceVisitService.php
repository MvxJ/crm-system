<?php

namespace App\Service;

use App\Entity\ServiceVisit;
use App\Repository\ServiceVisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ServiceVisitService
{
    private ServiceVisitRepository $serviceVisitRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ServiceVisitRepository $serviceVisitRepository, EntityManagerInterface $entityManager)
    {
        $this->serviceVisitRepository = $serviceVisitRepository;
        $this->entityManager = $entityManager;
    }

    public function getServiceVisitList(Request $request): ?array
    {

    }

    public function getServiceVisitDetail(int $id, Request $request): ?array
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return null;
        }

        return $this->createServiceVisitArray($serviceVisit);
    }

    public function getCustomerServiceVisitList(Request $request, string $customerEmail): ?array
    {

    }

    public function getCustomerServiceVistiDetail(int $id, string $customerEmail): ?array
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit || $serviceVisit->getCustomer()->getEmail() != $customerEmail) {
            return null;
        }

        return $this->createServiceVisitArray($serviceVisit);
    }

    public function addServiceVisit(Request $request): ?array
    {

    }

    public function editServiceVisit(int $id, Request $request): ?array
    {

    }

    public function deleteServiceVisit(int $id): bool
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return false;
        }

        $this->entityManager->remove($serviceVisit);
        $this->entityManager->flush();

        return true;
    }

    public function cancelSErviceVisit(int $id): bool
    {
        $serviceVisit  = $this->serviceVisitRepository->findOneBy(['id' => $id]);

        if (!$serviceVisit) {
            return false;
        }

        $serviceVisit->setCancelled(true);

        $this->entityManager->persist($serviceVisit);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(ServiceVisit $serviceVisit, array $content): ?ServiceVisit
    {
        return $serviceVisit;
    }

    private function createServiceVisitArray(ServiceVisit $serviceVisit): array
    {
        $serviceVisitArray = [];

        return $serviceVisitArray;
    }
}