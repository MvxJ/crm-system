<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Service\ModelService;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\Uid\Uuid;

/**
 * @OA\Tag(name="Models")
 */
#[Route('/api/public/models', name: 'api_public_model_')]
class ModelController extends AbstractController
{
    private ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getList(Request $request): JsonResponse
    {
        try {
            $models = $this->modelService->getModels($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'models' => $models
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
    public function getDetails(Uuid $id, Request $request): JsonResponse
    {
        try {
            $model = $this->modelService->getModelDetails($id);

            if (!$model) {
                return new JsonResponse(
                    [
                        'success',
                        'message' => 'Model was not found.'
                    ],
                    Response::HTTP_NOT_FOUND
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
}