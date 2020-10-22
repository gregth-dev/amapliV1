<?php

namespace App\Controller\Referent;

use App\Entity\Product;
use App\Entity\Producer;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\ProducerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux produits
 * 
 * @Route("/referent/produit")
 */
class ProductController extends AbstractController
{
    /**
     * Affiche la liste des produits d'un producteur
     * 
     * @Route("/{id}/liste", name="referent_product_list", methods={"GET"})
     */
    public function list(ProductRepository $productRepository, Producer $producer, ProducerRepository $producerRepository): Response
    {

        return $this->render('referent/product/list.html.twig', [
            'products' => $productRepository->findby(['producer' => $producer], ['name' => 'ASC']),
            'producer' => $producer
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un produit
     * 
     * @Route("/nouveau", name="referent_product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', "Nouveau produit ajouté");
            return $this->redirectToRoute('referent_product_list', ['id' => $product->getProducer()->getId()]);
        }

        return $this->render('referent/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'un produit
     * 
     * @Route("/{id}/editer", name="referent_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Produit mis à jour");
        }

        return $this->render('referent/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un produit
     * 
     * @Route("/{id}", name="referent_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Produit supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du produit"], 400);
        }
    }
}
