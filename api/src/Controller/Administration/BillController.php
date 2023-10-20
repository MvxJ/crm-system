<?php

namespace App\Controller\Administration;

use App\Service\BillService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bill', name: 'api_bill_')]
class BillController extends AbstractController
{
    private BillService $billService;
    private JsonValidator $validator;

    public function __construct(BillService $billService, JsonValidator $validator)
    {
        $this->billService = $billService;
        $this->validator = $validator;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getBills(Request $request): JsonResponse
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
    public function getBillDetail(int $id, Request $request): JsonResponse
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

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addBill(Request $request): JsonResponse
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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editBill(int $id, Request $request): JsonResponse
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

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteBill(int $id): JsonResponse
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