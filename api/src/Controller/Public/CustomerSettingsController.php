<?php

namespace App\Controller\Public;

use App\Service\CustomerSettingsService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/public/customers/settings", name: "api_public_customers_settings_")]
class CustomerSettingsController extends AbstractController
{
    private CustomerSettingsService $customerSettingsService;
    private JsonValidator $validator;

    public function __construct(CustomerSettingsService $customerSettingsService, JsonValidator $validator)
    {
        $this->customerSettingsService = $customerSettingsService;
        $this->validator = $validator;
    }

    #[Route("/detail", name: "detail", methods: ['GET'])]
    public function getCustomerSettings(int $customerId, Request $request): JsonResponse
    {
        try {
            $customer = $this->getUser();
            $settings = $this->customerSettingsService->getCustomerSettings(
                $customerId,
                $customer->getUserIdentifier()
            );

            if (!$settings) {
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
                    'settings' => $settings
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

    #[Route("/{customerId}/edit", name: "edit", methods: ['PUT'])]
    public function editSettings(int $customerId, Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('customer-settings-schema.json');

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

            $customer = $this->getUser();
            $settings = $this->customerSettingsService->editSettings(
                $customerId,
                $request,
                $customer->getUserIdentifier()
            );

            if (!$settings) {
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
                    'settings' => $settings
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