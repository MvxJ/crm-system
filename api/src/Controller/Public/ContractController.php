<?php

namespace App\Controller\Public;

use App\Service\ContractService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/public/contracts', name: 'api_public_contract_')]
class ContractController extends AbstractController
{
    private ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    #[Route('/list', name: 'list')]
    public function getContracts(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $contracts = $this->contractService->getCustomerContracts($user->getUserIdentifier());

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $contracts
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

    #[Route('/{id}/details', name: 'details')]
    public function getContractDetails(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $contract = $this->contractService->getCustomerContractDetails($id, $user->getUserIdentifier());

            if (!$contract) {
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
                    'contract' => $contract
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