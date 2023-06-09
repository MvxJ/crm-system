<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private RoleRepository $roleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        RoleRepository $roleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->roleRepository = $roleRepository;
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
        $roleAdmin = $this->roleRepository->findOneBy(['role' => Role::ROLE_ADMIN]);
        $content = json_decode($request->getContent(), true);
        $user = new User();

        $user->setUsername($content['username']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $content['password']));
        $user->setEmail($content['email']);
        $user->addRole($roleAdmin);

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
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $content['password']));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}