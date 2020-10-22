<?php

namespace App\Repository;

use App\Entity\ContractMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContractMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContractMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContractMember[]    findAll()
 * @method ContractMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContractMember::class);
    }

    /**
     * @return ContractMember[] Returns an array of ContractMember objects
     */
    public function findIfExist(ContractMember $contractMember)
    {
        $idContrat = $contractMember->getContract()->getId();
        $idAdherent = $contractMember->getSubscriber()->getId();
        return $this->createQueryBuilder('c')
            ->andWhere('c.contrat = :contrat')
            ->andWhere('c.adherent = :adherent')
            ->setParameter('contrat', $idContrat)
            ->setParameter('member', $idAdherent)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ContractMember[] Returns an array of ContractMember objects
     */
    public function findUniqAdherent()
    {
        return $this->createQueryBuilder('c')
            ->groupBy('c.adherent')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ContractMember[] Returns an array of ContractMember objects
     */
    public function findByYearArchive($year, $limit = null, $offset = null)
    {

        return $this->createQueryBuilder('c')
            ->join('c.contract', 'cc')
            ->andWhere('c.status = :status')
            ->andWhere('cc.endDate LIKE :endDate')
            ->setParameter('status', 'archivé')
            ->setParameter('endDate', "%$year%")
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return ContractMember[] Returns an array of ContractMember objects
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
    public function findOneBySomeField($value): ?ContractMember
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
