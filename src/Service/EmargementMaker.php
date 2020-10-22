<?php

namespace App\Service;


use App\Entity\Emargement;
use App\Repository\ContractRepository;
use App\Repository\DeliveryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProducerRepository;
use App\Repository\ProductOrderRepository;

class EmargementMaker
{

    private $por;
    private $pr;
    private $cr;
    private $dr;
    private $or;

    public function __construct(ProductOrderRepository $por, ProducerRepository $pr, ContractRepository $cr, DeliveryRepository $dr, OrderRepository $or)
    {
        $this->por = $por;
        $this->pr = $pr;
        $this->cr = $cr;
        $this->dr = $dr;
        $this->or = $or;
    }


    /**
     * Créé le tableau de données pour la feuille d'émargement
     *
     * @param Emargement $emargement
     * @return array
     */
    public function setEmargement(Emargement $emargement): array
    {
        $contract = $emargement->getContract();
        $producer = $contract->getProducer();
        $contracts = $this->cr->findBy(['producer' => $producer, 'status' => 'actif'], ['createdAt' => 'DESC']);
        $contractMembers = [];
        //on récupère tous les contrats du producteur
        foreach ($contracts as $contract) {
            foreach ($contract->getContractMembers() as $contractMember) {
                if ($contractMember->getStatus() == 'actif') {
                    //on récupère les orders
                    foreach ($this->or->findBy(['contractMember' => $contractMember]) as $order) {
                        //on boucle sur les productOrders
                        foreach ($order->getProductOrders() as $productOrder) {
                            $contractMembers[$contractMember->getSubscriber()->getFullName() . ' ' . $contractMember->getSubscriber()->getPhone1()][$productOrder->getDate()->format('Y-m-d')][$order->getProduct()->getFullName()] = $order->getQuantity();
                        }
                    }
                }
            }
        }
        $dates = [];
        foreach ($this->dr->findByPeriod($contract, $emargement->getStartDate(), $emargement->getEndDate()) as $date) {
            $dates[] = $date->getDate()->format('Y-m-d');
        }

        return ['list' => $contractMembers, 'dates' => $dates, 'producer' => $producer->getName()];
    }
}
