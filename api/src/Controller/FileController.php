<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route("/download", name: "download", methods: ["GET"])]
    public function downloadFile(Request $request): BinaryFileResponse
    {
        $fileName = $request->get('file', null);

        if (!$fileName) {
            throw new FileNotFoundException('No file name provided.');
        }

        $filePath = $this->getParameter('app.bills_directory') . '/' . urldecode($fileName);
        
        if (!file_exists($filePath)) {
            throw new FileNotFoundException('The requested file does not exist.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            // ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            ResponseHeaderBag::DISPOSITION_INLINE,
            basename($fileName)
        );

        return $response;
    }
}