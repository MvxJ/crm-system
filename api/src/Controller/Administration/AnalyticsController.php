<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class AnalyticsController extends AbstractController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['message' => 'Dashboard index'], Response::HTTP_OK);
    }
}