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
use Symfony\Component\Uid\Uuid;

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
        $searchTerm = $request->get('searchTerm', null);
        $totalItems = count($this->userRepository->findAll());
        $results = $this->userRepository->getUsersWithPagination((int)$itemsPerPage, (int)$page, $searchTerm);
        $users = [];

        /** @var User $user */
        foreach ($results as $user) {
            $users[] = $this->createUserArray($user, false);
        }

        return [
            'users' => $users,
            'maxResults' => $totalItems
        ];
    }

    public function getUser(Uuid $userId): ?array
    {
        $user  = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            return null;
        }

        return $this->createUserArray($user, true);
    }

    public function deleteUser(Uuid $userId): bool
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            return false;
        }

        $user->setIsDeleted(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    public function addUser(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);
        $user = new User();
        $user  = $this->objectCreator($user, $content);

        if (!$user) {
            return null;
        }

        $roleAdmin = $this->roleRepository->findOneBy(['role' => Role::ROLE_ACCESS_ADMIN_PANEL]);
        $user->addRole($roleAdmin);
        $user->setIsDeleted(false);
        $user->setIsVerified(true);

        if (array_key_exists('phoneNumber', $content)) {
            $user->setPhoneNumber($content['phoneNumber']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->createUserArray($user, true);
    }

    public function editUser(Uuid $userId, Request $request): ?array
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            return null;
        }

        $content = json_decode($request->getContent(), true);
        $user = $this->objectCreator($user, $content);

        if (!$user) {
            return null;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->createUserArray($user, true);
    }

    public function changePassword(Uuid $userId, Request $request): bool
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            return false;
        }

        $content = json_decode($request->getContent(), true);

        if (!array_key_exists('newPassword', $content) || !array_key_exists('oldPassword', $content)) {
            return false;
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $content['oldPassword'])) {
            return false;
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $content['newPassword']));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    public function getAvailableRoles(): array
    {
        $roles = $this->roleRepository->getUserRoles();
        $rolesArray = [];

        foreach ($roles as $role) {
            $rolesArray[] = [
                'id' => $role->getId(),
                'role' => $role->getRole(),
                'name' => $role->getName()
            ];
        }

        return $rolesArray;
    }

    public function editRoles(Uuid $userId, Request $request): bool
    {
        $user = $this->roleRepository->findOneBy(['id' => $userId]);
        $content= json_decode($request->getContent(), true);

        if (!array_key_exists('rolesIds', $content)) {
            return false;
        }

        $rolesIds  = $content['rolesIds'];
        $roles = $this->roleRepository->findAll();
        $roleIdsArray = array_map(function ($role) {
            return $role->getId();
        }, $roles);

        $userRoles = $user->getObjRoles();

        /** @var Role $userRole */
        foreach ($userRoles as $userRole) {
            if (!in_array($userRole->getId(), $rolesIds, true)) {
                $user->removeRole($userRole);
            }
        }

        foreach ($rolesIds as $roleId) {
            if (in_array($roleId, $roleIdsArray, true)) {
                $role = $roles[array_search($roleId, $roleIdsArray, true)];
                if ($role && !$user->hasObjRole($role)) {
                    $user->addRole($role);
                }
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    private function objectCreator(User $user, array $content): ?User
    {
        foreach ($content as $fieldName => $fieldValue) {
            if (property_exists(User::class, $fieldName)) {
                $setterMethod = 'set' . ucfirst($fieldName);
    
                if ($setterMethod == 'setPassword') {
                    $repeatedPassword = $content['repeatedPassword'];

                    if ($fieldValue != $repeatedPassword) {
                        return null;
                    }

                    $user->setPassword($this->userPasswordHasher->hashPassword($user, $fieldValue));
                } elseif ($fieldName === 'roles' && is_array($fieldValue)) {
                    $user->clearRoles();
                    $roles  = [];

                    foreach ($fieldValue as $uuid) {
                        $roles[] = $this->roleRepository->findOneBy(['id' => $uuid]);
                    }

                    foreach ($roles as $role) {
                        $user->addRole($role);
                    }
                } elseif (method_exists($user, $setterMethod)) {
                    $user->$setterMethod($fieldValue);
                }
            }
        }
    
        return $user;
    }

    private function createUserArray(User $user, bool $details = false): array
    {
        $userArray = [
            'username' => $user->getUsername(),
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'phoneNumber' => $user->getPhoneNumber(),
            'twoFactorAuth' => (bool)$user->isEmailAuthEnabled(),
            'isVerified' => (bool)$user->isVerified(),
            'isActive' => (bool)!$user->isDeleted()
        ];

        if ($details) {
            $roles = $user->getObjRoles();
            $rolesArray  = [];

            /** @var Role $role */
            foreach ($roles as $role) {
                $rolesArray[] = [
                    'name' => $role->getName(),
                    'role' => $role->getRole(),
                    'id' => $role->getId()
                ];
            }

            $userArray['roles'] = $rolesArray;
        }

        return $userArray;
    }
}