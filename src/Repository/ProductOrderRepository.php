<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\ProductOrder;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ProductOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductOrder[]    findAll()
 * @method ProductOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductOrder::class);
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDate(User $user, $month = null, $year = null)
    {
        if ($month === null) {
            $month = (int) date('m');
        }

        if ($year === null) {
            $year = (int) date('Y');
        }

        $startDate = new \DateTimeImmutable("$year-$month-01T00:00:00");
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        return $this->createQueryBuilder('po')
            ->andWhere('po.date BETWEEN :start AND :end')
            ->andWhere('po.subscriber = :subscriber')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('subscriber', $user)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findByPeriod(User $user, $startDate, $endDate = null)
    {
        $date1 = $startDate->format('Y-m-d');
        $date2 = $endDate != null ? $endDate->format('Y-m-d') : $date1;
        $startDate = new \DateTimeImmutable("$date1-01T00:00:00");
        $endDate = new \DateTimeImmutable("$date2-01T00:00:00");
        $endDate = date('Y-m-d', strtotime($date2 . '+1day')); //on ajoute un jour pour inclure le jour dans la recherche

        return $this->createQueryBuilder('po')
            ->andWhere('po.date BETWEEN :startDate AND :endDate')
            ->andWhere('po.subscriber = :subscriber')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('subscriber', $user)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDays(User $user, Order $order, $startDate, $endDate)
    {
        $startDate = new \DateTimeImmutable("$startDate-01T00:00:00");
        $endDate = new \DateTimeImmutable("$endDate-01T00:00:00");



        return $this->createQueryBuilder('po')
            ->andWhere('po.date BETWEEN :startDate AND :endDate')
            ->andWhere('po.command = :command')
            ->andWhere('po.subscriber = :subscriber')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('subscriber', $user)
            ->setParameter('command', $order)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDay(User $user, Order $order, $dateDay = null)
    {
        return $this->createQueryBuilder('po')
            ->andWhere('po.date LIKE :dateDay')
            ->andWhere('po.command = :command')
            ->andWhere('po.subscriber = :subscriber')
            ->setParameter('dateDay', "%$dateDay%")
            ->setParameter('subscriber', $user)
            ->setParameter('command', $order)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDateProduct(Product $product, $month = null, $year = null)
    {
        if ($month === null) {
            $month = (int) date('m');
        }

        if ($year === null) {
            $year = (int) date('Y');
        }

        $startDate = new \DateTimeImmutable("$year-$month-01T00:00:00");
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);
        //dd($endDate);

        return $this->createQueryBuilder('po')
            ->andWhere('po.date BETWEEN :start AND :end')
            ->andWhere('po.product = :product')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('product', $product)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $month
     * @param int $year
     * 
     * @return object[]
     */
    public function findByDateOrder($date)
    {

        return $this->createQueryBuilder('po')
            ->andWhere('po.date = :date')
            ->setParameter('date', $date)
            ->orderBy('po.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ProductOrder[] Returns an array of ProductOrder objects
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
    public function findOneBySomeField($value): ?ProductOrder
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
