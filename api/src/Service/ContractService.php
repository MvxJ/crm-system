<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Offer;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ContractService
{
    private ContractRepository $contractRepository;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;
    private OfferRepository $offerRepository;

    public function __construct(
        ContractRepository $contractRepository,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        OfferRepository $offerRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->offerRepository = $offerRepository;
    }

    public function getContractList(Request $request): ?array
    {
        $contractsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $status = $request->get('status', 'all');
        $customerId = $request->get('customerId', 'all');
        $contracts = $this->contractRepository->findContractsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $status,
            $customerId
        );
        $maxResults = $this->contractRepository->countContracts($status, $customerId);

        if (count($contracts) == 0) {
            return null;
        }

        /** @var Contract $contract */
        foreach ($contracts as $contract) {
            $contractsArray[] = $this->createContractArray($contract, false);
        }

        return [
            'contracts' => $contractsArray,
            'maxResults' => $maxResults
        ];
    }

    public function getContractDetails(int $contractId): ?array
    {
        $contract = $this->contractRepository->findOneBy(['id' => $contractId]);

        if (!$contract) {
            return null;
        }

        return $this->createContractArray($contract, true);
    }

    public function getCustomerContracts(string $userEmail): ?array
    {
        $contractsArray = [];
        $contracts = $this->contractRepository->getUserContracts($userEmail);

        if (count($contracts) == 0) {
            return null;
        }

        foreach ($contracts as $contract) {
            $contractsArray[] = $this->createContractArray($contract, false);
        }

        return $contractsArray;
    }

    public function getCustomerContractDetails(int $contractId, string $userEmail): ?array
    {
        $contract = $this->contractRepository->findOneBy(['id' => $contractId]);

        if (!$contract || $contract->getUser()->getEmail() != $userEmail) {
            return null;
        }

        return $this->createContractArray($contract, true);
    }

    public function addContract(Request $request): ?array
    {
        $contract = new Contract();
        $content = json_decode($request->getContent(), true);
        $contract = $this->contractCreator($contract, $content);

        if (!$contract) {
            return null;
        }

        $contract->setCreateDate(new \DateTime());
        $contract->setNumber($this->generateContractNumber($contract));
        $contract->setStatus(Contract::CONTRACT_STATUS_AWAITING_ACTIVATION);

        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $this->createContractArray($contract, true);
    }

    public function editContract(int $contractId, Request $request): ?array
    {
        $contract = $this->contractRepository->findOneBy(['id' => $contractId]);

        if (!$contract) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $contract= $this->contractCreator($contract, $content);

        if (!$contract) {
            return null;
        }

        $contract->setUpdatedDate(new \DateTime());

        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $this->createContractArray($contract, true);
    }

    public function deleteContract(int $contractId): bool
    {
        $contract = $this->contractRepository->findOneBy(['id' => $contractId]);

        if (!$contract) {
            return false;
        }

        $contract->setStatus(Contract::CONTRACT_STATUS_CLOSED);
        
        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return true;
    }

    private function contractCreator(Contract $contract, array $content): ?Contract
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Contract::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setUser') {
                    $customer = $this->customerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$customer) {
                        return null;
                    }

                    $contract->setUser($customer);
                }

                elseif ($setterMethod == 'setOffer') {
                    $offer = $this->offerRepository->findOneBy(['id' => $fieldValue]);

                    if (!$offer) {
                        return null;
                    }

                    $contract->setOffer($offer);
                    $contract->setPrice($offer->getPrice());
                }

                elseif ($setterMethod == 'setStartDate') {
                    $date = new \DateTime($fieldValue);
                    $contract->setStartDate($date);
                }

                elseif (method_exists($contract, $setterMethod)) {
                    $contract->$setterMethod($fieldValue);
                }
            }
        }

        $totalSum = $contract->getPrice();

        if (array_key_exists('discount', $content) || array_key_exists('discountType', $content)) {
           switch ($contract->getDiscountType()) {
               case Contract::DISCOUNT_TYPE_ABSOLUTE:
                   $totalSum -= $contract->getDiscount();
                   break;
               case Contract::DISCOUNT_TYPE_PERCENTAGE:
                   $totalSum -= (float)(($totalSum * $contract->getDiscount())/100);
                   break;
               default:
                   $totalSum = $contract->getPrice();
           }
        }

        $contract->setTotalSum((float)$totalSum);

        return $contract;
    }

    private function createContractArray(Contract $contract, bool $details = false): array
    {
        $contractArray = [
            'id' => $contract->getId(),
            'contractNumber' => $contract->getNumber(),
            'customer' => [
                'id' => $contract->getUser()->getId(),
                'email' => $contract->getUser()->getEmail(),
                'name' => $contract->getUser()->getFirstName(),
                'surname' => $contract->getUser()->getLastName()
            ],
            'status' => $contract->getStatus(),
            'startDate' => $contract->getStartDate(),
            'totalSum' => $contract->getTotalSum(),
            'city' => $contract->getCity(),
            'zipCode' => $contract->getZipCode(),
            'address' => $contract->getAddress(),
            'createdAt' => $contract->getCreateDate(),
            'updatedAt' => $contract->getUpdatedDate()
        ];

        if ($details) {
            $contractArray['price'] = $contract->getPrice();
            $contractArray['discount'] = $contract->getDiscount();
            $contractArray['description'] = $contract->getDescription();
            $contractArray['discountType'] = $contract->getDiscountType();
            $contractArray['offer'] = [
                'id' => $contract->getOffer()->getId(),
                'name' => $contract->getOffer()->getTitle(),
                'period' => $contract->getOffer()->getDuration()
            ];
        }

        return $contractArray;
    }

    private function generateContractNumber(Contract $contract): string
    {
        $contractNumber = '';
        $date = new \DateTime();
        $numberOfContracts = $this->contractRepository->count(['createDate' => $date]);

        switch ($contract->getOffer()->getType()) {
            case Offer::TYPE_INTERNET:
                $contractNumber .= 'NET/';
                break;
            case Offer::TYPE_TELEVISION:
                $contractNumber .= 'TEL/';
                break;
            case Offer::TYPE_INTERNET_AND_TELEVISION:
                $contractNumber .= 'NET-TEL/';
                break;
        }

        $contractNumber .= $date->format('Y-m-d') . '/';
        $contractNumber .= (string)($numberOfContracts + 1);

        return $contractNumber;
    }
}