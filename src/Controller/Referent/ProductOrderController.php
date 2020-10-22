<?php

namespace App\Controller\Referent;

use App\Entity\User;
use App\Entity\ProductOrder;
use App\Service\Pagination;
use App\Service\PaginationDate;
use App\Service\ProductOrderMaker;
use App\Service\ProducerOrderMaker;
use App\Repository\ProducerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux livraisons
 * 
 * @Route("/referent/livraisons/liste")
 */
class ProductOrderController extends AbstractController
{

    /**
     * Affiche la liste des adhérents
     * 
     * @Route("/adherents/{page<\d+>?1}", name="referent_order_list_member", methods={"GET"})
     */
    public function listMember(Pagination $pagination, $page): Response
    {
        $pagination
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('referent/order_product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche la liste des livraisons d'un adhérent
     * 
     * @Route("/adherent/{id}/{year}/{month}", name="referent_order_list", methods={"GET"})
     */
    public function listOrder(User $user, $month = null, $year = null, PaginationDate $paginationDate, ProductOrderMaker $orderProduitMaker): Response
    {
        $paginationDate
            ->setTemplatePathDate('withUser')
            ->setEntityClass(ProductOrder::class)
            ->setPath('referent_order_list')
            ->setYears($paginationDate->getOrderYears())
            ->setUser($user)
            ->setYear($year)
            ->setMonth($month);
        if (!($data = $orderProduitMaker->getProductOrder($paginationDate->getUser(), $paginationDate->getMonth(), $paginationDate->getYear())))
            $this->addFlash('danger', "Aucune livraison trouvée");
        $paginationDate->setData($data);
        return $this->render('referent/order_product/list.html.twig', ['paginationDate' => $paginationDate]);
    }

    /**
     * Affiche la liste des producteurs
     * 
     * @Route("/producteurs", name="referent_delivery_list_producer", methods={"GET"})
     */
    public function listProducer(ProducerRepository $pr): Response
    {
        return $this->render('referent/order_product/index_producers.html.twig', [
            'producers' => $pr->findAll(),
        ]);
    }

    /**
     * Affiche la liste des livraisons d'un producteur
     * 
     * @Route("/producteur/{id}/{year}/{month}", name="referent_deliveries_producer_list", methods={"GET"})
     */
    public function listDeliveries(User $user, $month = null, $year = null, ProducerOrderMaker $pom, PaginationDate $paginationDate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $paginationDate
            ->setTemplatePathDate('withUser')
            ->setEntityClass(ProductOrder::class)
            ->setPath('referent_deliveries_producer_list')
            ->setYears($paginationDate->getOrderYears())
            ->setUser($user)
            ->setYear($year)
            ->setMonth($month);
        if (!($data = $pom->getListOrders($user, $paginationDate->getMonth(), $paginationDate->getYear())))
            $this->addFlash('danger', "Aucune livraison trouvée");
        $paginationDate->setData($data);
        return $this->render('referent/order_product/deliveries_producer.html.twig', ['paginationDate' => $paginationDate, 'user' => $user]);
    }
}
