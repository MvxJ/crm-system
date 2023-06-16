<?php

namespace App\Service;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingsService
{
    private string $uploadDir;
    private SettingsRepository $settingsRepository;
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    public function __construct(
        string $uploadDir,
        SettingsRepository $settingsRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ) {
        $this->uploadDir = $uploadDir;
        $this->settingsRepository = $settingsRepository;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function getSystemSettings(int $id): Settings
    {
        $settings = $this->settingsRepository->findOneBy(['id' => $id]);

        return $settings;
    }

    public function updateSettings(int $id, Request $request): void
    {
        $requestContent = json_decode($request->getContent(), true);
        $settings = $this->settingsRepository->findOneBy(['id' => $id]);

        if ($requestContent['companyName']) {
            $settings->setCompanyName($requestContent['companyName']);
        }

        if ($requestContent['companyAddress']) {
            $settings->setCompanyAddress($requestContent['companyAddress']);
        }

        if ($requestContent['companyPhoneNumber']) {
            $settings->setCompanyPhoneNumber($requestContent['companyPhoneNumber']);
        }

        if ($requestContent['emailAddress']) {
            $settings->setEmailAddress($requestContent['emailAddress']);
        }

        if ($requestContent['facebookUrl']) {
            $settings->setFacebookUrl($requestContent['facebookUrl']);
        }

        if ($requestContent['privacyPolicy']) {
            $settings->setPrivacyPolicy($requestContent['privacyPolicy']);
        }

        if ($requestContent['termsAndConditions']) {
            $settings->setTermsAndConditions($requestContent['termsAndConditions']);
        }

        if ($requestContent['mailerAddress']) {
            $settings->setMailerAddress($requestContent['mailerAddress']);
        }

        if ($requestContent['technicalSupportNumber']) {
            $settings->setTechnicalSupportNumber($requestContent['technicalSupportNumber']);
        }

        $this->entityManager->persist($settings);
        $this->entityManager->flush();
    }

    public function uploadLogo(int $id, Request $request): void
    {
        $logo = $request->files->get('logo');
        $settings = $this->settingsRepository->findOneBy(['id' => $id]);

        if ($logo && $settings) {
            $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();

            $settings->setLogoUrl($newFilename);
            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            $logo->move(
                $this->uploadDir,
                $newFilename
            );
        }
    }
}