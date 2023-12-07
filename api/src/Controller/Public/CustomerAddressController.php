<?php

namespace App\Controller\Public;

use App\Entity\CustomerAddress;
use App\Service\CustomerAddressService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Uid\Uuid;

/**
 * @OA\Tag(name="Addresses")
 */
#[Route("/api/public/customers/address", name: "api_public_customers_address_")]
class CustomerAddressController extends AbstractController
{
    private JsonValidator $validator;
    private CustomerAddressService $customerAddressService;

    public function __construct(JsonValidator $validator, CustomerAddressService $customerAddressService)
    {
        $this->validator = $validator;
        $this->customerAddressService = $customerAddressService;
    }

    #[Route("/list", name: "list", methods: ['GET'])]
    public function getAddresses(Request $request): JsonResponse
    {
        try {
            $customer  = $this->getUser();
            $addresses = $this->customerAddressService->getCustomerAddresses($request, $customer->getUserIdentifier());

            return new JsonResponse(
                [
                    'status' => 'success',
                    'addresses' => $addresses
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

    #[Route("/add", name: "add", methods: ['POST'])]
    public function addAddress(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('customer-address-schema.json');

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

            $address = $this->customerAddressService->addAddress($request);

            if (!$address) {
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
                    'address' => $address
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

    #[Route("/{id}/edit", name: "edit", methods: ['PATCH'])]
    public function editAddress(Uuid $id, Request $request): JsonResponse
    {
        try {
            $customer  = $this->getUser();
            $address = $this->customerAddressService->editAddress($id, $request, $customer->getUserIdentifier());

            if (!$address) {
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
                    'address' => $address
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

    #[Route("/{id}/details", name: "details", methods: ['GET'])]
    public function addressDetail(Uuid $id): JsonResponse
    {
        try {
            $customer = $this->getUser();
            $address = $this->customerAddressService->getAddress($id, $customer->getUserIdentifier());

            if (!$address) {
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
                    'address' => $address
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

    #[Route("/{id}/delete", name: "delete", methods: ['DELETE'])]
    public function deleteAddress(Uuid $id): JsonResponse
    {
        try {
            $customer  = $this->getUser();
            $status = $this->customerAddressService->deleteAddressByCustomer($id, $customer->getUserIdentifier());

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
                    'message' => 'Address was deleted successfully.'
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