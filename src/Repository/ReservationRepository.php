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
    public function getReservationsByUserEmail(string $email): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.emailUser = :email')
            ->setParameter('email', $email)
            ->orderBy('r.Date', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function deleteReservationById(int $idReservation)
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->andWhere("r.id = :idReservation")
            ->setParameter("idReservation", $idReservation)
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
