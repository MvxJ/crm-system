<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Repository\UserRepository;
use App\Service\AuthenticationResponseService;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as BaseAuthenticationSuccessHandler;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private AuthenticationSuccessHandlerInterface $baseHandler;
    private AuthenticationResponseService $responseService;

    public function __construct(
        BaseAuthenticationSuccessHandler $baseHandler,
        AuthenticationResponseService $responseService
    ) {
        $this->baseHandler = $baseHandler;
        $this->responseService = $responseService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse|null|Response
    {
        if (!$token->getUser()->isVerified()) {
            return new JsonResponse(
                [
                    'message' => 'Account is not verified, please verify user email address.',
                    'error' => 'NOT_VERIFIED'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($token instanceof TwoFactorTokenInterface) {
            return new JsonResponse(
                [
                    'message' => 'Please complete two factor authentication',
                    'error' => 'REQUIRED_2FA'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $response = $this->baseHandler->onAuthenticationSuccess($request, $token);
        $responseContent = $this->responseService->addUserContent($response, $token);
        $response->setContent($responseContent);

        return $response;
    }
}