<?php

namespace App\Controller\Public;

use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Messages")
 */
#[Route('/api/public/messages', name: 'api_public_message_')]
class MessageController extends AbstractController
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getMessages(Request $request): JsonResponse
    {
        try {
            $customer = $this->getUser();
            $messages = $this->messageService->getCustomerMessages($customer->getUserIdentifier(), $request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'messages' => $messages
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