<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationResponseService
{
    private UserRepository $userRepository;

    private CustomerRepository $customerRepository;

    public function __construct(UserRepository $userRepository, CustomerRepository $customerRepository)
    {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    public function addUserContent(Response $response, TokenInterface $token): string
    {
        $responseContent = json_decode($response->getContent(), true);
        $login = $token->getUser()->getUserIdentifier();

        if ($token->getUser() instanceof User) {
            $user = $this->userRepository->findOneBy(['username' => $login]);
        } else if ($token->getUser() instanceof Customer) {
            $user = $this->customerRepository->findOneBy(['email' => $login]);
        }

        $responseContent['user'] = [
            'username' => $login,
            'email' => $user->getEmail()
        ];

        return json_encode($responseContent);
    }
}