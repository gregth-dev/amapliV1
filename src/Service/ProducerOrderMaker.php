<?php

namespace App\Service;

use App\Entity\Producer;
use App\Entity\User;
use App\Repository\ProducerRepository;
use App\Repository\ProductOrderRepository;
use App\Repository\ContractRepository;

class ProducerOrderMaker
{

    private $productOrderRepository;
    private $producerRepository;
    private $contractRepository;

    public function __construct(ProductOrderRepository $productOrderRepository, ProducerRepository $producerRepository, ContractRepository $contractRepository)
    {
        $this->productOrderRepository = $productOrderRepository;
        $this->producerRepository = $producerRepository;
        $this->contractRepository = $contractRepository;
    }

    /**
     * Affiche le nombre de product à distribuer par mois et année
     *
     * @param User $user
     * @param string $month
     * @param string $annee
     * @return array|null
     */
    public function getListOrders(User $user, $month = null, $year = null)
    {
        $producer = $this->producerRepository->findOneBy(['user' => $user]);
        if ($contract = $this->contractRepository->findOneByActiveYear($year, $producer)) {
            $contractMembers = $contract->getContractMembers();
            // On récupère tous les adhérents de chaque contract adhérent actif lié au contract producteur
            $members = [];
            foreach ($contractMembers as $contractMember) {
                if ($contractMember->getStatus() == 'actif')
                    $members[] = $contractMember->getSubscriber();
            }
            // On récupère chaque order de chaque adhérent en fonction de la date
            $Orders = [];
            foreach ($members as $member) {
                $productOrders = $this->productOrderRepository->findByDate($member, $month, $year);
                foreach ($productOrders as $productOrder) {
                    $status = $productOrder->getStatus();
                    if ($status)
                        $date = $productOrder->getDate()->format('d/m/Y') . " (" . $status . ") ";
                    else
                        $date = $productOrder->getDate()->format('d/m/Y');
                    $order = $productOrder->getCommand();
                    $Orders[$date][] = $order;
                }
            }
            // On récupère les products du producteur
            $productNameDetails = [];
            $products = $producer->getProducts()->toArray();
            foreach ($products as $product) {
                $productNameDetails[] = $product->getFullName();
            }
            // On créé la liste des products et des quantités en fonction de la date des Orders
            $list = [];
            foreach ($Orders as $date => $order) {
                $filterName = [];
                foreach ($order as $c) {
                    $productNameDetail = $c->getProduct()->getFullName();
                    if (in_array($productNameDetail, $productNameDetails)) {
                        //pour chaque quantité on ajoute le nom du product au tableau
                        for ($i = 0; $i < $c->getQuantity(); $i++) {
                            $filterName[] = $productNameDetail;
                        }
                        //Renvoie un tableau associatif nomDuProduct => nombre d'occurence de nomDuProduct
                        $productQuantities = array_count_values($filterName);
                        //on associe la date et la quantité
                        $list[$date] = $productQuantities;
                    }
                }
            }
            return $list;
        }
        return false;
    }


    /**
     * Undocumented function
     *
     * @param Producer $producer
     * @param Datetime $startDate
     * @param Datetime $endDate
     * @return array
     */
    public function getListOrdersByPeriode(Producer $producer, $startDate, $endDate): array
    {
        $contracts = $this->contractRepository->findBy(['producer' => $producer]);
        $members = [];
        foreach ($contracts as $contract) {
            foreach ($contract->getContractMembers()->toArray() as $contractMember) {
                if ($contractMember->getStatus() == 'actif')
                    $members[] = $contractMember->getSubscriber();
            }
        }
        // On récupère tous les adhérents de chaque contract adhérent lié au contract producteur
        // On récupère chaque order de chaque adhérent en fonction de la date
        $Orders = [];
        foreach ($members as $adherent) {
            $productOrders = $this->productOrderRepository->findByPeriod($adherent, $startDate, $endDate);
            foreach ($productOrders as $productOrder) {
                $status = $productOrder->getStatus();
                if ($status)
                    $date = $productOrder->getDate()->format('d/m/Y') . " (" . $status . ") ";
                else
                    $date = $productOrder->getDate()->format('d/m/Y');
                $order = $productOrder->getCommand();
                $Orders[$date][] = $order;
            }
        }
        // On récupère les produits du producteur
        $productNameDetails = [];
        $products = $producer->getProducts()->toArray();
        foreach ($products as $product) {
            $productNameDetails[] = $product->getFullName();
        }
        // On créé la liste des produits et des quantités en fonction de la date des Orders
        $list = [];
        foreach ($Orders as $date => $order) {
            $filterName = [];
            foreach ($order as $o) {
                $productNameDetail = $o->getProduct()->getFullName();
                if (in_array($productNameDetail, $productNameDetails)) {
                    //pour chaque quantité on ajoute le nom du product au tableau
                    for ($i = 0; $i < $o->getQuantity(); $i++) {
                        $filterName[] = $productNameDetail;
                    }
                    //Renvoie un tableau associatif nomDuProduct => nombre d'occurence de nomDuProduct
                    $productQuantities = array_count_values($filterName);
                    //on associe la date et la quantité
                    $list[$date] = $productQuantities;
                }
            }
        }
        return $list;
    }
}
