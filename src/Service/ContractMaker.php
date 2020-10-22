<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ContractMember;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ContractMemberRepository;

class ContractMaker
{
    private $manager;
    private $cmr;
    private $subscriberError;

    public function __construct(EntityManagerInterface $manager, ContractMemberRepository $cmr, OrderDeliveryMaker $odm)
    {
        $this->manager = $manager;
        $this->cmr = $cmr;
        $this->odm = $odm;
    }

    public function setMultipleContract(Product $product, ContractMember $contractMember)
    {
        $subscribers = $contractMember->getSubscribers()->toArray();
        $contract = $contractMember->getContract();
        foreach ($subscribers as $subscriber) {
            if (!$this->cmr->findBy(['contract' => $contractMember->getContract(), 'subscriber' => $subscriber])) {
                $newContractMember = new ContractMember();
                $newContractMember->setSubscriber($subscriber);
                $newContractMember->setContract($contract);
                $newContractMember->setTotalAmount(count($newContractMember->getContract()->getDeliveries()->toArray()) * $product->getPrice());
                $newContractMember->setBalance(count($newContractMember->getContract()->getDeliveries()->toArray()) * $product->getPrice());
                $this->odm->setOrderDeliveriesMultiple($product, $newContractMember);
                $this->manager->persist($newContractMember);
            }
        }
        $contract->setStatus('actif');
        $this->manager->flush();
    }

    public function getSubscriberError()
    {
        return $this->subscriberError;
    }

    /**
     * Set the value of subscriberError
     *
     * @return  self
     */
    public function setSubscriberError($subscriberError)
    {
        $this->subscriberError = $subscriberError;

        return $this;
    }
}
