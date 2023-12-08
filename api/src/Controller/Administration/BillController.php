<?php

namespace App\Controller\Administration;

use App\Service\BillService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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
            $bills = $this->billService->getBills($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $bills
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
    public function getBillDetail(Uuid $id, Request $request): JsonResponse
    {
        try {
            $bill = $this->billService->getBillDetails($id);

            if (!$bill) {
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
                    'bill' => $bill
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
            $errors = $this->validator->validateRequest('bill-schema.json');

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

            $bill = $this->billService->addBill($request);

            if (!$bill) {
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
                    'bill' => $bill
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
    public function editBill(Uuid $id, Request $request): JsonResponse
    {
        try {
            $bill = $this->billService->editBill($id, $request);

            if (!$bill) {
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
                    'bill' => $bill
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
    public function deleteBill(Uuid $id): JsonResponse
    {
        try {
            $status  = $this->billService->deleteBill($id);

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
                    'message' => 'Bill was deleted successfully.'
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

    #[Route('/{id}/generatePdfFile', name: 'generate_pdf_file', methods: ['POST'])]
    public function generatePdfFile(Uuid $id): JsonResponse
    {
        try {
            $status = $this->billService->generateBill($id);

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
                    'message' => 'Successfully generated bill file and notified customer.'
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