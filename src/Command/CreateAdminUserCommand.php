<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Creates a new admin user.')
            ->setHelp('This command allows you to create an admin user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);

        // Set a plain password and hash it
        $plainPassword = 'adminpass';
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, $plainPassword);
        $adminUser->setPassword($hashedPassword);

        // Save the admin user to the database
        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $output->writeln('Admin user created successfully!');
        return Command::SUCCESS;
    }
}
