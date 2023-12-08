<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Uid\Uuid;

/**
 * @OA\Tag(name="Documents")
 */
#[Route('/api/public/document', name: 'api_public_document_')]
class DocumentController extends AbstractController
{
    private DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getDocuments(Request $request): JsonResponse
    {
        try {
            $documents = $this->documentService->getDocuments($request, true);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $documents
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
    public function getDocumentDetails(Uuid $id, Request $request): JsonResponse
    {
        try {
            $document = $this->documentService->getDocument($id, true);

            if (!$document) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Invalid request. Please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'document' => $document
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