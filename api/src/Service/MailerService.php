<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Twig\Environment;

class MailerService
{
    private string $clientVerifyAddress;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MessageBusInterface $bus;
    private EntityManagerInterface $entityManager;
    private CustomerRepository $customerRepository;

    public function __construct(
        string $clientVerifyAddress,
        VerifyEmailHelperInterface $verifyEmailHelper,
        MessageBusInterface $bus,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository
    ) {
        $this->clientVerifyAddress = $clientVerifyAddress;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
    }

        public function sendConfirmationEmail(string $verifyEmailRouteName, Customer $customer, ?string $password = null)
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $customer->getId()->toBinary(),
            $customer->getEmail()
        );

//        'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey()
//        'expiresAtMessageData' => $signatureComponents->getExpirationMessageData()

        $message = new Message();
        $message->setSubject('Account Confirmation');
        $message->setEmail($customer->getEmail());
        $message->setCreatedDate(new \DateTime());
        $message->setCustomer($customer);
        $message->setType(Message::TYPE_ACCOUNT_CONFIRMATION);
        $message->setMessage('<p>
            Please confirm your email address by clicking the following link: <br><br>
            ' . $password ? 'Your generated password:' . $password .'<br><br>' . '<a href="' . $this->generateFeUrl($signatureComponents->getSignedUrl()) . '">Confirm my Email</a>.
            </p>' : ' ' .
            '<a href="' . $this->generateFeUrl($signatureComponents->getSignedUrl()) . '">Confirm my Email</a>.
            </p>');

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->bus->dispatch(
            $message,
            [
                new AmqpStamp(null, AMQP_MANDATORY, ['priority' => 9])
            ]
        );
    }

    public function sendTwoFactorAuthenticationEmail(TwoFactorInterface $user)
    {
        $customer = $this->customerRepository->findOneBy(['email' => $user->getEmailAuthRecipient()]);

        if (!$customer) {
            throw new \Exception("Couldn't find the customer.");
        }

        $message = new Message();
        $message->setSubject('Two-Factor Authentication');
        $message->setEmail($user->getEmailAuthRecipient());
        $message->setCreatedDate(new \DateTime());
        $message->setType(Message::TWO_FACTOR_CODE);
        $message->setCustomer($customer);
        $message->setMessage('<p>Your verification code is: ' . $user->getEmailAuthCode() . ' </p>');

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->bus->dispatch(
            $message,
            [
                new AmqpStamp(null, AMQP_MANDATORY, ['priority' => 9])
            ]
        );
    }

    private function generateFeUrl(string $verifyUrl):string
    {
        return $this->clientVerifyAddress . substr($verifyUrl, strpos($verifyUrl, '?'));
    }
}