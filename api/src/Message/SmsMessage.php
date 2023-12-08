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
        $sms = new Sms(
            $message->getPhoneNumber(),
            $message->getMessage()
        );

        $result = $this->texter->send($sms);
    }
}