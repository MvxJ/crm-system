<?php

namespace App\Controller\Administration;

use App\Entity\User;
use App\Service\UserService;
use App\Service\Validator\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    private UserService $userService;
    private JsonValidator $validator;
    private SerializerInterface $serializer;

    public function __construct(
        UserService $userService,
        JsonValidator $validator,
        SerializerInterface $serializer
    ) {
        $this->userService = $userService;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    #[Route("/list", name: "list", methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $usersArray = $this->userService->getUsers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'items' => $usersArray['users'],
                    'page' => $usersArray['page'],
                    'limit' => $usersArray['limit']
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

    #[Route("/add", name: "add", methods: ["POST"])]
    public function addUser(Request $request): JsonResponse
    {
        $errors = $this->validator->validateRequest('user-schema.json');

        if (count($errors) > 0) {
            return new JsonResponse(
                [

                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $userId = $this->userService->addUser($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'userId' => $userId
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

    #[Route("/delete/{user}", name: "delete", methods: ["DELETE"])]
    public function deleteUser(User $user): JsonResponse
    {
        try {
            $this->userService->deleteUser($user);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'User was deleted'
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

    #[Route("/edit/{user}", name: "edit", methods: ["POST", "PUT", "PATCH"])]
    public function editUser(User $user, Request $request): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'User not found.'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->userService->editUser($user, $request);

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

    #[Route("/{user}", name: "detail", methods: ["GET"])]
    public function getUserDetail(User $user): JsonResponse
    {
        if ($user) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'user' => [
                        'id' => $user->getId(),
                        'email' => $user->getEmail(),
                        'username' => $user->getUsername(),
                        'is_verified' => $user->isVerified(),
                        'email_auth' => $user->isEmailAuthEnabled(),
                        'roles' => $user->getRoles(),
                        'profile' => $this->serializer->normalize(
                            $user->getProfile(),
                            null,
                            [
                                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'id']
                            ]
                        )
                    ]
                ],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            [
                'status' => 'error',
                'message' => 'User not found'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}