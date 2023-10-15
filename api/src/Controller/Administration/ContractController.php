<?php

namespace App\Controller\Administration;

use App\Service\ContractService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/contract', name: 'api_contract_')]
class ContractController extends AbstractController
{
    private ContractService $contractService;
    private JsonValidator $validator;

    public function __construct(ContractService $contractService, JsonValidator $validator)
    {
        $this->contractService = $contractService;
        $this->validator = $validator;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getContracts(Request  $request): JsonResponse
    {
        try {
            $contracts = $this->contractService->getContractList($request);

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

    #[Route('/{id}/details', name: 'details', methods: ['GET'])]
    public function getContractDetails(int $id, Request  $request): JsonResponse
    {
        try {
            $contract = $this->contractService->getContractDetails($id);

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

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addContract(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('contract-schema.json');

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

            $contract = $this->contractService->addContract($request);

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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editContract(int $id, Request $request): JsonResponse
    {
        try {
            $contract = $this->contractService->editContract($id, $request);

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

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteContract(int $id): JsonResponse
    {
        try {
            $success = $this->contractService->deleteContract($id);

            if (!$success) {
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
                    'message' => 'Contract deleted successfully.'
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