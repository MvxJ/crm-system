<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Service\Validator\JsonValidator;
use App\Service\ModelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/models', name: 'api_model_')]
class ModelController extends  AbstractController
{
    private JsonValidator $validator;
    private ModelService $modelService;

    public function __construct(JsonValidator $validator, ModelService $modelService)
    {
        $this->validator = $validator;
        $this->modelService = $modelService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getModels(Request $request): JsonResponse
    {
        try {
            $models = $this->modelService->getModels($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $models
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

    #[Route('/{id}', name: 'details', methods: ['GET'])]

    public function getModelDetails(int $id, Request $request): JsonResponse
    {
        try {
            $model = $this->modelService->getModelDetails($id);

            if (!$model) {
                return new JsonResponse(
                    [
                        'status' => 'success',
                        'message' => 'Model was not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'model' => $model
                ]
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ]
            );
        }
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addModel(Request $request): JsonResponse
    {
        $errors = $this->validator->validateRequest('model-schema.json');

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
            $model = $this->modelService->addModel($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'model' => $model
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
    public function editModel(int $id, Request $request): JsonResponse
    {
        try {
            $model = $this->modelService->editModel($id, $request);

            if (!$model) {
                return new JsonResponse(
                    [
                        'status' => 'success',
                        'message' => 'Model not found.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'model' => $model
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
    public function deleteModel(int $id, Request $request): JsonResponse
    {
        try {
            $success = $this->modelService->deleteModel($id);

            if (!$success) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Cant delete model. Try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Model deleted successfully.'
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

    #[Route('/{id}/devices', name: 'devices', methods: ['GET'])]
    public function getModelDevices(int $id, Request $request): JsonResponse
    {
        try {
            $devices = $this->modelService->getModelDevices($id, $request);

            if (!is_array($devices)) {
                return new JsonResponse(
                    [
                        'status' => 'success',
                        'message' => 'Model not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'devices' => $devices
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