<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Service\SettingsService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/settings", name: "api_settings_")]
class SettingsController extends AbstractController
{
    private SettingsService $settingsService;
    private JsonValidator $validator;
    private SerializerInterface $serializer;

    public function __construct(
        SettingsService $settingsService,
        JsonValidator $jsonValidator,
        SerializerInterface $serializer
    ) {
        $this->settingsService = $settingsService;
        $this->validator = $jsonValidator;
        $this->serializer = $serializer;
    }

    #[Route("/{id}", name: "detail", methods: ["GET"])]
    public function getSystemSettings(int $id):JsonResponse
    {
        try {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'settings' => $this->serializer->normalize($this->settingsService->getSystemSettings($id))
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

    #[Route("/edit/{id}", name: "edit", methods: ["POST", "PUT", "PATCH"])]
    public function editSettings(int $id, Request $request):JsonResponse
    {
        try {
            $this->settingsService->updateSettings($id, $request);

            return new JsonResponse(
                [
                    'status' => 'success',
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

    #[Route("/{id}/logo/upload", name: "upload_logo", methods: ["POST"])]
    public function uploadLogo(int $id, Request $request): JsonResponse
    {
        try {
            $this->settingsService->uploadLogo($id, $request);

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
}