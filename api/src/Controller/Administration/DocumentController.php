<?php

declare(strict_types=1);

namespace App\Controller\Administration;

use App\Service\DocumentService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/document', name: 'api_document_')]
class DocumentController extends AbstractController
{
    private JsonValidator $validator;
    private DocumentService $documentService;

    public function __construct(JsonValidator $validator, DocumentService $documentService)
    {
        $this->validator = $validator;
        $this->documentService = $documentService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getDocuments(Request $request): JsonResponse
    {
        try {
            $documents = $this->documentService->getDocuments($request, false);

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
    public function getDocumentDetails(int $id, Request $request): JsonResponse
    {
        try {
            $document = $this->documentService->getDocument($id);

            if (!$document) {
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

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createDocument(Request $request): JsonResponse
    {
        $errors = $this->validator->validateRequest('document-schema.json');

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
            $user = $this->getUser();

            if (!$user) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'You dont have permissions to do this action.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $document = $this->documentService->addDocument($request, $user);

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

    #[Route('/{id}/edit', name: 'edit', methods: ['PATCH'])]
    public function editDocument(int $id, Request $request): JsonResponse
    {
        try {
            $document = $this->documentService->editDocument($id, $request, $this->getUser());

            if (!$document) {
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

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function deleteDocument(int $id, Request $request): JsonResponse
    {
        try {
            $success = $this->documentService->deleteDocument($id);

            if (!$success) {
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
                    'message' => 'Document was deleted successfully.'
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