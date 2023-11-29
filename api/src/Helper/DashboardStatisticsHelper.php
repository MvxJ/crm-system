<?php

declare(strict_types=1);

namespace App\Helper;

use App\Repository\BillRepository;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\CustomerSettingsRepository;
use App\Repository\DeviceRepository;
use App\Repository\MessageRepository;
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

    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        ContractRepository $contractRepository,
        ServiceRequestRepository $serviceRequestRepository,
        MessageRepository $messageRepository,
        CustomerSettingsRepository $customerSettingsRepository,
        DeviceRepository $deviceRepository,
        BillRepository $billRepository,
        PaymentRepository $paymentRepository
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
    }

    public function getAdminStatistics(): array
    {
        return [
            'usersCount' => 0,
            'customersCount' => 0,
            'activeContractsCount' => 0,
            'serviceRequestCount' => 0,
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
            'serviceRequestCount' => 0,
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
            'activeContractsCount' => 0,
            'dayIncome' => 0,
            'offersCount' => 0,
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
            'activeContractsCount' => 0,
            'customersCount' => 0,
            'offersCount' => 0,
        ];
    }
}