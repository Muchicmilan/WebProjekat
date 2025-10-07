<?php

namespace App\DataFixtures;

use App\Entity\Enums\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//Cela poenta ove klase je kako bi imali lak nacin za kreiranje admin role-a

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $uph) {
        $this->userPasswordHasher = $uph;
    } 
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setName("Admin");
        $user->setSurname('User');
        $user->setHeight('200');
        $user->setEmail("admin@admin.rs");
        $user->setRole(UserRole::ADMIN);

        $hashedPass = $this->userPasswordHasher->hashPassword(
            $user,
            'admin'
        );
        $user->setPassword($hashedPass);
        $manager->persist($user);
        $manager->flush();
    }
}
