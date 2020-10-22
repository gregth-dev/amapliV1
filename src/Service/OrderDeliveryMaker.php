<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Contract;
use App\Entity\ProductOrder;
use App\Entity\ContractMember;
use App\Repository\ProductRepository;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductOrderRepository;

class OrderDeliveryMaker
{

    private $manager;
    private $productRepository;
    private $deliveryRepository;
    private $productOrderRepository;

    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepository, DeliveryRepository $deliveryRepository, productOrderRepository $productOrderRepository)
    {
        $this->manager = $manager;
        $this->productRepository = $productRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->productOrderRepository = $productOrderRepository;
    }

    public function setOrderDeliveries(array $orders, ContractMember $contractMember)
    {
        if (!$orders)
            return;
        $startDate = $contractMember->getStartDate();
        $startDate = $startDate->format('Y-m-d');
        foreach ($orders as $key => $value) {
            $order = new Order();
            $product = $this->productRepository->findOneBy(['id' => $value['product']]);
            $order->setProduct($product);
            $order->setUnitPrice($product->getPrice());
            $order->setQuantity($value['quantity']);
            $deliveries = $value['deliveries'] ?? null;
            foreach ($deliveries ?? $contractMember->getContract()->getDeliveries() as $key => $delivery) {
                if ($deliveries)
                    $delivery = $this->deliveryRepository->findOneBy(['id' => $delivery]);
                if ($delivery->getDate()->format('Y-m-d') >= $startDate) {
                    $productOrder = new ProductOrder();
                    $order->addDelivery($delivery);
                    $productOrder->setSubscriber($contractMember->getSubscriber());
                    $productOrder->setCommand($order);
                    $productOrder->setStatus($delivery->getStatus());
                    $productOrder->setDate($delivery->getDate());
                    $this->manager->persist($productOrder);
                }
            }
            $contractMember->addOrder($order);
            $this->manager->persist($order);
        }
    }

    public function setOrderDeliveriesMultiple(Product $product, ContractMember $contractMember)
    {
        $order = new Order();
        $order->setProduct($product);
        $order->setUnitPrice($product->getPrice());
        $order->setQuantity(1);
        foreach ($contractMember->getContract()->getDeliveries() as $key => $delivery) {
            $productOrder = new ProductOrder();
            $order->addDelivery($delivery);
            $productOrder->setSubscriber($contractMember->getSubscriber());
            $productOrder->setCommand($order);
            $productOrder->setStatus($delivery->getStatus());
            $productOrder->setDate($delivery->getDate());
            $this->manager->persist($productOrder);
        }
        $contractMember->addOrder($order);
        $this->manager->persist($order);
    }

    public function updateOrderDeliveries(Contract $contract)
    {
        $multidistributions = $contract->getMultidistribution();
        $contractMembers = $contract->getContractMembers();
        foreach ($contractMembers as $contractMember) {
            $orders = $contractMember->getOrders();
            if (!$multidistributions)
                $deliveries = $contractMember->getContract()->getDeliveries();
            $member = $contractMember->getSubscriber();
            foreach ($orders as $order) {
                $productOrders = $this->productOrderRepository->findBy(['command' => $order]);
                if ($multidistributions)
                    $deliveries = $order->getDeliveries();
                foreach ($deliveries as $delivery) {
                    $productOrder = new ProductOrder();
                    $productOrder->setDate($delivery->getDate());
                    $productOrder->setStatus($delivery->getStatus());
                    $productOrder->setSubscriber($member);
                    $productOrder->setCommand($order);
                    $this->manager->persist($productOrder);
                }
                $contractMember->addOrder($order);
                $this->manager->persist($order);
                foreach ($productOrders as $productOrder) {
                    $this->manager->remove($productOrder);
                }
            }
        }
    }
}
