<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', "%$role%")
            ->getQuery()
            ->getResult();
    }

    public function findByRoleGroup()
    {
        return $this->createQueryBuilder('u')
            ->addSelect('COUNT(u) as total')
            ->groupBy('u.roles')
            ->getQuery()
            ->getResult();
    }

    public function findBySession(string $role,$session)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->andWhere('u.session = :session')
            ->setParameter('role', "%$role%")
            ->setParameter('session', $session)
            ->getQuery()
            ->getResult();
    }

    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email LIKE :email')
            ->setParameter('email', "%$email%")
            ->getQuery()
            ->getResult();
    }


    /**
      * Calcule nombre d utilisateurs par mois pour l annéé en cour
      * @return int|mixed
      * @throws Exception
      * @throws \Doctrine\DBAL\Exception
      */
      public function findUserByMonth(): array
      {
          $date = new \DateTime();
          return $this->createQueryBuilder('u')
             ->addSelect('MONTH(u.createdAt) as month , count(u) as total')
             ->andWhere('YEAR(u.createdAt) = YEAR(:dateNow)')
             ->setParameter('dateNow', $date->format('Y-m-d 00:00:00'))
             ->groupBy('month')
             ->orderBy('month', 'ASC')
             ->getQuery()
             ->getResult()
          ;
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}