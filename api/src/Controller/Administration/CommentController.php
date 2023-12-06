<?php

namespace App\Controller\Administration;

use App\Service\CommentService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/comment', name: 'api_comment_')]
class CommentController extends AbstractController
{
    private JsonValidator $validator;
    private CommentService $commentService;

    public function __construct(JsonValidator $validator, CommentService $commentService)
    {
        $this->validator = $validator;
        $this->commentService = $commentService;
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addComment(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('comment-schema.json');

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

            $comment = $this->commentService->addComment($request);

            if (!$comment) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request, please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'comment' => $comment
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
    public function editComment(int $id, Request $request): JsonResponse
    {
        try {
            $comment = $this->commentService->editComment($id, $request);

            if (!$comment) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request, please try again.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'comment' => $comment
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

    #[Route('/{id}/hide', name: 'hide', methods: ['POST'])]
    public function hideComment(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $status = $this->commentService->hideComment($id, $user->getUserIdentifier());

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
                    'message' => 'Your comment was hidden successfully.'
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
    public function deleteComment(int $id, Request $request): JsonResponse
    {
        try {
            $status = $this->commentService->deleteComment($id);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Comment was deleted successfully.'
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

    #[Route('/{serviceRequestId}/list', name: 'list', methods: ['GET'])]
    public function getComments(int $serviceRequestId, Request $request): JsonResponse
    {
        try {
            $comments = $this->commentService->getCommentsByServiceRequest($serviceRequestId, $request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'data' => $comments
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