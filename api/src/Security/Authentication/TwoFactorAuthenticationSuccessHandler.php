<?php

namespace App\Security\Authentication;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as BaseAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class TwoFactorAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private AuthenticationSuccessHandlerInterface $baseHandler;
    private UserRepository $userRepository;

    public function __construct(BaseAuthenticationSuccessHandler $baseHandler, UserRepository $userRepository)
    {
        $this->baseHandler = $baseHandler;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $response = $this->baseHandler->onAuthenticationSuccess($request, $token);
        $responseContent = json_decode($response->getContent(), true);
        $userName = $token->getUser()->getUserIdentifier();
        $user = $this->userRepository->findOneBy(['username' => $userName]);

        $responseContent['user'] = [
            'username' => $userName,
            'profile' => $user->getProfile(),
            'email' => $user->getEmail()
        ];

        $response->setContent(json_encode($responseContent));

        return $response;
    }
}