<?php

namespace App\Controller\Administration;

use App\Service\BillPositionService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/bill/position', name: 'api_bill_position_')]
class BillPositionController extends AbstractController
{
    private BillPositionService $billPositionService;
    private JsonValidator $validator;

    public function __construct(BillPositionService $billPositionService, JsonValidator $validator)
    {
        $this->billPositionService = $billPositionService;
        $this->validator = $validator;
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addPosition(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('bill-position-schema.json');

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

            $position = $this->billPositionService->addPosition($request);

            if (!$position) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'position' => $position
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editPosition(Uuid $id, Request $request): JsonResponse
    {
        try {
            $position = $this->billPositionService->editPosition($id, $request);


            if (!$position) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'position' => $position
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deletePosition(Uuid $id): JsonResponse
    {
        try {
            $status = $this->billPositionService->deletePosition($id);

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
                    'message' => 'Bill position was deleted successfully.'
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{billId}/list', name: 'list', methods: ['GET'])]
    public function getPositions(Uuid $billId, Request $request): JsonResponse
    {
        try {
            $positions = $this->billPositionService->getPositionListsByBill($billId, $request);

            if (!$positions) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'positions' => $positions
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}