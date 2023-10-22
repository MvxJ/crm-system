<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Role;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerService
{
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $entityManager;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private MailerService $mailerService;

    public function __construct(
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        MailerService $mailerService
    ) {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->mailerService = $mailerService;
    }

    public function getCustomers(Request $request): array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $totalItems = count($this->customerRepository->findAll());
        $results = $this->customerRepository->getCustomersWithPagination((int)$itemsPerPage, (int)$page);
        $customers = [];

        /** @var Customer $customer */
        foreach ($results as $customer) {
            $customers[] = [
                'id' => $customer->getId(),
                'email' => $customer->getEmail(),
                'name' => $customer->getFirstName(),
                'surname' => $customer->getSecondName()
            ];
        }

        return [
            'customers' => $customers,
            'totalItems' => $totalItems,
            'page' => $page,
            'limit' => $itemsPerPage
        ];
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    public function addCustomer(Request $request): int
    {
        $roleCustomer = $this->roleRepository->findOneBy(['role' => Role::ROLE_CUSTOMER]);
        $content = json_decode($request->getContent(), true);
        $customer = new Customer();

        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $content['password']));
        $customer->setEmail($content['email']);
        $customer->addRole($roleCustomer);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $this->mailerService->sendConfirmationEmail(
            'api_register_confirm',
            $customer
        );

        return $customer->getId();
    }

    public function editCustomer(Customer $customer, Request $request): void
    {
        $content = json_decode($request->getContent(), true);

        if (array_key_exists('email', $content)) {
            $customer->setEmail($content['email']);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }
}