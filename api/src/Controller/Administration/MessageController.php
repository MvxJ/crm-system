<?php

namespace App\Controller\Administration;

use App\Service\MessageService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/message', name: 'api_message_')]
class MessageController extends AbstractController
{
    private MessageService $messageService;
    private JsonValidator $validator;

    public function __construct(MessageService $messageService, JsonValidator $validator)
    {
        $this->messageService = $messageService;
        $this->validator = $validator;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getMessages(Request $request): JsonResponse
    {
        try {
            $messages = $this->messageService->getMessages($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $messages
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

    #[Route('/detail/{id}', name: 'detail', methods: ['GET'])]
    public function getMessageDetails(int $id, Request $request): JsonResponse
    {
        try {
            $message = $this->messageService->getMessage($id);

            if (!$message) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request please try again.'
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => $message
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
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $errors = $this->validator->validateRequest('message-schema.json');

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

            $message = $this->messageService->createMessage($request);

            if (!$message) {
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
                    'results' => $message,
                    'message' => 'Message was send successfully.'
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