<?php

namespace App\Controller\Public;

use App\Service\ServiceVisitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/public/service/visit', name: 'api_public_service_visit_')]
class ServiceVisitController extends AbstractController
{
    private ServiceVisitService $serviceVisitService;

    public function __construct(ServiceVisitService $serviceVisitService)
    {
        $this->serviceVisitService = $serviceVisitService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getCustomerServiceVisits(Request $request): JsonResponse
    {
        try {
            return new JsonResponse(
                [
                    'status' => 'success'
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

    #[Route('/{id}/detail', name: 'detail', methods: ['GET'])]
    public function getCustomerServiceVisitDetails(int $id, Request $request): JsonResponse
    {
        try {
            return new JsonResponse(
                [
                    'status' => 'success'
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

    #[Route('/{id}/cancel', name: 'cancel', methods: ['DELETE'])]
    public function cancalServiceVisit(int $id, Request $request): JsonResponse
    {
        try {
            return new JsonResponse(
                [
                    'status' => 'success'
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