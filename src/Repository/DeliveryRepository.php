<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Delivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Delivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Delivery[]    findAll()
 * @method Delivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDate($month = null, $year = null)
    {
        if ($month === null) {
            $month = (int) date('m');
        }

        if ($year === null) {
            $year = (int) date('Y');
        }

        $startDate = new \DateTimeImmutable("$year-$month-01T00:00:00");
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        return $this->createQueryBuilder('d')
            ->andWhere('d.date BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('d.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByPeriod(Contract $contract, $startDate, $endDate = null)
    {
        $date1 = $startDate->format('Y-m-d');
        $date2 = $endDate != null ? $endDate->format('Y-m-d') : $date1;
        $startDate = new \DateTimeImmutable("$date1-01T00:00:00");
        $endDate = new \DateTimeImmutable("$date2-01T00:00:00");
        $endDate = date('Y-m-d', strtotime($date2 . '+1day')); //on ajoute un jour pour inclure le jour dans la recherche

        return $this->createQueryBuilder('d')
            ->andWhere('d.date BETWEEN :start AND :end')
            ->andWhere('d.contract = :contract')
            ->andWhere('d.status = :status')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('contract', $contract)
            ->setParameter('status', 'Validée')
            ->orderBy('d.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Delivery[] Returns an array of Delivery objects
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
    public function findOneBySomeField($value): ?Delivery
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
