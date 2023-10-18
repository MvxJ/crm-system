<?php

namespace App\Controller\Administration;

use App\Service\ServiceVisitService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/service/visit', name: 'api_service_visit_')]
class ServiceVisitController extends AbstractController
{
    private JsonValidator $validator;
    private ServiceVisitService $serviceVisitService;

    public function __construct(JsonValidator $validator, ServiceVisitService $serviceVisitService)
    {
        $this->validator = $validator;
        $this->serviceVisitService = $serviceVisitService;
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function createServiceVisit(Request $request): JsonResponse
    {
        try {
            $errors  = $this->validator->validateRequest('service-visit-schema.json');

            if (count($errors) > 0) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request.',
                        'errors' => $errors
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $serviceVisit = $this->serviceVisitService->addServiceVisit($request);

            if (!$serviceVisit) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceVisit' => $serviceVisit
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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editServiceVisit(int $id, Request $request): JsonResponse
    {
        try {
            $serviceVisit = $this->serviceVisitService->editServiceVisit($id, $request);

            if (!$serviceVisit) {
                return new JsonResponse(
                  [
                      'status' => 'error',
                      'message' => 'Bad request please try again later.'
                  ],
                  Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceVisit' => $serviceVisit
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

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getServiceVisits(Request $request): JsonResponse
    {
        try {
            $serviceVisits = $this->serviceVisitService->getServiceVisitList($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceVisits' => $serviceVisits
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
    public function getServiceVisitDetails(int $id, Request $request): JsonResponse
    {
        try {
            $serviceVisit = $this->serviceVisitService->getServiceVisitDetail($id);

            if (!$serviceVisit) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceVisit' => $serviceVisit
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

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteServiceVisit(int $id, Request $request): JsonResponse
    {
        try {
            $status = $this->serviceVisitService->deleteServiceVisit($id);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Service visit was deleted successfully'
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