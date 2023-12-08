<?php

namespace App\Controller\Administration;

use App\Service\PaymentService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/payments', name: 'api_payment_')]
class PaymentController extends AbstractController
{
    private PaymentService $paymentService;
    private JsonValidator $validator;

    public function __construct(PaymentService $paymentService, JsonValidator $validator)
    {
        $this->paymentService = $paymentService;
        $this->validator = $validator;
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addPayment(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('payment-schema.json');

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

            $payment = $this->paymentService->addPayment($request);

            if (!$payment) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'payment' => $payment
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
    public function editPayment(Uuid $id, Request $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->editPayment($id, $request);

            if (!$payment) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'payment' => $payment
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
    public function getPayments(Request $request): JsonResponse
    {
        try {
            $payments = $this->paymentService->getPaymentsList($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $payments
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
    public function getPayment(Uuid $id, Request $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);

            if (!$payment) {
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
                    'payment' => $payment
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