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

    public function __construct(
        UserService $userService,
        JsonValidator $validator,
    ) {
        $this->userService = $userService;
        $this->validator = $validator;
    }

    #[Route("/list", name: "list", methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $usersArray = $this->userService->getUsers($request);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'results' => $usersArray
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

    #[Route("/{userId}/password/change", name: "password_change", methods: ["POST"])]
    public function changePassword(int $userId, Request $request): JsonResponse
    {
        try {
            $status = $this->userService->changePassword($userId, $request);

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
                    'message' => 'Successfully changed password.'
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

    #[Route("/{userId}/roles/edit", name: "roles_edit", methods: ["POST"])]
    public function editRoles(int $userId, Request $request): JsonResponse
    {
        try {
            $status = $this->userService->editRoles($userId, $request);

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
                    'message' => 'Successfully edited user roles.'
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
        try {
            $errors = $this->validator->validateRequest('user-schema.json');

            if (count($errors) > 0) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'errors' => $errors
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user = $this->userService->addUser($request);

            if (!$user) {
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
                    'user' => $user
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

    #[Route("/{userId}/delete/", name: "delete", methods: ["DELETE"])]
    public function deleteUser(int $userId): JsonResponse
    {
        try {
            $status = $this->userService->deleteUser($userId);

            if (!$status) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'Bad request, please try again later.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

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

    #[Route("/{userId}/edit/", name: "edit", methods: ["PATCH"])]
    public function editUser(int $userId, Request $request): JsonResponse
    {
        try {
            $user  = $this->userService->editUser($userId, $request);

            if (!$user) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'User not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'user' => $user
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

    #[Route("/{userId}/detail", name: "detail", methods: ["GET"])]
    public function getUserDetail(int $userId): JsonResponse
    {
        try {
            $user = $this->userService->getUser($userId);

            if (!$user) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'User not found'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'user' => $user
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