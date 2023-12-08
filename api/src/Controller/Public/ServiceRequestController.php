<?php

namespace App\Controller\Public;

use App\Service\ServiceRequestService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Uid\Uuid;

/**
 * @OA\Tag(name="Service Requests")
 */
#[Route('/api/public/service-requests', name: 'api_public_service_request_')]
class ServiceRequestController extends AbstractController
{
    private ServiceRequestService $serviceRequestService;
    private JsonValidator $validator;

    public function __construct(ServiceRequestService $serviceRequestService, JsonValidator $validator)
    {
        $this->serviceRequestService = $serviceRequestService;
        $this->validator = $validator;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getServiceRequests(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $serviceRequests = $this->serviceRequestService->getCustomerServiceRequests(
                $request,
                $user->getUserIdentifier()
            );

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $serviceRequests
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
    public function getServiceRequestDetails(Uuid $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $serviceRequest = $this->serviceRequestService->getCustomerServiceRequestDetails(
                $id,
                $user->getUserIdentifier()
            );

            if (!$serviceRequest) {
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

    #[Route('/{id}/cancel', name: 'delete', methods: ['DELETE'])]
    public function cancelServiceRequest(Uuid $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $status = $this->serviceRequestService->cancelServiceRequestByCustomer($id, $user->getUserIdentifier());

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later.'
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Successfully cancelled service request.'
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

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createServiceRequest(Request $request): JsonResponse
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

            $user = $this->getUser();
            $serviceRequest = $this->serviceRequestService->addServiceRequestByCustomer(
                $request,
                $user->getUserIdentifier()
            );

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
}