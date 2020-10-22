<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Permanence;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Permanence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permanence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permanence[]    findAll()
 * @method Permanence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermanenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permanence::class);
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
        //dd($endDate);

        return $this->createQueryBuilder('p')
            ->andWhere('p.date BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPeriod($startDate, $endDate = null)
    {
        $date1 = $startDate->format('Y-m-d');
        $date2 = $endDate != null ? $endDate->format('Y-m-d') : $date1;
        $startDate = new \DateTimeImmutable("$date1-01T00:00:00");
        $endDate = new \DateTimeImmutable("$date2-01T00:00:00");
        $endDate = date('Y-m-d', strtotime($date2 . '+1day')); //on ajoute un jour pour inclure le jour dans la recherche

        return $this->createQueryBuilder('p')
            ->andWhere('p.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByParticipant(User $user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.participants', 'u')
            ->where('u = :participant')
            ->setParameter('participant', $user)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Permanence[] Returns an array of Permanence objects
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
    public function findOneBySomeField($value): ?Permanence
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
