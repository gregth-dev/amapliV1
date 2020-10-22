<?php

namespace App\Controller\Treasurer;

use App\Entity\Donation;
use App\Form\DonationType;
use App\Repository\DonationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux donations
 * 
 * @Route("/tresorier/donation")
 */
class DonationController extends AbstractController
{
    /**
     * Affiche la liste des donations
     * 
     * @Route("/", name="treasurer_donation_index", methods={"GET"})
     */
    public function index(DonationRepository $donationRepository): Response
    {
        return $this->render('treasurer/donation/index.html.twig', [
            'donations' => $donationRepository->findAll(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de création d'une donation
     * 
     * @Route("/nouveau", name="treasurer_donation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $donation = new Donation();
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($donation->isEqualAmount()) {
                foreach ($donation->getPayment() as $payment) {
                    $payment->setDonation($donation);
                    $entityManager->persist($payment);
                }
                $entityManager->persist($donation);
                $entityManager->flush();
                $this->addFlash('success', 'Donation enregistrée');
                return $this->redirectToRoute('treasurer_donation_index');
            }
            $this->addFlash('danger', 'Erreur dans les paiements. Vérifier les montants saisis.');
        }
        return $this->render('treasurer/donation/new.html.twig', [
            'donation' => $donation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'une donation
     * 
     * @Route("/{id}/editer", name="treasurer_donation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Donation $donation): Response
    {
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($donation->isEqualAmount()) {
                foreach ($donation->getPayment() as $payment)
                    $payment->setDonation($donation);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Donation mise à jour');
                return $this->redirectToRoute('treasurer_donation_index');
            }
            $this->addFlash('danger', 'Erreur dans les paiements. Vérifier les montants saisis.');
        }
        return $this->render('treasurer/donation/edit.html.twig', [
            'donation' => $donation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une donation
     * 
     * @Route("/{id}", name="treasurer_donation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Donation $donation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $donation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($donation->getPayment()[0]->getStatus() == 'remis')
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible le paiement a été remis"]);
            $entityManager->remove($donation);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Donation supprimée']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression de la donation"]);
        }
    }
}
