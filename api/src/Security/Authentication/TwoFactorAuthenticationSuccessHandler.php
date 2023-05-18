<?php

namespace App\Security\Authentication;

use App\Repository\UserRepository;
use App\Service\AuthenticationResponseService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as BaseAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class TwoFactorAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private AuthenticationSuccessHandlerInterface $baseHandler;
    private AuthenticationResponseService $responseService;

    public function __construct(BaseAuthenticationSuccessHandler $baseHandler, AuthenticationResponseService $responseService)
    {
        $this->baseHandler = $baseHandler;
        $this->responseService = $responseService;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $response = $this->baseHandler->onAuthenticationSuccess($request, $token);
        $responseContent = $this->responseService->addUserContent($response, $token);

        $response->setContent($responseContent);

        return $response;
    }
}