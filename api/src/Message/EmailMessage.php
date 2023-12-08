<?php

namespace App\Message;

use App\Entity\Message;
use App\Helper\EmailHelper;
use Symfony\Component\Mailer\MailerInterface;

class EmailMessage implements MessageInterface
{
    private MailerInterface $mailer;
    private EmailHelper $emailHelper;

    public function __construct(MailerInterface $mailer, EmailHelper $emailHelper)
    {
        $this->mailer = $mailer;
        $this->emailHelper = $emailHelper;
    }

    public function sendMessage(Message $message): void
    {
        $email = $this->emailHelper->getEmailTemplate($message);

        $this->mailer->send($email);
    }
}