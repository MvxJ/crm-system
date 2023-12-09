<?php

namespace App\Message;

use App\Entity\Customer;
use App\Entity\Message;

interface MessageInterface
{
    public function sendMessage(Message $message): void;
}