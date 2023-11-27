<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerSettings;
use App\Entity\Role;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use App\Security\EmailVerifier;
use App\Service\MailerService;
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
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var MailerService
     */
    private MailerService $mailerService;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository,
        EmailVerifier $emailVerifier,
        MailerService $mailerService,
        CustomerRepository $customerRepository
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->emailVerifier = $emailVerifier;
        $this->mailerService = $mailerService;
        $this->customerRepository = $customerRepository;
    }

    #[Route('/register', name: 'register_customer', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $roleClient = $this->roleRepository->findOneBy(['role' => Role::ROLE_CUSTOMER]);
            $customer = new Customer();
            $settings = new CustomerSettings();
            $requestContent = json_decode($request->getContent(), true);

            $customer->setEmail($requestContent['email']);
            $customer->setPassword($this->userPasswordHasher->hashPassword(
                $customer,
                $requestContent['password']
            ));
            $customer->addRole($roleClient);
            $customer->setEmailAuthEnabled(true);
            $customer->setSettings($settings);

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            $this->mailerService->sendConfirmationEmail(
                'api_register_confirm',
                $customer
            );
        } catch (\Exception $exception) {
            dd($exception);
            return new JsonResponse(
                [
                    'message' => 'An error occurred during registration please try again'
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
        $email = $requestContent['email'];

        if (!$email) {
            return new JsonResponse(
                [
                    'message' => 'Bad request, email parameter missing.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $customer = $this->customerRepository->findOneBy(['email' => $email]);
            $this->emailVerifier->handleEmailConfirmation($request, $customer);
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

        if (array_key_exists('email', $requestContent)) {
            try {
                $customer = $this->customerRepository->findOneBy(['email' => $requestContent['email']]);

                if ($customer->isVerified()) {
                    return new JsonResponse(
                        [
                            'message' => 'Account already verified.'
                        ],
                        Response::HTTP_OK
                    );
                }

                $this->mailerService->sendConfirmationEmail(
                    'api_register_confirm',
                    $customer
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
