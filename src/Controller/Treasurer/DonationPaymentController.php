<?php

namespace App\Controller\Treasurer;

use App\Service\Pagination;
use App\Service\PaymentMaker;
use App\Entity\DonationPayment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux paiements des donations
 * 
 * @Route("/tresorier/donations")
 */
class DonationPaymentController extends AbstractController
{
    /**
     * Affiche la liste des paiemnts des donations
     * 
     * @Route("/paiements/{page<\d+>?1}", name="treasurer_payment_donation_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page, PaymentMaker $pm)
    {
        $pagination
            ->setEntityClass(DonationPayment::class)
            ->setCurrentPage($page);
        $pm->setStatus(DonationPayment::class);
        return $this->render('treasurer/donation/payment_donations.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Remet un paiement
     * 
     * @Route("/remettre/{id}", name="treasurer_payment_donation_check", methods={"GET","POST"})
     */
    public function check(DonationPayment $payment)
    {
        if ($payment->getStatus() != 'remis') {
            $payment->setStatus('remis');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 1]);
        }
    }
}
