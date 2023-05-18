<?php

namespace App\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    #[Route("/list", name: "list")]
    public function index(): JsonResponse
    {
        return new JsonResponse(['message' => 'Users list'], Response::HTTP_OK);
    }
}