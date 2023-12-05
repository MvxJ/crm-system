<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\SettingsRepository;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Twig\Environment;

class MailerService
{
    private string $uploadDir;
    private string $clientVerifyAddress;
    private MailerInterface $mailer;
    private SettingsRepository $settingsRepository;
    private Environment $twig;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        string $uploadDir,
        string $clientVerifyAddress,
        MailerInterface $mailer,
        SettingsRepository $settingsRepository,
        Environment $twig,
        VerifyEmailHelperInterface $verifyEmailHelper,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->uploadDir = $uploadDir;
        $this->clientVerifyAddress = $clientVerifyAddress;
        $this->mailer = $mailer;
        $this->settingsRepository = $settingsRepository;
        $this->twig = $twig;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->urlGenerator = $urlGenerator;
    }

        public function sendConfirmationEmail(string $verifyEmailRouteName, Customer $customer, ?string $password = null)
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $customer->getId(),
            $customer->getEmail()
        );

        $email = $this->createEmailMessage(
            'Account Confirmation',
            [
                'customer' => $customer,
                'password' => $password,
                'signedUrl' => $this->generateFeUrl($signatureComponents->getSignedUrl()),
                'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
            ],
            'emails/registration-confirmation-email.html.twig',
            $customer->getEmail()
        );

        $this->mailer->send($email);
    }

    public function sendTwoFactorAuthenticationEmail(TwoFactorInterface $user)
    {
        $email = $this->createEmailMessage(
            'Two-Factor Authentication',
            [
                'authCode' => $user->getEmailAuthCode(),
            ],
            'emails/authentication-code-email.html.twig',
            $user->getEmailAuthRecipient()
        );

        $this->mailer->send($email);
    }


    private function createEmailMessage(
        string $subject,
        array $context,
        string $template,
        string $recipient
    ): TemplatedEmail {
        $settings = $this->settingsRepository->findOneBy(['id' => 1]);

        $email = (new TemplatedEmail())
            ->from(new Address($settings->getMailerAddress(), $settings->getMailerName()))
            ->to($recipient)
            ->subject($subject)
            ->htmlTemplate($template);

        $context['crmSettings'] = $settings;

        if ($settings->getLogoUrl()) {
            $logoPath = $this->uploadDir . '/' . $settings->getLogoUrl();
            $email->addPart((new DataPart(fopen($logoPath, 'r'), 'logo', 'image/png'))->asInline());
            $context['logoUrl'] = $this->urlGenerator->generate(
                'api_file_display',
                [
                    'fileName' => $settings->getLogoUrl()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } else {
            $context['logoUrl'] = '';
        }

        $email->context($context);

        return $email;
    }

    private function generateFeUrl(string $verifyUrl):string
    {
        return $this->clientVerifyAddress . substr($verifyUrl, strpos($verifyUrl, '?'));
    }
}