<?php

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\ServiceRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentService
{
    private CommentRepository $commentRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private ServiceRequestRepository $serviceRequestRepository;

    public function __construct(
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ServiceRequestRepository $serviceRequestRepository
    )
    {
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
    }

    public function addComment(Request $request): ?array
    {
        $comment = new Comment();
        $content = json_decode($request->getContent(), true);
        $comment = $this->objectCreator($comment, $content);

        if (!$comment) {
            return null;
        }

        $comment->setCreatedDate(new \DateTime());

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->createCommentArray($comment);
    }

    public function editComment(int $commentId, Request $request): ?array
    {
        $comment = $this->commentRepository->findOneBy(['id' => $commentId]);

        if (!$comment) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $comment = $this->objectCreator($comment, $content);

        if (!$comment) {
            return null;
        }

        $comment->setEditDate(new \DateTime());

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->createCommentArray($comment);
    }

    public function deleteComment(int $commentId): bool
    {
        $comment = $this->commentRepository->findOneBy(['id' => $commentId]);

        if (!$comment) {
            return false;
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return true;
    }

    public function hideComment(int $commentId, string $userEmail): bool
    {
        $comment = $this->commentRepository->findOneBy(['id' => $commentId]);

        if (!$comment || $comment->getUser()->getUsername() != $userEmail) {
            dd($comment, $userEmail);
            return false;
        }

        $comment->setIsHidden(true);
        $comment->setEditDate(new \DateTime());

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return true;
    }

    public function getCommentsByServiceRequest(int $serviceRequestId, Request $request): ?array
    {
        $commentsArray = [];
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'id');
        $maxResults = $this->commentRepository->countCommentsByServiceRequestId($serviceRequestId);
        $comments = $this->commentRepository->getCommentsByServiceRequestId(
            $serviceRequestId,
            $page,
            $itemsPerPage,
            $order,
            $orderBy
        );

        if (count($comments) == 0) {
            return null;
        }

        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $commentsArray[] = $this->createCommentArray($comment);
        }

        return [
            'maxResults' => $maxResults,
            'comments' => $commentsArray
        ];
    }

    private function objectCreator(Comment $comment, array $content): ?Comment
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(Comment::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);

                if ($setterMethod == 'setUser') {
                    $user = $this->userRepository->findOneBy(['id' => $fieldValue]);

                    if (!$user) {
                        return null;
                    }

                    $comment->setUser($user);
                }

                elseif ($setterMethod == 'setServiceRequest') {
                    $serviceRequest = $this->serviceRequestRepository->findOneBy(['id' => $fieldValue]);

                    if (!$serviceRequest) {
                        return null;
                    }

                    $comment->setServiceRequest($serviceRequest);
                }

                elseif (method_exists($comment, $setterMethod)) {
                    $comment->$setterMethod($fieldValue);
                }
            }
        }

        return $comment;
    }

    private function createCommentArray(Comment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'message' => $comment->getMessage(),
            'isHidden' => $comment->getIsHidden(),
            'author' => [
                'id' => $comment->getUser()->getId(),
                'email' => $comment->getUser()->getEmail()
            ],
            'createdAt' => $comment->getCreatedDate(),
            'updatedAt' => $comment->getEditDate()
        ];
    }
}