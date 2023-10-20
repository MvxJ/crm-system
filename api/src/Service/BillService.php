<?php

namespace App\Service;

use App\Entity\Bill;
use App\Entity\BillPosition;
use App\Repository\BillRepository;
use Doctrine\ORM\EntityManagerInterface;

class BillService
{
    private BillRepository $billRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(BillRepository $billRepository, EntityManagerInterface $entityManager)
    {
        $this->billRepository = $billRepository;
        $this->entityManager = $entityManager;
    }

    public function getBills(): ?array
    {

    }

    public function getBillDetails(int $id): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill) {
            return null;
        }

        return $this->createBillArray($bill);
    }

    public function getCustomerBillDetails(int $id, string $customerEmail): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill || !$bill->getCustomer()->getEmail() != $customerEmail) {
            return null;
        }

        return $this->createBillArray($bill);
    }

    public function getCustomerBills(): ?array
    {

    }

    public function editBill(): ?array
    {

    }

    public function addBill(): ?array
    {

    }

    public function deleteBill(int $id): bool
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill) {
            return false;
        }

        $this->entityManager->remove($bill);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(Bill $bill, array $content): ?Bill
    {
        return $bill;
    }

    private function createBillArray(Bill $bill, bool $details = false): array
    {
        $billArray = [
            'id' => $bill->getId(),
            'number' => $bill->getNumber(),
            'status' => $bill->getStatus(),
            'amount' => $bill->getTotalAmount(),
            'dateOfIssue' => $bill->getDateOfIssue(),
            'paymentDate' => $bill->getPaymentDate(),
            'payDue' => $bill->getPayDue(),
            'updatedAt' => $bill->getUpdateDate()
        ];

        if ($details) {
            $billArray['customer'] = [
                'id' => $bill->getCustomer()->getId(),
                'username' => $bill->getCustomer()->getEmail()
            ];
            $billArray['contract'] = [
                'id' => $bill->getContract()->getId(),
                'number' => $bill->getContract()->getNumber()
            ];

            $positions = $bill->getBillPositions();
            $billPositions = [];

            /** @var BillPosition $position */
            foreach ($positions as $position) {
                $billPositions[] = [
                    'id' => $position->getId(),
                    'name' => $position->getName(),
                    'amount' => $position->getAmount(),
                    'price' => $position->getPrice(),
                    'description' => $position->getDescription(),
                    'type' => $position->getType(),
                ];
            }

            $billArray['positions'] = $billPositions;
        }

        return $billArray;
    }
}