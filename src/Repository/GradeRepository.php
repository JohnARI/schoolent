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
           return $this->createQueryBuilder('g')
              ->addSelect('MONTH(g.createdAt) as month , SUM(g.grade)/ COUNT(g.grade) as total')
              ->where('g.user = :id')
              ->andWhere('YEAR(g.createdAt) = YEAR(:dateNow)')
              ->setParameter('id', $id)
              ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
              ->groupBy('month')
              ->orderBy('month', 'ASC')
              ->getQuery()
              ->getResult()
           ;
     }

     /**
      * @return int|mixed
      * @throws Exception
      * @throws \Doctrine\DBAL\Exception
      */
      public function findGradeByUser($studentId): array
      {
          return $this->createQueryBuilder('g')
          ->where('g.user = :user')
          ->setParameter('user', $studentId)
          ->getQuery()
          ->getResult();
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
        return $this->createQueryBuilder('g')
            ->addSelect('SUM(g.grade)/ COUNT(g.grade) as total')
            ->where('g.user = :id')
            ->andWhere('YEAR(g.createdAt) = YEAR(:dateNow)')
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
        return $this->createQueryBuilder('g')
            ->addSelect('SUM(g.grade)/ COUNT(g.grade) as total')
            ->join('g.user','u')
            ->where('u.session = :session')
            ->andWhere('YEAR(g.createdAt) = YEAR(:dateNow)')
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
        return $this->createQueryBuilder('g')
            ->addSelect('COUNT(g.category) as category ,SUM(g.grade)/ COUNT(g.grade) as total')
            ->join('g.user','u')
            ->where('u.session = :session')
            ->andWhere('YEAR(g.createdAt) = YEAR(:dateNow)')
            ->setParameter('session', $session)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('g.category')
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
        return $this->createQueryBuilder('g')
            ->addSelect('COUNT(g.category) as category ,SUM(g.grade)/ COUNT(g.grade) as total')
            ->where('g.user = :id')
            ->andWhere('YEAR(g.createdAt) = YEAR(:dateNow)')
            ->setParameter('id', $id)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('g.category')
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
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grade
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
