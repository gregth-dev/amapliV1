<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProductOrderRepository;

class ProductOrderMaker
{
    private $productOrderRepository;

    public function __construct(ProductOrderRepository $productOrderRepository)
    {
        $this->productOrderRepository = $productOrderRepository;
    }

    public function getProductOrder(User $user, $month, $year)
    {
        $productOrders = $this->productOrderRepository->findByDate($user, $month, $year);
        $orders = [];
        foreach ($productOrders as $productOrder) {
            if ($productOrder->getCommand()->getContractMember()->getStatus() == 'actif') {
                $status = $productOrder->getStatus();
                if ($status)
                    $date = $productOrder->getDate()->format('d/m/Y') . " (" . $status . ") ";
                else
                    $date = $productOrder->getDate()->format('d/m/Y');
                $order = $productOrder->getCommand();
                $orders[$date][] = $order;
            }
        }
        $this->data = $orders;
        return $this->data;
    }
}
