<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Producer;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    /**
     * @return Contract[] Returns an array of Contrat objects
     */
    public function findByActiveYear($year)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate like :date')
            ->andWhere('c.status != :status')
            ->setParameter('date', "%$year%")
            ->setParameter('status', 'archivé')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Contract[] Returns an array of Contrat objects
     */
    public function findByArchiveYear($year)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate like :date')
            ->andWhere('c.status = :status')
            ->setParameter('date', "%$year%")
            ->setParameter('status', 'archivé')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Retourne un contrat en fonction du producer et de l'année
     *
     * @param string $year
     * @param Producer $producer
     * @return Contract|null
     */
    public function findOneByActiveYear($year = null, Producer $producer): ?Contract
    {
        if (!$year)
            $year = date('Y', time());

        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate like :startDate')
            ->orWhere('c.endDate like :endDate')
            ->andWhere('c.producer = :producer')
            ->andWhere('c.status = :status')
            ->setParameter('startDate', "%$year%")
            ->setParameter('endDate', "%$year%")
            ->setParameter('status', 'actif')
            ->setParameter('producer', $producer)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Undocumented function
     *
     * @param Producer $producer
     * @param Datetime $startDate
     * @param Datetime $endDate
     * @return Contract[]|null Returns an array of Contrat objects
     */
    public function findByActivePeriod(Producer $producer, $startDate, $endDate): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate BETWEEN :startDate AND :endDate')
            ->andWhere('c.producer = :producer')
            ->andWhere('c.status != :status')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', 'archivé')
            ->setParameter('producer', $producer)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Contract[] Returns an array of Contract objects
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
    public function findOneBySomeField($value): ?Contract
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
