<?php

namespace App\Message;

use App\Entity\Message;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage as Sms;

class SmsMessage implements MessageInterface
{
    private TexterInterface $texter;

    public function __construct(TexterInterface $texter)
    {
        $this->texter = $texter;
    }

    public function sendMessage(Message $message): void
    {
        try {
            if (!$message->getPhoneNumber()) {
                $phoneNumber = $message->getCustomer()->getPhoneNumber();
            } else {
                $phoneNumber = $message->getPhoneNumber();
            }

            $sms = new Sms(
                $phoneNumber,
                strip_tags($message->getMessage())
            );

            $result = $this->texter->send($sms);
        } catch (\Exception $exception) {
        }
    }
}