<?php

namespace App\Controller\Administration;

use App\Entity\Customer;
use App\Service\CustomerService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/customers", name: "api_customers_")]
class CustomerController extends AbstractController
{
    private JsonValidator $validator;
    private CustomerService $customerService;

    public function __construct(JsonValidator $validator, CustomerService $customerService)
    {
        $this->validator = $validator;
        $this->customerService = $customerService;
    }

    #[Route("/add", name: "add", methods: ["POST"])]
    public function addCustomer(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('customer-schema.json');

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

            $customer  = $this->customerService->addCustomer($request);

            if (!$customer) {
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
                    'customer' => $customer
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

    #[Route("/{customerId}/edit", name: "edit", methods: ["PATCH"])]
    public function editCustomer(int $customerId, Request $request): JsonResponse
    {
        try {
            $customer  = $this->customerService->editCustomer($customerId, $request);

            if (!$customer) {
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
                  'customer' => $customer
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

    #[Route("/{customerId}/delete", name: "delete", methods: ["DELETE"])]
    public function deleteCustomer(int $customerId): JsonResponse
    {
        try {
            $status = $this->customerService->deleteCustomer($customerId);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Customer not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Customer deleted successfully.'
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

    #[Route("/{customerId}/detail", name: "detail", methods: ["GET"])]
    public function getCustomerDetail(int $customerId): JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomer($customerId);

            if (!$customer) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Customer not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'customer' => $customer
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

    #[Route("/list", name: "list", methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $customers = $this->customerService->getCustomers($request);

            return new JsonResponse(
              [
                  'status' => 'success',
                  'results' => $customers
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