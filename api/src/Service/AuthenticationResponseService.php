<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationResponseService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function addUserContent(Response $response, TokenInterface $token): string
    {
        $responseContent = json_decode($response->getContent(), true);
        $userName = $token->getUser()->getUserIdentifier();
        $user = $this->userRepository->findOneBy(['username' => $userName]);

        $responseContent['user'] = [
            'username' => $userName,
            'profile' => $user->getProfile(),
            'email' => $user->getEmail()
        ];

        return json_encode($responseContent);
    }
}