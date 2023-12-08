<?php

namespace App\Helper;

use App\Entity\Message;
use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailHelper
{
    private string $uploadDir;
    private SettingsRepository $settingsRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        string $uploadDir,
        SettingsRepository $settingsRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->uploadDir = $uploadDir;
        $this->settingsRepository = $settingsRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function getEmailTemplate(Message $message): TemplatedEmail
    {
        $settings = $this->settingsRepository->findAll()[0];

        $context = [
            'crmSettings' => $settings,
            'subject' => $message->getSubject(),
            'type' => $this->getMessageTypeBadge($message->getType()),
            'message' => $message->getMessage()
        ];
        $template = $this->createTemplatedEmail($message, $settings);

        if ($settings->getLogoUrl() != null) {
            $logoPath = $this->uploadDir . '/' . $settings->getLogoUrl();
            $template->addPart(
                    (new DataPart(
                        fopen($logoPath, 'r'), 'logo', 'image/png'
                    )
                )->asInline());

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

        if ($message->getAttachmentPatch() != null) {
            $template->attachFromPath(
                $message->getAttachmentPatch(),
                $message->getAttachmentName(),
                'application/pdf'
            );
        }

        $template->context($context);

        return $template;
    }

    private function createTemplatedEmail(Message $message, Settings $settings): TemplatedEmail
    {
        $mailerName = $settings->getMailerName();
        $mailerAddress = $settings->getMailerAddress();

        if (!$mailerAddress || !$mailerName) {
            throw new \Exception('Invalid mailer configuration please check system settings.');
        }

        $templatedEmail = (new TemplatedEmail())->from(new Address($mailerAddress, $mailerName))
            ->to($message->getEmail())
            ->subject($message->getSubject())
            ->htmlTemplate(
                $this->getEmailTemplateName($message)
            );

        return $templatedEmail;
    }

    private function getEmailTemplateName(Message $message): string
    {
        $template = '';
        $messageType = $message->getType();
        $attachment = $message->getAttachmentPatch();

        if ($messageType == Message::TYPE_NOTIFICATION || $attachment != null) {
            return 'emails/custom_message.html.twig';
        }

        switch ($messageType) {
            case Message::TYPE_ACCOUNT_CONFIRMATION;
                $template = 'emails/registration-confirmation-email.html.twig';
                break;
            case Message::TWO_FACTOR_CODE:
                $template = 'emails/authentication-code-email.html.twig';
                break;
            default:
                $template = 'emails/custom_message.html.twig';
                break;
        }

        return $template;
    }

    private function getMessageTypeBadge(int $type): array
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