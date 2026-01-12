<?php
// src/DataFixtures/UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@contactmanager.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Administrateur');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@contactmanager.com');
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        
        $manager->persist($user);

        $manager->flush();
    }
}