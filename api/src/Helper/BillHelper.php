<?php

namespace App\Helper;

use App\Entity\Bill;
use App\Entity\Message;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class BillHelper
{
    private MessageHelper $messageHelper;
    private Environment $twig;
    private KernelInterface $kernel;
    private EntityManagerInterface $entityManager;
    private SettingsRepository $settingsRepository;

    public function __construct (
        MessageHelper $messageHelper,
        Environment $twig,
        KernelInterface $kernel,
        EntityManagerInterface $entityManager,
        SettingsRepository $settingsRepository,
    ) {
        $this->messageHelper = $messageHelper;
        $this->twig = $twig;
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
        $this->settingsRepository = $settingsRepository;
    }

    public function generateBillPdf(Bill $bill): void
    {
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $settings = $this->settingsRepository->findOneBy(['id' => 1]);

        $html = $this->twig->render('bill/pdf/template.html.twig', [
            'positions' => $bill->getBillPositions(),
            'bill' => $bill,
            'customer' => $bill->getCustomer(),
            'contactAddress' => $bill->getCustomer()->getSettings()?->getContactAddress(),
            'billingAddress' => $bill->getCustomer()->getSettings()?->getBillingAddress(),
            'settings' => $settings
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $subFolder = (new \DateTime())->format('Y-m-d');
        $pdfPath = $this->kernel->getProjectDir() . '/public/bills/pdf/' . $subFolder  . '/' . $bill->getNumber() . '.pdf';
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $directory = dirname($pdfPath);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new \RuntimeException("Unable to create the directory: $directory");
            }
        }

        file_put_contents($pdfPath, $pdfOutput);

        $customerPhoneNumber = $bill->getCustomer()->getSettings()?->getBillingAddress() ?
            $bill->getCustomer()->getSettings()?->getBillingAddress()->getPhoneNumber() :
            $bill->getCustomer()->getSettings()?->getContactAddress()->getPhoneNumber();

        $customerEmail = $bill->getCustomer()->getSettings()?->getBillingAddress() ?
            $bill->getCustomer()->getSettings()?->getBillingAddress()->getEmailAddress() :
            $bill->getCustomer()->getSettings()?->getContactAddress()->getEmailAddress();

        $message = new Message();
        $message->setCustomer($bill->getCustomer());
        $message->setCreatedDate(new \DateTime());
        $message->setType(Message::TYPE_NOTIFICATION);
        $message->setPhoneNumber($customerPhoneNumber);
        $message->setEmail($customerEmail);
        $message->setSubject('New invoice issued');
        $message->setMessage(
            'We have issued a new invoice to your account for the amount - ' . $bill->getTotalAmount()
        );

        $bill->setFileName($subFolder  . '/' . $bill->getNumber() . '.pdf');

        $this->entityManager->persist($bill);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->messageHelper->sendMessageToCustomer(
            $message,
            $pdfPath
        );
    }
}