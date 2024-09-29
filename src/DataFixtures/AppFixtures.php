<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        //c'est normal si c'est vide
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Appel de la méthode qui charge les utilisateurs
        $this->loadUsers($manager);
        $manager->flush();
    }
    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user-admin-@gmail.dev");
        $user->setPassword($this->hasher->hashPassword($user, "password"));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setUsername("user-admin");
        $user->setNom("nom-admin");
        $user->setPrenom("prenom-admin");
        $user->setDateNaissance(new \DateTime());
        $user->setAdresse("adresse-admin");
        $user->setCodePostal("92100");
        $user->setLastPasswordChanged(new \DateTime());
        $user->setVille("ville-admin");
        $manager->persist($user);
        for ($i=1; $i<=10; $i++)
        {
            $user = new User();
            $user->setEmail("user-$i@gmail.dev");
            $user->setPassword($this->hasher->hashPassword($user, "password"));
            $user->setRoles(["ROLE_USER"]);
            $user->setUsername("user-$i");
            $user->setNom("nom-$i");
            $user->setPrenom("prenom-$i");
            $user->setDateNaissance(new \DateTime());
            $user->setAdresse("adresse-$i");
            $user->setCodePostal("92100");
            $user->setLastPasswordChanged(new \DateTime());
            $user->setVille("ville-$i");
            $manager->persist($user);
        }
        $manager->flush();
    }
}
