<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Please run php bin/console app:create-admin --email="user" --password="123456789" for create admin user
 */

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:create-admin')
            ->setDescription('Creates a new admin user.')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the admin user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password of the admin user')
            ->setHelp('This command allows you to create a new admin user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln([
            'Admin User Creator',
            '==================',
            '',
        ]);

        $email = $input->getOption('email');
        $password = $input->getOption('password');

        if (!$email) {
            $emailQuestion = new Question('Enter the admin email: ');
            $email = $helper->ask($input, $output, $emailQuestion);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $output->writeln('<error>Invalid email address!</error>');
            return Command::FAILURE;
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $output->writeln('<error>An account with this email already exists!</error>');
            return Command::FAILURE;
        }

        if (!$password) {
            $passwordQuestion = new Question('Enter the admin password: ');
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $passwordQuestion);
        }

        if (strlen($password) < 8) {
            $output->writeln('<error>Password must be at least 8 characters long!</error>');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setVerified(true);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $output->writeln('<error>Error creating the admin user: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('Admin user successfully created!');

        return Command::SUCCESS;
    }
}