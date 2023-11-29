<?php

declare(strict_types=1);

namespace App\Helper;

use App\Repository\BillRepository;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\CustomerSettingsRepository;
use App\Repository\DeviceRepository;
use App\Repository\MessageRepository;
use App\Repository\OfferRepository;
use App\Repository\PaymentRepository;
use App\Repository\ServiceRequestRepository;
use App\Repository\UserRepository;

class DashboardStatisticsHelper
{
    private UserRepository $userRepository;
    private CustomerRepository $customerRepository;
    private ContractRepository $contractRepository;
    private ServiceRequestRepository $serviceRequestRepository;
    private MessageRepository $messageRepository;
    private CustomerSettingsRepository $customerSettingsRepository;
    private DeviceRepository $deviceRepository;
    private BillRepository $billRepository;
    private PaymentRepository $paymentRepository;
    private OfferRepository $offerRepository;

    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        ContractRepository $contractRepository,
        ServiceRequestRepository $serviceRequestRepository,
        MessageRepository $messageRepository,
        CustomerSettingsRepository $customerSettingsRepository,
        DeviceRepository $deviceRepository,
        BillRepository $billRepository,
        PaymentRepository $paymentRepository,
        OfferRepository $offerRepository
    ) {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->contractRepository = $contractRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->messageRepository = $messageRepository;
        $this->customerSettingsRepository = $customerSettingsRepository;
        $this->deviceRepository = $deviceRepository;
        $this->billRepository = $billRepository;
        $this->paymentRepository = $paymentRepository;
        $this->offerRepository = $offerRepository;
    }

    public function getAdminStatistics(): array
    {
        return [
            'usersCount' => $this->userRepository->countActiveUsers(),
            'customersCount' => $this->customerRepository->countActiveCustomers(),
            'activeContractsCount' => $this->contractRepository->countActiveContracts(),
            'serviceRequestCount' => $this->serviceRequestRepository->countNotFinishedServiceRequests(),
            'messagesStatistics' => [],
            'customer2faStatistics' => [],
            'customerNotificationStatistics' => [],
            'customerConfirmedAccountStatistics' => [],
            'contractsStatistics' => []
        ];
    }

    public function getTechnicianStatistics(): array
    {
        return [
            'serviceRequestCount' => $this->serviceRequestRepository->countNotFinishedServiceRequests(),
            'userVisits' => [],
            'serviceRequestByContractType' => [],
            'serviceTypesCount' => [],
            'devicesStatusStatistics' => [],
            'internetSpeedStatistics' => [],
            'televisionStatistics' => []
        ];
    }

    public function getStuffStatistics(): array
    {
        return [
            'activeContractsCount' => $this->contractRepository->countActiveContracts(),
            'dayIncome' => $this->paymentRepository->countDayIncome(),
            'offersCount' => $this->offerRepository->countActiveOffers(),
            'serviceTypesCount' => [],
            'billsStatistics' => [],
            'paymentsIncomeByDays' => [],
            'paymentsStatistics' => [],
            'contractsStatistics' => []
        ];
    }

    public function getPublicStatistics(): array
    {
        return [
            'activeContractsCount' => $this->contractRepository->countActiveContracts(),
            'customersCount' => $this->customerRepository->countActiveCustomers(),
            'offersCount' => $this->offerRepository->countActiveOffers(),
        ];
    }
}