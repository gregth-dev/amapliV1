<?php

namespace App\Repository;

use App\Entity\DonationPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DonationPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonationPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonationPayment[]    findAll()
 * @method DonationPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonationPayment::class);
    }

    // /**
    //  * @return DonationPayment[] Returns an array of DonationPayment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DonationPayment
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
