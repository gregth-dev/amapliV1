<?php

namespace App\Controller\Producer;

use App\Entity\ProductOrder;
use App\Service\PaginationDate;
use App\Service\ProducerOrderMaker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités lées à l'affichage des livraisons du producteur
 * 
 * @Route("/producteur/livraisons")
 */
class OrderController extends AbstractController
{

    /**
     * Affiche la liste des livraisons du producteur
     * 
     * @Route("/liste/{year}/{month}", name="producer_orders_index", methods={"GET"})
     */
    public function orders($month = null, $year = null, ProducerOrderMaker $pom, PaginationDate $paginationDate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCER');
        $user = $this->getUser();
        $paginationDate
            ->setTemplatePathDate('default')
            ->setEntityClass(ProductOrder::class)
            ->setPath('producer_orders_index')
            ->setYears($paginationDate->getOrderYears())
            ->setYear($year)
            ->setMonth($month);
        if (!($data = $pom->getListOrders($user, $paginationDate->getMonth(), $paginationDate->getYear())))
            $this->addFlash('danger', "Aucune livraison trouvée");
        $paginationDate->setData($data);
        return $this->render('producer/delivery/deliveries.html.twig', ['paginationDate' => $paginationDate]);
    }
}
