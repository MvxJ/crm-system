<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        EmailVerifier $emailVerifier,
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'register_customer', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $roleClient = $this->roleRepository->findOneBy(['id' => Role::ROLE_CUSTOMER]);
            $user = new User();
            $requestContent = json_decode($request->getContent(), true);

            $user->setUsername($requestContent['username']);
            $user->setEmail($requestContent['email']);
            $user->setPassword($this->userPasswordHasher->hashPassword(
                $user,
                $requestContent['password']
            ));
            $user->addRole($roleClient);
            $user->setEmailAuth(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'api_register_confirm',
                $user
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'message' => 'An error occurred during registration please try again',

                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'message' => 'Registered successfully',
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/register/confirm', name: 'register_confirm', methods: ['POST'])]
    public function confirmRegistration(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        $username = $requestContent['username'];

        if (!$username) {
            return new JsonResponse(
                [
                    'message' => 'Bad request, username parameter missing.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $this->userRepository->findOneBy(['username' => $username]);
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return new JsonResponse(
                [
                    'message' => 'An error occurred during verification user email.'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'message' => 'Successfully verified user email.'
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/register/resend', name: 'resend_confirmation', methods: ['POST'])]
    public function resendConfriamtionEmail(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        if (array_key_exists('username', $requestContent)) {
            try {
                $user = $this->userRepository->findOneBy(['username' => $requestContent['username']]);

                if ($user->isVerified()) {
                    return new JsonResponse(
                        [
                            'message' => 'Account already verified.'
                        ],
                        Response::HTTP_OK
                    );
                }

                $this->emailVerifier->sendEmailConfirmation(
                    'api_register_confirm',
                    $user
                );

                return new JsonResponse(
                    [
                        'message' => 'Verification email was resend.'
                    ],
                    Response::HTTP_OK
                );
            } catch (\Exception $exception) {
                return new JsonResponse(
                    [
                        'message' => 'An error occurred please try again later.'
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }

        return new JsonResponse(
            [
                'Required parameter missing, please send all required parameters.'
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
