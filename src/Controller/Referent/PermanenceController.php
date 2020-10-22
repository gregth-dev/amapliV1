<?php

namespace App\Controller\Referent;

use App\Entity\Permanence;
use App\Service\PaginationDate;
use App\Service\PermanenceMaker;
use App\Repository\PermanenceRepository;
use App\Form\PermanenceForm\PermanenceType;
use App\Form\PermanenceForm\Permanence2Type;
use App\Form\PermanenceForm\Permanence3Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\PermanenceForm\PermanenceUnsubscribeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux permanences
 * 
 * @Route("/referent/permanence")
 */
class PermanenceController extends AbstractController
{
    /**
     * Affiche la liste des permanences
     * 
     * @Route("/liste/{year}/{month}", name="referent_permanence_index", methods={"GET"})
     */
    public function index($month = null, $year = null, PaginationDate $paginationDate): Response
    {
        $paginationDate
            ->setTemplatePathDate('default')
            ->setEntityClass(Permanence::class)
            ->setPath('referent_permanence_index')
            ->setYears($paginationDate->getPermanenceYears())
            ->setYear($year)
            ->setMonth($month);
        $data = $this->getDoctrine()->getManager()->getRepository(Permanence::class)->findByDate($month, $year);
        $paginationDate->setData($data);
        return $this->render('referent/permanence/index.html.twig', [
            'paginationDate' => $paginationDate,
        ]);
    }

    /**
     * Affiche et traite le formulaire de création d'une permanence
     * 
     * @Route("/nouveau", name="referent_permanence_new", methods={"GET","POST"})
     */
    public function new(Request $request, PermanenceRepository $pr): Response
    {
        $permanence = new Permanence();
        $form = $this->createForm(PermanenceType::class, $permanence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$permanenceExist = $pr->findByPeriod($permanence->getDate())) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($permanence);
                $entityManager->flush();
                $this->addFlash('success', 'Nouvelle permanence créée');
                return $this->redirectToRoute('referent_permanence_index');
            } else
                $this->addFlash('danger', "Une permanence existe déjà à cette date");
        }

        return $this->render('referent/permanence/new.html.twig', [
            'permanence' => $permanence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de création des permanences multiples
     * 
     * @Route("/multiple", name="referent_permanence_multiple", methods={"GET","POST"})
     */
    public function multiple(Request $request, PermanenceMaker $pm, PermanenceRepository $pr): Response
    {
        $permanence = new Permanence();
        $form = $this->createForm(Permanence2Type::class, $permanence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$permanenceExist = $pr->findByPeriod($permanence->getStartDate(), $permanence->getEndDate())) {
                $entityManager = $this->getDoctrine()->getManager();
                $pm->setPermanences($permanence);
                $entityManager->flush();
                $this->addFlash('success', 'Nouvelles permanences créées');
                return $this->redirectToRoute('referent_permanence_index');
            } else {
                $permanenceExist = $permanenceExist[0]->getDate()->format('d-m-Y');
                $this->addFlash('danger', "Une permanence existe déjà sur cette période le $permanenceExist");
            }
        }

        return $this->render('referent/permanence/multiple.html.twig', [
            'permanence' => $permanence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le détail d'une permanence
     * 
     * @Route("/{id}", name="referent_permanence_show", methods={"GET"})
     */
    public function show(Permanence $permanence): Response
    {
        return $this->render('referent/permanence/show.html.twig', [
            'permanence' => $permanence,
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'une permanence
     * 
     * @Route("/{id}/editer", name="referent_permanence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Permanence $permanence, PermanenceRepository $pr): Response
    {
        $date = $permanence->getDate()->format('Y-m-d');
        $form = $this->createForm(Permanence3Type::class, $permanence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newDate = $permanence->getDate()->format('Y-m-d');
            $permanenceExist = [];
            if ($date != $newDate)
                $permanenceExist = $pr->findByPeriod($permanence->getDate());
            if (!$permanenceExist) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour de la permanence');
                return $this->redirectToRoute('referent_permanence_index');
            } else
                $this->addFlash('danger', "Une permanence existe déjà à cette date");
        }

        return $this->render('referent/permanence/edit.html.twig', [
            'permanence' => $permanence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Désinscrit un adhérent à une permanence
     * 
     * @Route("/{id}/desinscrire", name="referent_permanence_unsubscribe", methods={"GET","POST"})
     */
    public function unsubscribe(Request $request, Permanence $permanence): Response
    {
        $form = $this->createForm(PermanenceUnsubscribeType::class, $permanence, ['participants' => $permanence->getParticipants()->toArray()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour de la permanence');
            return $this->redirectToRoute('referent_permanence_index');
        }

        return $this->render('referent/permanence/unsubscribe.html.twig', [
            'permanence' => $permanence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une permanence
     * 
     * @Route("/{id}", name="referent_permanence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Permanence $permanence): Response
    {
        if ($this->isCsrfTokenValid('delete' . $permanence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            if (count($permanence->getParticipants()->toArray()))
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible : des participants sont inscrits à cette permanence"]);
            $entityManager->remove($permanence);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Permanence supprimée']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression de la permanence"]);
        }
    }
}
