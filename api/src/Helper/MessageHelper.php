<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Message;
use App\Repository\SettingsRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MessageHelper
{
    private string $uploadDir;
    private TexterInterface $texter;
    private MailerInterface $mailer;
    private SettingsRepository $settingsRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        string $uploadDir,
        TexterInterface $texter, 
        MailerInterface $mailer,
        SettingsRepository $settingsRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->uploadDir = $uploadDir;
        $this->texter = $texter;
        $this->mailer = $mailer;
        $this->settingsRepository = $settingsRepository;
        $this->urlGenerator = $urlGenerator;
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
                $message->getSubject(),
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

    private function sendEmailMessageToCustomer(int $type, string $email, string $message, string $subject, ?string $attachemtnPath): void
    {
        $settings = $this->settingsRepository->findOneBy(['id' => 1]);
        $context = [];
        $email = (new TemplatedEmail())
            ->from(new Address($settings->getMailerAddress(), $settings->getMailerName()))
            ->to($email)
            ->subject($subject)
            ->htmlTemplate('emails/custom_message.html.twig');

        $context['crmSettings'] = $settings;
        $context['subject'] = $subject;
        $context['type'] = $this->getMessageTypeName($type);
        $context['message'] = $message;

        if ($settings->getLogoUrl()) {
            $logoPath = $this->uploadDir . '/' . $settings->getLogoUrl();
            $email->addPart((new DataPart(fopen($logoPath, 'r'), 'logo', 'image/png'))->asInline());
            $context['logoUrl'] = $this->urlGenerator->generate(
                'api_file_display',
                [
                    'fileName' => $settings->getLogoUrl(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } else {
            $context['logoUrl'] = '';
        }

        $email->context($context);

        if ($attachemtnPath) {
            $email->attachFromPath($attachemtnPath, 'document.pdf', 'application/pdf');
        }

        $this->mailer->send($email);
    }

    private function getMessageTypeName(int $type): array
    {
        $typeArray = ['name' => 'Unknown', 'color' => '#f5f5f5'];

        switch ($type) {
            case 0:
                $typeArray = ['name' => 'Notification', 'color' => '#91d5ff'];
                break;
            case 1:
                $typeArray = ['name' => 'Reminder', 'color' => '#ffd666'];
                break;
            case 2:
                $typeArray = ['name' => 'Message', 'color' => '#91d5ff'];
                break;
            default:
                $typeArray = ['name' => 'Unknown', 'color' => '#f5f5f5'];
                break;
        }

        return $typeArray;
    }
}