<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Service\DeviceService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/devices', name: 'api_device_')]
class DeviceController extends AbstractController
{
    private JsonValidator $validator;
    private DeviceService $deviceService;

    public function __construct(JsonValidator $validator, DeviceService $deviceService)
    {
        $this->validator = $validator;
        $this->deviceService = $deviceService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getDevices(Request $request): JsonResponse
    {
        try {
            $devices = $this->deviceService->getDeviceList($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $devices
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

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function getDeviceDetails(Uuid $id, Request $request): JsonResponse
    {
        try {
            $device = $this->deviceService->getDeviceDetails($id);

            if (!$device) {
                return new JsonResponse(
                    [
                        'status' => 'success',
                        'message' => 'Device not Found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status'=> 'success',
                    'device' => $device
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
    public function addDevice(Request $request): JsonResponse
    {
        $errors = $this->validator->validateRequest('device-schema.json');

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

        try {
            $device = $this->deviceService->addDevice($request);

            if (!$device) {
                return new JsonResponse(
                  [
                      'status' => 'error',
                      'message' => 'There was an error while creating Device. Please try again later.'
                  ] ,
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'device' => $device
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
    public function deleteDevice(Uuid $id, Request $request): JsonResponse
    {
        try {
            $status = $this->deviceService->deleteDevice($id);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Device not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Device deleted successfully.'
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
    public function editDevice(Uuid $id, Request $request): JsonResponse
    {
        try {
            $device = $this->deviceService->editDevice($id, $request);

            if (!$device) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request. Please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'device' => $device
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