<?php

namespace Zitro\Blog\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zitro\Blog\Entity\User;

class AppFixtures extends Fixture {

    public function load(ObjectManager $manager)
    {
        $password = "password";

        for ($u = 0; $u < 10;  $u++) {

            $user = new User();
            $user->setUsername("Utilisateur n°$u");
            $user->setEmail("Email n°$u");

            $hashPassword = password_hash($password, PASSWORD_BCRYPT);

            $user->setPassword($hashPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}