<?php

namespace App\Controller\Public;

use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Payments")
 */
#[Route('/api/public/payments', name: 'api_public_payment_')]
class PaymentController extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getPayments(Request $request): JsonResponse
    {
        try {
            $customer  = $this->getUser();
            $payments = $this->paymentService->getCustomerPayments($customer->getUserIdentifier());

            return new JsonResponse(
                [
                    'status' => 'success',
                    'payments' => $payments
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