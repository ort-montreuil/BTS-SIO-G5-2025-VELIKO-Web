<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Retrieves an array of Reservation objects for a given user email.
     *
     * @param string $email The email of the user whose reservations are to be retrieved.
     * @return array An array of Reservation objects ordered by date in ascending order.
     */
    public function getReservationsByUserEmail(string $email): array //Récupère les réservations d'un utilisateur par son email
    {
        // Crée un QueryBuilder pour la classe Reservation
        return $this->createQueryBuilder('r')
            // Ajoute une condition pour filtrer par email de l'utilisateur
            ->andWhere('r.emailUser = :email')
            // Définit le paramètre email avec la valeur fournie
            ->setParameter('email', $email)
            // Trie les résultats par date en ordre croissant
            ->orderBy('r.Date', 'ASC')
            // Exécute la requête et récupère les résultats
            ->getQuery()
            ->getResult();
    }

    public function deleteReservationById(int $idReservation) //Supprime une réservation par son ID
    {
        // Crée un QueryBuilder pour la classe Reservation
        return $this->createQueryBuilder('r')
            // Définit l'opération de suppression
            ->delete()
            // Ajoute une condition pour filtrer par ID de la réservation
            ->andWhere("r.id = :idReservation")
            // Définit le paramètre idReservation avec la valeur fournie
            ->setParameter("idReservation", $idReservation)
            // Exécute la requête de suppression
            ->getQuery()
            ->execute();
    }
//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
