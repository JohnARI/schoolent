<?php

namespace App\Repository;


use Exception;
use App\Entity\Grade;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    // /**
    //  * Compte le nombre de note par mois
    //  * @return int|mixed
    //  * @throws Exception
    //  * @throws \Doctrine\DBAL\Exception
    //  */
    // public function findStats($id): array
    // {
    //     $conn = $this->getEntityManager()->getConnection();

    //     $sql = '
    //         SELECT MONTH(created_at) as month ,SUM(grade) / count(*) as total 
    //         FROM grade 
    //         WHERE YEAR(created_at) = YEAR(CURDATE())
    //         AND (user_id) = '.$id.'
    //         GROUP BY MONTH(created_at) 
    //         ORDER BY MONTH(created_at) ASC
    //         ';
    //     $stmt = $conn->prepare($sql);
    //     $stmt->execute();

    //     // returns an array of arrays (i.e. a raw data set)
    //     return $stmt->fetchAllAssociative();
    // }

     /**
      * Calcule la moyenne general par mois pour l annéé en cour
      * @return int|mixed
      * @throws Exception
      * @throws \Doctrine\DBAL\Exception
      */
       public function findStats($id): array
       {
           $date = new \DateTime();
           return $this->createQueryBuilder('r')
              ->addSelect('MONTH(r.createdAt) as month , SUM(r.grade)/ COUNT(r.grade) as total')
              ->where('r.user = :id')
              ->andWhere('YEAR(r.createdAt) = YEAR(:dateNow)')
              ->setParameter('id', $id)
              ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
              ->groupBy('month')
              ->orderBy('month', 'ASC')
              ->getQuery()
              ->getResult()
           ;
     }


    /**
     * Calcule la moyenne general pour l annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotal($id): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('r')
            ->addSelect('SUM(r.grade)/ COUNT(r.grade) as total')
            ->where('r.user = :id')
            ->andWhere('YEAR(r.createdAt) = YEAR(:dateNow)')
            ->setParameter('id', $id)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Calcule la moyenne general de la session de l utilisateur pour l annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotalRatinsByClassRoom($session): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('r')
            ->addSelect('SUM(r.grade)/ COUNT(r.grade) as total')
            ->join('r.user','u')
            ->where('u.session = :session')
            ->andWhere('YEAR(r.createdAt) = YEAR(:dateNow)')
            ->setParameter('session', $session)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->getQuery()
            ->getResult()
        ;
    }

   
    
  
     /**
     * Calcule la moyenne general pour l annéé en cour par category 
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotalByClassRoom($session): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('r')
            ->addSelect('COUNT(r.category) as category ,SUM(r.grade)/ COUNT(r.grade) as total')
            ->join('r.user','u')
            ->where('u.session = :session')
            ->andWhere('YEAR(r.createdAt) = YEAR(:dateNow)')
            ->setParameter('session', $session)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('r.category')
            ->getQuery()
            ->getResult()
        ;
    }

     /**
     * Calcule la moyenne general pour l annéé en cour par category 
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findTotalByCategory($id): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('r')
            ->addSelect('COUNT(r.category) as category ,SUM(r.grade)/ COUNT(r.grade) as total')
            ->where('r.user = :id')
            ->andWhere('YEAR(r.createdAt) = YEAR(:dateNow)')
            ->setParameter('id', $id)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('r.category')
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Grade[] Returns an array of Grade objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grade
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
