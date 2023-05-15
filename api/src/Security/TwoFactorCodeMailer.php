<?php

namespace App\Security;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class TwoFactorCodeMailer implements AuthCodeMailerInterface
{
    private string $noReplyAddress;
    private string $mailerName;
    private MailerInterface $mailer;

    public function __construct(
        string $noReplyAddress,
        string $mailerName,
        MailerInterface $mailer
    ) {
        $this->noReplyAddress = $noReplyAddress;
        $this->mailerName = $mailerName;
        $this->mailer = $mailer;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();

        $email = (new TemplatedEmail())
            ->subject('Authentication Code')
            ->from(new Address($this->noReplyAddress, $this->mailerName))
            ->to($user->getEmailAuthRecipient())
            ->htmlTemplate('emails/authentication-code-email.html.twig')
            ->context([
                'authCode' => $authCode,
            ]);

        $this->mailer->send($email);
    }
}