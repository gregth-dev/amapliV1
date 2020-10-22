<?php

namespace App\Controller\Treasurer;

use App\Entity\Organism;
use App\Form\OrganismType;
use App\Repository\OrganismRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux associations
 * 
 * @Route("/tresorier/association")
 */
class OrganismController extends AbstractController
{
    /**
     * Affiche la liste des associations
     * 
     * @Route("/", name="treasurer_organism_index", methods={"GET"})
     */
    public function index(OrganismRepository $organismRepository): Response
    {
        return $this->render('treasurer/organism/index.html.twig', [
            'organisms' => $organismRepository->findAll(),
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout des associations
     * 
     * @Route("/nouveau", name="treasurer_organism_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $organism = new Organism();
        $form = $this->createForm(OrganismType::class, $organism);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organism);
            $entityManager->flush();
            $this->addFlash('success', "L'association a été ajoutée");
            return $this->redirectToRoute('treasurer_organism_index');
        }
        return $this->render('treasurer/organism/new.html.twig', [
            'organism' => $organism,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'une association
     * 
     * @Route("/{id}/edit", name="treasurer_organism_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, organism $organism): Response
    {
        $form = $this->createForm(OrganismType::class, $organism);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'association a été mise à jour");
            return $this->redirectToRoute('treasurer_organism_index');
        }
        return $this->render('treasurer/organism/edit.html.twig', [
            'organism' => $organism,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une association
     * 
     * @Route("/{id}", name="treasurer_organism_delete", methods={"DELETE"})
     */
    public function delete(Request $request, organism $organism): Response
    {
        if ($this->isCsrfTokenValid('delete' . $organism->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $subscriptions = $organism->getSubscriptions()->toArray();
            if ($subscriptions)
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible des adhésions sont liées à cette association"]);
            $entityManager->remove($organism);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Association supprimée']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression de l'assocation"]);
        }
    }
}
