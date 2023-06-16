<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/file", name: "api_file_")]
class FileController extends AbstractController
{
    #[Route("/display/{fileName}", name: "display", methods: ["GET"])]
    public function showFile(string $fileName): BinaryFileResponse
    {
        $filePath = $this->getParameter('app.upload_directory') . '/system/' . $fileName;

        if (!file_exists($filePath)) {
            throw new FileNotFoundException('The requested file does not exist.');
        }

        return new BinaryFileResponse($filePath);
    }
}