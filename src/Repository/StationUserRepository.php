<?php

namespace App\Repository;

use App\Entity\Station;
use App\Entity\StationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StationUser>
 *
 * @method StationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method StationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method StationUser[]    findAll()
 * @method StationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StationUser::class);
    }

    // Add custom methods here
    public function findStationsByUserId(int $userId)
    {
        return $this->createQueryBuilder('su')
            ->select("su.idStation")
            ->andWhere('su.idUser  = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }
    public function findStationNameById(int $stationId)
    {
        return $this->createQueryBuilder('su')
            ->select("s.name")
            ->innerJoin(Station::class, "s", 'WITH', "s.station_id = su.idStation") //Comprendre : https://blog.digital-craftsman.de/use-inner-join-in-doctrine-query-builder/
            ->andWhere("su.idStation = :stationId")
            ->setParameter("stationId", $stationId)
            ->getQuery()
            ->getResult();

    }
    public function deleteStationByStationId(int $stationId)
    {
        return $this->createQueryBuilder('su')
            ->delete()
            ->andWhere("su.idStation = :stationId")
            ->setParameter("stationId", $stationId)
            ->getQuery()
            ->execute();
    }
//    /**
//     * @return StationUser[] Returns an array of StationUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StationUser
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
