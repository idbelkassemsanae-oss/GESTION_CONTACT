<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setNom('Administrateur');
        $user->setPrenom('System');
        $user->setRoles(['ROLE_ADMIN']);
        
        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'admin123' // Changez ce mot de passe
        );
        $user->setPassword($hashedPassword);
        
        // Date de création sera automatique grâce au PrePersist
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        $output->writeln('<info>✅ Utilisateur admin créé avec succès !</info>');
        $output->writeln('<comment>Email: admin@example.com</comment>');
        $output->writeln('<comment>Mot de passe: admin123</comment>');
        $output->writeln('<comment>⚠️ Changez le mot de passe après la première connexion !</comment>');
        
        return Command::SUCCESS;
    }
}