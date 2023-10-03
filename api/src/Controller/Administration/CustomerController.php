<?php

namespace App\Controller\Administration;

use App\Entity\Customer;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/customers", name: "api_customers_")]
class CustomerController extends AbstractController
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    #[Route("/add", name: "add", methods: ["POST"])]
    public function addCustomer(Request $request): JsonResponse
    {
        try {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'customerId' => $this->customerService->addCustomer($request)
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

    #[Route("/edit/{customer}", name: "edit", methods: ["POST", "PUT", "PATCH"])]
    public function editCustomer(Customer $customer, Request $request): JsonResponse
    {
        if (!$customer) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Customer not found'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->customerService->editCustomer($customer, $request);

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

    #[Route("/delete/{customer}", name: "delete", methods: ["DELETE"])]
    public function deleteCustomer(Customer $customer): JsonResponse
    {
        try {
            if (!$customer) {
                return new JsonResponse(
                    [
                        'status' => 'success',
                        'message' => 'Customer not found'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->customerService->deleteCustomer($customer);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Customer deleted successfully.'
                ]
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

    #[Route("/detail/{customer}", name: "detail", methods: ["GET"])]
    public function getCustomerDetail(Customer $customer): JsonResponse
    {
        if ($customer) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'customer' => [
                        'id' => $customer->getId(),
                        'email' => $customer->getEmail(),
                        'firstName' => $customer->getProfile() ? $customer->getProfile()->getFirstName() : '',
                        'secondName' => $customer->getProfile() ? $customer->getProfile()->getSecondName() : '',
                        'surname' => $customer->getProfile() ? $customer->getProfile()->getSurname() : '',
                        'phoneNumber' => $customer->getProfile() ? $customer->getProfile()->getPhoneNumber() : '',
                        'socialSecurityNumber' => $customer->getProfile() ? $customer->getProfile()->getSocialSecurityNumber() : '',
                        'is_verified' => $customer->isVerified(),
                        'email_auth' => $customer->isEmailAuthEnabled(),
                        'roles' => $customer->getRoles(),
                    ]
                ],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            [
                'status' => 'success',
                'message' => 'Customer not found'
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route("/list", name: "list", methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->customerService->getCustomers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'items' => $response['customers'],
                    'page' => $response['page'],
                    'limit' => $response['limit']
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