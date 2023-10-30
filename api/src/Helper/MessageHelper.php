<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class MessageHelper
{
    private TexterInterface $texter;
    private MailerInterface $mailer;

    public function __construct(TexterInterface $texter, MailerInterface $mailer)
    {
        $this->texter = $texter;
        $this->mailer = $mailer;
    }

    public function sendMessageToCustomer(Message $message, ?string $attachmentPath = null): void
    {
        $customerSettings  = $message->getCustomer()->getSettings();

        if(!$customerSettings) {
            return;
        }

        if ($customerSettings->isSmsNotifications()) {
            $this->sendSmsMessageToCustomer(
                $message->getType(),
                $message->getPhoneNumber(),
                $message->getMessage()
            );
        }

        if ($customerSettings->getEmailNotifications()) {
            $this->sendEmailMessageToCustomer(
                $message->getType(),
                $message->getEmail(),
                $message->getMessage(),
                $attachmentPath
            );
        }
    }

    private function sendSmsMessageToCustomer(int $type, string $phoneNumber, string $message): void
    {
        $sms = new SmsMessage(
            $phoneNumber,
            $message
        );

        $sentMessage = $this->texter->send($sms);
    }

    private function sendEmailMessageToCustomer(int $type, string $email, string $message, ?string $attachemtnPath): void
    {
        $email = (new Email())
            ->from('giganet@noreply.com')
            ->to($email)
            ->subject('Invoice NO')
            ->text($message);

        if ($attachemtnPath) {
            $email->attachFromPath($attachemtnPath);
        }

        $this->mailer->send($email);
    }
}