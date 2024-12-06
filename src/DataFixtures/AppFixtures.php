<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Station;
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

    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Appel de la méthode qui charge les utilisateurs
        $this->loadUsers($manager);
        $this->loadReservations($manager);
        $manager->flush();
    }
    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user-admin-@gmail.dev");
        $user->setPassword($this->hasher->hashPassword($user, "Bonjour12345!"));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setNom("nom-admin");
        $user->setPrenom("prenom-admin");
        $user->setDateNaissance(new \DateTime());
        $user->setAdresse("adresse-admin");
        $user->setCodePostal("92100");
        $user->setLastPasswordChanged(new \DateTime());
        $user->setVille("ville-admin");
        $user->setVerified(true);
        $user->setBlocked(false);
        $user->setMustChangePassword(false);


        $manager->persist($user);
        for ($i=1; $i<=10; $i++)
        {
            $user = new User();
            $user->setEmail("user-$i@gmail.dev");
            $user->setPassword($this->hasher->hashPassword($user, "Bonjour12345!"));
            $user->setRoles(["ROLE_USER"]);
            $user->setNom("nom-$i");
            $user->setPrenom("prenom-$i");
            $user->setDateNaissance(new \DateTime());
            $user->setAdresse("adresse-$i");
            $user->setCodePostal("92100");
            $user->setLastPasswordChanged(new \DateTime());
            $user->setVille("ville-$i");
            $user->setVerified(true);
            $user->setBlocked(false);
            $user->setMustChangePassword(false);
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function loadReservations(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $reservation = new Reservation();
            $reservation->setEmailUser("user-$i@gmail.dev");
            $reservation->setDate((new \DateTime())->setTimestamp(mt_rand(strtotime('2010-01-01'), strtotime('2024-12-31'))));            $reservation->setHeureDebut((new \DateTime())->setTimestamp(mt_rand(1, time())));
            $reservation->setHeureFin((new \DateTime())->setTimestamp(mt_rand(1, time())));
            $reservation->setIdStationDepart($i); // Set integer value
            $reservation->setIdStationArrivee($i + 1); // Set integer value
            $reservation->setId($i);
            $manager->persist($reservation);
        }
        $manager->flush();
    }
}
