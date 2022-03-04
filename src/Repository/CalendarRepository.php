<?php

namespace App\Repository;

use App\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Calendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendar[]    findAll()
 * @method Calendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }



    /**
     * Calcule du nombres de dates par mois pour l annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findDateMonth(): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->addSelect('MONTH(c.start) as month ,SUM(DATE_DIFF(c.end,c.start)) as total')
            ->andWhere('YEAR(c.start) = YEAR(:dateNow)')
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Calcule le nombre de dates par mois pour l'annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findStats($id): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->addSelect('MONTH(c.start) as month ,  SUM(DATE_DIFF(c.end,c.start))  as total')
            ->where('c.teacher = :id')
            ->andWhere('YEAR(c.createdAt) = YEAR(:dateNow)')
            ->setParameter('id', $id)
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Calcule du nombres de dates multiplier par le taux Jr de Payment par mois pour l annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function totalPaymentForTeacherByMonth($id): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->addSelect('MONTH(c.start) as month , SUM(DATE_DIFF(c.end,c.start)) as total')
            ->where('c.teacher = :id')
            ->andWhere('YEAR(c.start) = YEAR(:dateNow)')
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->setParameter('id', $id)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule du nombres de dates multiplier par le taux Jr de Payment pour le mois en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function totalPaymentForMonth($id): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->addSelect('MONTH(c.start) as month , SUM(DATE_DIFF(c.end,c.start)) as total')
            ->where('c.teacher = :id')
            ->andWhere('MONTH(c.start) = MONTH(:dateNow)')
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->setParameter('id', $id)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }




    /**
     * Calcule du nombres de dates multiplier par le taux Jr de Payment par mois pour l annéé en cour
     * @return int|mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function totalcalendar(): array
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->addSelect('SUM(DATE_DIFF(c.end,c.start)) as total')
            ->andWhere('YEAR(c.start) = YEAR(:dateNow)')
            ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Calendar[] Returns an array of Calendar objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Calendar
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /*
    public function findByNot($fiel, $value): ?Calendar
    {
      $qb = $this->createQueryBuilder('c');
    $qb->where($qb->expr()->not($qb->expr()->eq('c.'.$field, '?indispobible')));
    $qb->setParameter(1, $value);

    return $qb->getQuery()
        ->getResult();
    }
    */
}
