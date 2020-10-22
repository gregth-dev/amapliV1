<?php

namespace App\Controller\Treasurer;

use App\Service\Pagination;
use App\Service\PaymentMaker;
use App\Entity\SubscriptionPayment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux paiements des adhésions
 * 
 * @Route("/tresorier/adhesions/paiements")
 */
class SubscriptionPaymentController extends AbstractController
{
    /**
     * Affiche la liste des paiements
     * 
     * @Route("/{page<\d+>?1}", name="treasurer_payment_subscription_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page, PaymentMaker $pm)
    {
        $pagination
            ->setEntityClass(SubscriptionPayment::class)
            ->setCurrentPage($page);
        $pm->setStatus(SubscriptionPayment::class);
        return $this->render('treasurer/subscription/payment_subscription.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Remet un paiement
     * 
     * @Route("/remettre/{id}", name="treasurer_payment_subscription_check", methods={"GET","POST"})
     */
    public function check(SubscriptionPayment $payment)
    {
        if ($payment->getStatus() != 'remis') {
            $payment->setStatus('remis');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $payment->getSubscription()->setIsValid(1);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 1]);
        }
    }
}
