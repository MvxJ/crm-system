<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class DashboardController extends AbstractController
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $results  = $this->dashboardService->getDashboardStatistics($this->getUser());

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $results
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}