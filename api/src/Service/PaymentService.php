<?php

namespace App\Service;

use App\Entity\Bill;
use App\Entity\Payment;
use App\Repository\BillRepository;
use App\Repository\CustomerRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentService
{
    private PaymentRepository $paymentRepository;
    private EntityManagerInterface $entityManager;
    private BillRepository $billRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        EntityManagerInterface $entityManager,
        BillRepository $billRepository,
        CustomerRepository $customerRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->entityManager = $entityManager;
        $this->billRepository = $billRepository;
        $this->customerRepository = $customerRepository;
    }

    public function addPayment(Request $request): ?array
    {
        $payment = new Payment();
        $content = json_decode($request->getContent(), true);
        $payment = $this->objectCreator($payment, $content);

        if (!$payment) {
            return null;
        }

        $payment->setCreatedAt(new \DateTime());

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->updateBillStatus($payment->getBill());

        return $this->getPaymentArray($payment);
    }

    public function editPayment(int $paymentId, Request $request): ?array
    {
        $payment = $this->paymentRepository->findOneBy(['id' => $paymentId]);

        if (!$payment) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $payment = $this->objectCreator($payment, $content);

        if (!$payment) {
            return null;
        }

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->updateBillStatus($payment->getBill());

        return $this->getPaymentArray($payment);
    }

    public function getPayment(int $paymentId): ?array
    {
        $payment = $this->paymentRepository->findOneBy(['id' => $paymentId]);

        if (!$payment) {
            return null;
        }

        return $this->getPaymentArray($payment);
    }

    public function getPaymentsList(Request $request): array
    {
        $paymentsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $status = $request->get('status', 'all');
        $paidBy = $request->get('paidBy', 'all');
        $customer = $request->get('customerId', 'all');
        $maxResults = $this->paymentRepository->countPayments($status, $paidBy, $customer);
        $payments = $this->paymentRepository->getPaymentsWithPagination(
            $page,
            $itemsPerPage,
            $order,
            $orderBy,
            $status,
            $paidBy,
            $customer
        );

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $paymentsArray[] = $this->getPaymentArray($payment);
        }

        return [
            'maxResults' => $maxResults,
            'payments' => $paymentsArray
        ];
    }

    public function getCustomerPayments(string $userEmail): ?array
    {
        $paymentsArray = [];
        $payments = $this->paymentRepository->findCustomerPayments($userEmail);

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $paymentsArray[] = $this->getPaymentArray($payment);
        }

        return $paymentsArray;
    }

    private function updateBillStatus(Bill $bill): void
    {
        $payments = $bill->getPayments();
        $totalAmount = $bill->getTotalAmount();
        $paidAmount = 0;

        foreach ($payments as $payment) {
            if ($payment->getStatus() == Payment::PAYMENT_STATUS_POSTED) {
                $paidAmount += $payment->getAmount();
            }
        }

        if ($paidAmount == $totalAmount) {
            $bill->setStatus(Bill::STATUS_PAID);
        }

        elseif ($paidAmount < $totalAmount && $paidAmount != 0) {
            $bill->setStatus(Bill::STATUS_PAID_PARTIALLY);
        }

        $this->entityManager->persist($bill);
        $this->entityManager->flush();
    }

    private function getPaymentArray(Payment $payment): array
    {
        return [
            'id' => $payment->getId(),
            'status' => $payment->getStatus(),
            'amount' => $payment->getAmount(),
            'paidBy' => $payment->getPaidBy(),
            'note' => $payment->getNote(),
            'createdAt' => $payment->getCreatedAt(),
            'customer' => [
                'id' => $payment->getCustomer()->getId(),
                'email' => $payment->getCustomer()->getEmail()
            ],
            'bill' => [
                'number' => $payment->getBill()->getNumber(),
                'id' => $payment->getBill()->getId()
            ]
        ];
    }

    private function objectCreator(Payment $payment, array $content): ?Payment
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Payment::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setCustomer') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $payment->setCustomer($customer);
                } elseif ($setterMethod == 'setBill') {
                    $bill = $this->billRepository->findOneBy(['id' => $fieldValue]);

                    if (!$bill) {
                        return null;
                    }

                    $payment->setBill($bill);
                } elseif (method_exists($payment, $setterMethod)) {
                    $payment->$setterMethod($fieldValue);
                }
            }
        }

        return $payment;
    }
}