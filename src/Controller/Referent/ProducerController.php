<?php

namespace App\Controller\Referent;

use App\Entity\Producer;
use App\Form\ProducerForm\ProducerType;
use App\Repository\ProducerRepository;
use App\Form\ProducerForm\Producer2Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux producteurs
 * 
 * @Route("/referent/producteur")
 */
class ProducerController extends AbstractController
{
    /**
     * Affiche la liste des producteurs
     * 
     * @Route("/", name="referent_producer_index", methods={"GET"})
     */
    public function index(ProducerRepository $producerRepository): Response
    {
        return $this->render('referent/producer/index.html.twig', [
            'producers' => $producerRepository->findAll(),
        ]);
    }

    /**
     * Affiche la page qui renvoie vers la liste des produits de chaque producteur
     * 
     * @Route("/produits", name="referent_producer_products", methods={"GET"})
     */
    public function produits(ProducerRepository $producerRepository): Response
    {
        return $this->render('referent/producer/products.html.twig', [
            'producers' => $producerRepository->findAll(),
        ]);
    }

    /**
     * Affiche et traite le formulaite d'ajout d'un producteur
     * 
     * @Route("/nouveau", name="referent_producer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $producer = new Producer();
        $form = $this->createForm(ProducerType::class, $producer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $producer->defineRole();
            $entityManager->persist($producer);
            $entityManager->flush();
            $this->addFlash('success', "Le producteur a été ajouté");
            return $this->redirectToRoute('referent_producer_index');
        }

        return $this->render('referent/producer/new.html.twig', [
            'producer' => $producer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le détail d'un producteur
     * 
     * @Route("/{id}", name="referent_producer_show", methods={"GET"})
     */
    public function show(Producer $producer): Response
    {
        return $this->render('referent/producer/show.html.twig', [
            'producer' => $producer,
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'un producteur
     * 
     * @Route("/{id}/editer", name="referent_producer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Producer $producer): Response
    {
        $user = $producer->getUser();
        $form = $this->createForm(Producer2Type::class, $producer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $producer->getUser();
            if ($user != $newUser)
                $producer->updateRole($user, $newUser);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Le producteur a été mis à jour");
            return $this->redirectToRoute('referent_producer_index');
        }

        return $this->render('referent/producer/edit.html.twig', [
            'producer' => $producer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un producteur
     * 
     * @Route("/{id}", name="referent_producer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Producer $producer): Response
    {
        if ($text = $producer->checkProduct())
            return new JsonResponse(['error' => 1, 'text' => "Suppression impossible : $text."]);
        if ($text = $producer->checkContract())
            return new JsonResponse(['error' => 1, 'text' => "Suppression impossible : $text."]);
        if ($this->isCsrfTokenValid('delete' . $producer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $producer->deleteRole();
            $entityManager->remove($producer);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Producteur supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du producteur"]);
        }
    }
}
