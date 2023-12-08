<?php

namespace App\Service;

use App\Entity\Bill;
use App\Entity\BillPosition;
use App\Repository\BillPositionRepository;
use App\Repository\BillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class BillPositionService
{
    private EntityManagerInterface $entityManager;
    private BillPositionRepository $positionRepository;
    private BillRepository $billRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BillPositionRepository $positionRepository,
        BillRepository $billRepository
    ) {
        $this->entityManager = $entityManager;
        $this->positionRepository = $positionRepository;
        $this->billRepository = $billRepository;
    }

    public function addPosition(Request $request): ?array
    {
        $position = new BillPosition();
        $content = json_decode($request->getContent(), true);
        $position = $this->objectCreator($position, $content);

        if (!$position) {
            return null;
        }

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        $billAmount = $this->calculateBillAmount($position->getBill());
        $bill = $position->getBill();

        $bill->setTotalAmount($billAmount);

        $this->entityManager->persist($bill);
        $this->entityManager->flush();

        return $this->createPositionArray($position);
    }

    public function editPosition(Uuid $positionId, Request $request): ?array
    {
        $position = $this->positionRepository->findOneBy(['id' => $positionId]);

        if (!$position) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $position = $this->objectCreator($position, $content);

        if (!$position) {
            return null;
        }

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        $billAmount = $this->calculateBillAmount($position->getBill());
        $bill = $position->getBill();

        $bill->setTotalAmount($billAmount);

        $this->entityManager->persist($bill);
        $this->entityManager->flush();

        return $this->createPositionArray($position);
    }

    public function deletePosition(Uuid $positionId): bool
    {
        $position = $this->positionRepository->findOneBy(['id' => $positionId]);

        if (!$position) {
            return false;
        }

        $bill = $position->getBill();
        $bill->removeBillPosition($position);
        $billAmount = $this->calculateBillAmount($position->getBill());

        $bill->setTotalAmount($billAmount);

        $this->entityManager->remove($position);
        $this->entityManager->flush();
        $this->entityManager->persist($bill);
        $this->entityManager->flush();

        return true;
    }

    public function getPositionListsByBill(Uuid $billId, Request $request): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $billId]);

        if (!$bill) {
            return null;
        }

        $positionsArray = [];
        $positions = $bill->getBillPositions();

        foreach ($positions as $position) {
            $positionsArray[] = $this->createPositionArray($position);
        }

        return $positionsArray;
    }

    private function objectCreator(BillPosition $position, array $content): ?BillPosition
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(BillPosition::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setBill') {
                    $bill = $this->billRepository->findOneBy(['id' => $fieldValue]);

                    if (!$bill) {
                        return null;
                    }

                    $position->setBill($bill);
                }

                elseif (method_exists($position, $setterMethod)) {
                    $position->$setterMethod($fieldValue);
                }
            }
        }

        return $position;
    }

    private function createPositionArray(BillPosition $position): array
    {
        return [
            'id' => $position->getId(),
            'price' => $position->getPrice(),
            'amount' => $position->getAmount(),
            'name' => $position->getName(),
            'description' => $position->getDescription(),
            'type' => $position->getType()
        ];
    }

    private function calculateBillAmount(Bill $bill): float
    {
        $amount = 0;

        /** @var BillPosition $position */
        foreach ($bill->getBillPositions() as $position) {
            $amount += ($position->getAmount() * $position->getPrice());
        }

        return $amount;
    }
}