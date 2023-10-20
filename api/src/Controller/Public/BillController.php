<?php

namespace App\Controller\Public;

use App\Service\BillService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/public/bill', name: 'api_public_bill_')]
class BillController extends AbstractController
{
    private BillService $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getBills(Request $request): JsonResponse
    {
        try {
            $customer = $this->getUser();
            $bills = $this->billService->getCustomerBills($request, $customer->getUserIdentifier());

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
    public function getBillDetail(int $id, Request $request): JsonResponse
    {
        try {
            $customer = $this->getUser();
            $bill = $this->billService->getCustomerBillDetails($id, $customer->getUserIdentifier());

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
}