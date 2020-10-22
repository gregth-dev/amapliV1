<?php

namespace App\Repository;

use DateTime;
use App\Entity\Payment;
use App\Entity\Producer;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * Trouve les paiements sur une période donnée avec un statut donné
     *
     * @param Producer $producer
     * @param Datetime $startDate
     * @param Datetime $endDate
     * @param string $status
     * @return Payment[]
     */
    public function findByPeriodByStatus(Producer $producer, $startDate, $endDate = null, $status = null)
    {
        if ($status == 'all')
            return $this->findByPeriod($producer, $startDate, $endDate);

        $date1 = $startDate->format('Y-m-d');
        $date2 = $endDate != null ? $endDate->format('Y-m-d') : $date1;
        $startDate = new \DateTimeImmutable("$date1-01T00:00:00");
        $endDate = new \DateTimeImmutable("$date2-01T00:00:00");
        $endDate = date('Y-m-d', strtotime($date2 . '+1day')); //on ajoute un jour pour inclure le jour dans la recherche

        return $this->createQueryBuilder('p')
            ->andWhere('p.depositDate BETWEEN :startDate AND :endDate')
            ->andWhere('p.producer = :producer')
            ->andWhere('p.status = :status')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('producer', $producer)
            ->setParameter('status', $status)
            ->orderBy('p.depositDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les paiements sur une période donnée avec un statut donné
     *
     * @param Producer $producer
     * @param Datetime $startDate
     * @param Datetime $endDate
     * @return Payment[]
     */
    public function findByPeriod(Producer $producer, $startDate, $endDate = null)
    {
        $date1 = $startDate->format('Y-m-d');
        $date2 = $endDate != null ? $endDate->format('Y-m-d') : $date1;
        $startDate = new \DateTimeImmutable("$date1-01T00:00:00");
        $endDate = new \DateTimeImmutable("$date2-01T00:00:00");
        $endDate = date('Y-m-d', strtotime($date2 . '+1day')); //on ajoute un jour pour inclure le jour dans la recherche

        return $this->createQueryBuilder('p')
            ->andWhere('p.depositDate BETWEEN :startDate AND :endDate')
            ->andWhere('p.producer = :producer')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('producer', $producer)
            ->orderBy('p.depositDate', 'ASC')
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Payment[] Returns an array of Payment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
