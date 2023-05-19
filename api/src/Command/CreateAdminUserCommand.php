<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user:admin',
    description: 'This command allow you to create Admin user.',
)]
class CreateAdminUserCommand extends Command
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('email', InputArgument::REQUIRED, 'User e-mail')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $roleAdmin = $this->roleRepository->findOneBy(['role' => Role::ROLE_ADMIN]);
        $roleAccessAdminPanel = $this->roleRepository->findOneBy(['role' => Role::ROLE_ACCESS_ADMIN_PANEL]);
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the user password: ');
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = empty($input->getArgument('password')) ?
            $helper->ask($input, $output, $question) : $input->getArgument('password');

        if ($password === null) {
            $io->error('Password cant be null');

            return Command::FAILURE;
        }

        try {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $user->setIsVerified(true);
            $user->addRole($roleAdmin);
            $user->addRole($roleAccessAdminPanel);
            $user->setEmailAuth(false);

            $this->userRepository->save($user, true);

            $io->success('User with username: ' . $username . ' was successfully created');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error('An exception occurred during execution of command: ' . $exception->getMessage());

            return Command::FAILURE;
        }
    }
}