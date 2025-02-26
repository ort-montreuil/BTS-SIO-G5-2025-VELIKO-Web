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
    public function findStationsByUserId(int $userId) //Récupère les stations d'un utilisateur
    {
        // Crée un QueryBuilder pour la classe StationUser
        return $this->createQueryBuilder('su')
            // Sélectionne uniquement l'ID de la station
            ->select("su.idStation")
            // Ajoute une condition pour filtrer par ID de l'utilisateur
            ->andWhere('su.idUser  = :userId')
            // Définit le paramètre userId avec la valeur fournie
            ->setParameter('userId', $userId)
            // Exécute la requête et récupère les résultats sous forme de tableau
            ->getQuery()
            ->getArrayResult();
    }

    public function findStationNameById(int $stationId) //Récupère le nom d'une station par son ID
    {
        // Crée un QueryBuilder pour la classe StationUser
        return $this->createQueryBuilder('su')
            // Sélectionne uniquement le nom de la station
            ->select("s.name")
            // Effectue une jointure interne avec la classe Station
            ->innerJoin(Station::class, "s", 'WITH', "s.station_id = su.idStation") //Comprendre : https://blog.digital-craftsman.de/use-inner-join-in-doctrine-query-builder/
            // Ajoute une condition pour filtrer par ID de la station
            ->andWhere("su.idStation = :stationId")
            // Définit le paramètre stationId avec la valeur fournie
            ->setParameter("stationId", $stationId)
            // Exécute la requête et récupère les résultats
            ->getQuery()
            ->getResult();
    }

    public function deleteStationByStationId(int $stationId) //Supprime une station par son ID
    {
        // Crée un QueryBuilder pour la classe StationUser
        return $this->createQueryBuilder('su')
            // Définit l'opération de suppression
            ->delete()
            // Ajoute une condition pour filtrer par ID de la station
            ->andWhere("su.idStation = :stationId")
            // Définit le paramètre stationId avec la valeur fournie
            ->setParameter("stationId", $stationId)
            // Exécute la requête de suppression
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
