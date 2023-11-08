<?php

namespace App\Service;

use App\Entity\Bill;
use App\Entity\BillPosition;
use App\Helper\BillHelper;
use App\Repository\BillRepository;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BillService
{
    private BillRepository $billRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private ContractRepository $contractRepository;
    private BillHelper $billHelper;

    public function __construct(
        BillRepository $billRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        ContractRepository $contractRepository,
        BillHelper $billHelper
    ) {
        $this->billRepository = $billRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->contractRepository = $contractRepository;
        $this->billHelper = $billHelper;
    }

    public function getBills(Request $request): ?array
    {
        $billsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $customerId = $request->get('customerId', 'all');
        $status = $request->get('status', 'all');
        $bills = $this->billRepository->findBillsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $status,
            $customerId
        );
        $maxResults = $this->billRepository->countBills($status);

        if (count($bills) == 0) {
            return null;
        }

        /** @var Bill $bill */
        foreach ($bills as $bill) {
            $billsArray[] = $this->createBillArray($bill, false);
        }

        return [
            'bills' => $billsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getBillDetails(int $id): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill) {
            return null;
        }

        return $this->createBillArray($bill, true);
    }

    public function getCustomerBillDetails(int $id, string $customerEmail): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill || $bill->getCustomer()->getEmail() != $customerEmail) {
            return null;
        }

        return $this->createBillArray($bill, true);
    }

    public function getCustomerBills(Request $request, string $customerEmail): ?array
    {
        $billsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $bills = $this->billRepository->findCustomerBillsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $customerEmail
        );
        $maxResults = $this->billRepository->countCustomerBills($customerEmail);

        if (count($bills) == 0) {
            return null;
        }

        /** @var Bill $bill */
        foreach ($bills as $bill) {
            $billsArray[] = $this->createBillArray($bill, false);
        }

        return [
            'bills' => $billsArray,
            'maxResults' => $maxResults
        ];
    }

    public function editBill(int $id, Request $request): ?array
    {
        $bill = $this->billRepository->findOneBy(['id' => $id]);

        if (!$bill) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $bill = $this->objectCreator($bill, $content);

        if (!$bill) {
            return null;
        }

        $bill->setUpdateDate(new \DateTime());
        $bill->setTotalAmount($this->calculateBillAmount($bill));

        $this->entityManager->persist($bill);
        $this->entityManager->flush();

        return $this->createBillArray($bill, true);
    }

    public function addBill(Request $request): ?array
    {
        $bill = new Bill();
        $content = json_decode($request->getContent(), true);
        $bill = $this->objectCreator($bill, $content);

        if (!$bill) {
            return null;
        }

        $bill->setDateOfIssue(new \DateTime());
        $bill->setNumber($this->generateBillNumber($bill));
        $bill->setFileName('');
        $bill->setTotalAmount($this->calculateBillAmount($bill));

        $this->entityManager->persist($bill);
        $this->entityManager->flush();

        return $this->createBillArray($bill, true);
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
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Bill::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $bill->setCustomer($customer);
                }

                elseif ($setterMethod == 'setContract') {
                    $contract = $this->contractRepository->findOneBy(['id' => $fieldValue]);

                    if (!$contract) {
                        return null;
                    }

                    $bill->setContract($contract);
                }
                elseif ($setterMethod == 'setPayDue') {

                    $bill->setPayDue(new \DateTime($fieldValue));
                }

                elseif (method_exists($bill, $setterMethod)) {
                    $bill->$setterMethod($fieldValue);
                }
            }
        }

        return $bill;
    }

    public function generateBill(int $billId): bool
    {
        $bill = $this->billRepository->findOneBy(['id' => $billId]);

        if (!$bill) {
            return false;
        }

        $this->billHelper->generateBillPdf($bill);

        return true;
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
            'updatedAt' => $bill->getUpdateDate(),
            'fileName' => $bill->getFileName()
        ];

        if ($details) {
            $billArray['customer'] = [
                'id' => $bill->getCustomer()->getId(),
                'username' => $bill->getCustomer()->getEmail()
            ];

            if ($bill->getContract()) {
                $billArray['contract'] = [
                    'id' => $bill->getContract()->getId(),
                    'number' => $bill->getContract()->getNumber()
                ];
            }

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

    private function generateBillNumber(Bill $bill): string
    {
        $billNumber = 'bill';
        $date = new \DateTime();
        $numberOfBills = $this->billRepository->count(['dateOfIssue' => $date]);
        $contract = $bill->getContract();

        if ($bill->getContract()) {
            $billNumber .= "-" . $contract->getNumber() . "-FV-";
        }

        $billNumber .= $date->format('Y-m-d') . "-";
        $billNumber .= (string)($numberOfBills + 1);

        return $billNumber;
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