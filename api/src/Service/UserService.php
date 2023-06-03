<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function getUsers(Request $request): array
    {
        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('items', 25);
        $totalItems = count($this->userRepository->findAll());
        $results = $this->userRepository->getUsersWithPagination((int)$itemsPerPage, (int)$page);
        $users = [];

        /** @var User $user */
        foreach ($results as $user) {
            $users[] = [
                'username' => $user->getUsername(),
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getProfile() ? $user->getProfile()->getFirstName() : '',
                'surname' => $user->getProfile() ? $user->getProfile()->getSurname() : ''
            ];
        }

        return [
            'users' => $users,
            'totalItems' => $totalItems,
            'page' => $page,
            'limit' => $itemsPerPage
        ];
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function addUser(Request $request): int
    {
        $content = json_decode($request->getContent(), true);
        $user = new User();

        $user->setUsername($content['username']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $content['password']));
        $user->setEmail($content['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }

    public function editUser(User $user, Request $request): void
    {
        $content = json_decode($request->getContent(), true);

        if (array_key_exists('email', $content)) {
            $user->setEmail($content['email']);
        }

        if (array_key_exists('username', $content)) {
            $user->setUsername($content['username']);
        }

        if (array_key_exists('password', $content)) {
            $user->setEmail($this->userPasswordHasher->hashPassword($user, $content['password']));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}