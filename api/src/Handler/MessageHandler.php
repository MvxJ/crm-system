<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Message;
use App\Message\EmailMessage;
use App\Message\SmsMessage;
use App\Repository\CustomerSettingsRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MessageHandler
{
    private SmsMessage $smsMessage;
    private EmailMessage $emailMessage;
    private CustomerSettingsRepository $customerSettingsRepository;

    public function __construct(
        SmsMessage $smsMessage,
        EmailMessage $emailMessage,
        CustomerSettingsRepository $customerSettingsRepository
    ) {
        $this->smsMessage = $smsMessage;
        $this->emailMessage = $emailMessage;
        $this->customerSettingsRepository = $customerSettingsRepository;
    }

    public function __invoke(Message $message): void
    {
        try {
            $customer = $message->getCustomer();
            $customerSettings = $this->customerSettingsRepository->getCustomerSettings($customer->getId());

            if (!$customerSettings) {
                throw new \Exception('Customer settings not found.');
            }

            if ($customerSettings[0]['smsNotifications'] === true &&
                $message->getType() != Message::TYPE_ACCOUNT_CONFIRMATION
            ) {
                $this->smsMessage->sendMessage($message);
            }

            if ($customerSettings[0]['emailNotifications'] === true) {
                $this->emailMessage->sendMessage($message);
            }

        } catch (TransportExceptionInterface $exception) {
        }
    }
}