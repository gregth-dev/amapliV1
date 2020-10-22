<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\ContractMember;
use Doctrine\ORM\EntityManagerInterface;

class ContractMemberMaker
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function updateBalance(Contract $contract)
    {
        foreach ($contract->getContractMembers() as $contractMember) {
            $amountOrders = $contractMember->getAmountOrders();
            $amountActivePayment = $contractMember->getAmountActivePayments();
            $amountDeposit = $contractMember->getAmountDeposit();
            $contractMember->setBalance(($amountOrders + $amountDeposit) - $amountActivePayment);
            $this->manager->persist($contractMember);
        }
        $this->manager->flush();
    }
}
