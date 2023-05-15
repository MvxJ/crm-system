<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use Scheb\TwoFactorBundle\Security\Http\Authentication\AuthenticationRequiredHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TwoFactorAuthenticationRequiredHandler implements AuthenticationRequiredHandlerInterface
{
    public function onAuthenticationRequired(Request $request, TokenInterface $token): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => 'TWo factor authentication required.',
                'error' => 'ACCESS_DENIED',
                '2fa_completed' => false
            ],
            Response::HTTP_OK
        );
    }
}