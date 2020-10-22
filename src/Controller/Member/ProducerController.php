<?php

namespace App\Controller\Member;

use App\Entity\Producer;
use App\Repository\ProductRepository;
use App\Repository\ProducerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées à l'affiche des producteurs et des produits
 * 
 * @Route("/adherent/producteurs")
 */
class ProducerController extends AbstractController
{
    /**
     * Affiche la liste des producteurs
     * 
     * @Route("/produits", name="producer_products", methods={"GET"})
     */
    public function producers(ProducerRepository $producerRepository): Response
    {
        return $this->render('member/producer/products.html.twig', [
            'producers' => $producerRepository->findAll(),
        ]);
    }

    /**
     * Affiche la liste des produits d'un producteur
     * 
     * @Route("/liste/produits/{id}", name="producer_product_list", methods={"GET"})
     */
    public function list(ProductRepository $productRepository, Producer $producer, ProducerRepository $producerRepository): Response
    {
        return $this->render('member/product/list.html.twig', [
            'products' => $productRepository->findby(['producer' => $producer], ['name' => 'ASC']),
            'producer' => $producerRepository->findOneBy(['id' => $producer->getId()])
        ]);
    }
}
