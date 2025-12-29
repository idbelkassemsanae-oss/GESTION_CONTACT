<?php
// src/DataFixtures/ContactFixtures.php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $contact = new Contact();
            $contact->setNom($faker->lastName());
            $contact->setPrenom($faker->firstName());
            $contact->setEmail($faker->email());
            $contact->setTelephone($faker->phoneNumber());
            $contact->setAdresse($faker->address());
            
            // 70% des contacts avec une date de modification aléatoire
            if (rand(0, 100) > 30) {
                $modificationDate = clone $contact->getDateCreation();
                $modificationDate->modify('+'.rand(1, 30).' days');
                $contact->setDateModification($modificationDate);
            }

            $manager->persist($contact);
        }

        $manager->flush();
    }
}