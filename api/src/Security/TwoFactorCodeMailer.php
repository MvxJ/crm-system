<?php

namespace App\Security;

use App\Service\MailerService;
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
    private MailerService $mailerService;

    public function __construct(
        string $noReplyAddress,
        string $mailerName,
        MailerInterface $mailer,
        MailerService $mailerService
    ) {
        $this->noReplyAddress = $noReplyAddress;
        $this->mailerName = $mailerName;
        $this->mailer = $mailer;
        $this->mailerService = $mailerService;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $this->mailerService->sendTwoFactorAuthenticationEmail($user);
    }
}