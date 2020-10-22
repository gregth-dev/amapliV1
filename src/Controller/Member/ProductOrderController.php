<?php

namespace App\Controller\Member;

use App\Entity\ProductOrder;
use App\Service\ProductOrderMaker;
use App\Service\PaginationDate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées à l'affichage des livraisons
 * 
 * @Route("/adherent/livraisons")
 */
class ProductOrderController extends AbstractController
{

    /**
     * Affiche les livraisons de l'adhérent
     * 
     * @Route("/liste/{year}/{month}", name="order_list", methods={"GET"})
     */
    public function list($month = null, $year = null, ProductOrderMaker $pom, PaginationDate $paginationDate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $paginationDate
            ->setTemplatePathDate('withUser')
            ->setEntityClass(ProductOrder::class)
            ->setPath('order_list')
            ->setYears($paginationDate->getOrderYears())
            ->setUser($user)
            ->setYear($year)
            ->setMonth($month);
        $data = $pom->getProductOrder($paginationDate->getUser(), $paginationDate->getMonth(), $paginationDate->getYear());
        $paginationDate->setData($data);
        return $this->render('member/order_product/list.html.twig', ['paginationDate' => $paginationDate]);
    }
}
