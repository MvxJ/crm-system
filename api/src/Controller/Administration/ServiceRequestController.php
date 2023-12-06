<?php

namespace App\Controller\Administration;

use App\Service\ServiceRequestService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/service-requests', name: 'api_service_request_')]
class ServiceRequestController extends AbstractController
{
    private JsonValidator $validator;
    private ServiceRequestService $serviceRequestService;

    public function __construct(JsonValidator $validator, ServiceRequestService $serviceRequestService)
    {
        $this->validator = $validator;
        $this->serviceRequestService = $serviceRequestService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getServiceRequests(Request $request): JsonResponse
    {
        try {
            $results = $this->serviceRequestService->getServiceRequests($request);

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

    #[Route('/{id}/detail', name: 'detail', methods: ['GET'])]
    public function getServiceRequestDetails($id, Request $request): JsonResponse
    {
        try {
            $serviceRequest = $this->serviceRequestService->getServiceRequestDetails($id);

            if (!$serviceRequest) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Service request not found. Try again.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceRequest' => $serviceRequest
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

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addServiceRequest(Request $request): JsonResponse
    {
        try {
            $errors  = $this->validator->validateRequest('service-request-schema.json');

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

            $serviceRequest  = $this->serviceRequestService->addServiceRequestByAdmin($request);

            if (!$serviceRequest) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'Bad request please try again. Check your payload.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceRequest' => $serviceRequest
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
    public function editServiceRequest(int $id, Request $request): JsonResponse
    {
        try {
            $serviceRequest  = $this->serviceRequestService->editServiceRequest($id, $request);

            if (!$serviceRequest) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request, Please try again later. Check you payload.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'serviceRequest' => $serviceRequest
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
    public function deleteServiceRequest(int $id): JsonResponse
    {
        try {
            $status = $this->serviceRequestService->deleteServiceRequest($id);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request. Please try again later.'
                    ]
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Service request was deleted.'
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