<?php

namespace App\Service;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentService
{
    private DocumentRepository $documentRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private ModelRepository $modelRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ModelRepository $modelRepository
    ) {
        $this->documentRepository = $documentRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->modelRepository = $modelRepository;
    }

    public function getDocument(int $id, bool $isCustomer = false): ?array
    {
        $document = $this->documentRepository->findOneBy(['id' => $id]);

        if (!$document) {
            return null;
        }

        if ($isCustomer && !$document->getIsForClients()) {
            return null;
        }

        return $this->parseObjectToArray($document);
    }

    public function getDocuments(Request $request, bool $isClient = false): ?array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $orderBy = $request->get('orderBy', 'id');
        $order = $request->get('order', 'ASC');
        $maxDocuments = $this->documentRepository->countDocuments($isClient);
        $documentsArray = [];
        $documents = $this->documentRepository->getDocumentsWithPagination(
            (int)$page,
            (int)$itemsPerPage,
            $order,
            $orderBy,
            $isClient
        );

        if (count($documents) == 0) {
            return null;
        }

        /** @var Document $document */
        foreach ($documents as $document) {
            $documentsArray[] = [
                'id' => $document->getId(),
                'title' => $document->getTitle(),
                'createdDate' => $document->getCreatedDate(),
                'updatedDate' => $document->getEditDate(),
                'author' => $document->getAuthor()->getId(),
                'isForCustomers' => $document->getIsForClients(),
                'description' => $document->getDescription()
            ];
        }

        return [
            'documents' => $documentsArray,
            'maxResults' => $maxDocuments
        ];
    }

    public function deleteDocument(int $id): bool
    {
        $document = $this->documentRepository->findOneBy(['id' => $id]);

        if (!$document) {
            return false;
        }

        $this->entityManager->remove($document);
        $this->entityManager->flush();

        return true;
    }

    public function addDocument(Request $request, UserInterface $userInterface): ?array
    {
        $document = new Document();
        $content = json_decode($request->getContent(), true);
        $document = $this->objectCreator($content, $document);
        $user = $this->userRepository->findOneBy(['email' => $userInterface->getUserIdentifier()]);

        $document->setCreatedDate(new \DateTime());
        $document->setAuthor($user);

        $this->entityManager->persist($document);
        $this->entityManager->flush();

        return $this->parseObjectToArray($document);
    }

    public function editDocument(int $id, Request $request, UserInterface $userInterface): ?array
    {
        $document = $this->documentRepository->findOneBy(['id' => $id]);
        $user = $this->userRepository->findOneBy(['email' => $userInterface->getUserIdentifier()]);

        if (!$document) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $document = $this->objectCreator($content, $document);

        $document->setEditDate(new \DateTime());
        $document->setEditor($user);

        $this->entityManager->persist($document);
        $this->entityManager->flush();

        return $this->parseObjectToArray($document);
    }

    private function parseObjectToArray(Document $document): array
    {
        return [
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'description' => $document->getDescription(),
            'content' => $document->getContent(),
            'author' => $document->getAuthor()->getId(),
            'createdDate' => $document->getCreatedDate(),
            'editedDate' => $document->getEditDate(),
            'isForClients' => $document->getIsForClients(),
            'editedBy' => $document->getEditor() ? $document->getEditor()->getId() : null,
            'modelId' => $document->getModel() ? $document->getModel()->getId() : null
        ];
    }

    private function objectCreator(array $content, Document $document): ?Document
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Document::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if (method_exists($document, $setterMethod)) {
                    if ($setterMethod == 'setModel') {
                        $model = $this->modelRepository->findOneBy(['id' => (int)$fieldValue]);

                        if (!$model) {
                            return null;
                        }

                        $document->$setterMethod($model);
                    } else {
                        $document->$setterMethod($fieldValue);
                    }

                }
            }
        }

        return $document;
    }
}