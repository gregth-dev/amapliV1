<?php

namespace App\Controller\Treasurer;

use App\Entity\User;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Service\Pagination;
use App\Repository\UserRepository;
use App\Validator\SubscriptionValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux adhésions
 * 
 * @Route("/tresorier/adhesion")
 */
class SubscriptionController extends AbstractController
{
    /**
     * Affiche la liste des adhésions
     * 
     * @Route("/{page<\d+>?1}", name="treasurer_subscription_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setEntityClass(Subscription::class)
            ->setCurrentPage($page);
        return $this->render('treasurer/subscription/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche la liste des adhérents sans adhésion
     * 
     * @Route("/noninscrits", name="treasurer_subscription_unsubscribe", methods={"GET"})
     */
    public function unsubscribe(UserRepository $userRepository): Response
    {
        return $this->render('treasurer/subscription/unsubscribe.html.twig', [
            'subscribers' => $userRepository->findAll(),
        ]);
    }

    /**
     * Affiche et traite le formulaire d'une adhésion
     * 
     * @Route("/nouveau/{id}", name="treasurer_subscription_new", methods={"GET","POST"})
     */
    public function new($id = null, Request $request, SubscriptionValidator $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $subscription = new Subscription();
        if ($user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]))
            $subscription->setSubscriber($user);
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($validator->validateSubscriptionSubscriber($subscription)) {
                if ($subscription->isEqualAmount()) {
                    if ($validator->validateSubscriptionPayment($subscription)) {
                        foreach ($subscription->getPayment() as $payment) {
                            $payment->setSubscription($subscription);
                            $entityManager->persist($payment);
                        }
                        $entityManager->persist($subscription);
                        $entityManager->flush();
                        return $this->redirectToRoute('treasurer_subscription_index');
                    } else
                        $this->addFlash('danger', "Erreur : $validator->error");
                } else {
                    $diffTotal = $subscription->getDiffTotal();
                    $this->addFlash('danger', "Erreur : Il y a une différence de $diffTotal € entre le montant de l'adhésion et le paiement");
                }
            } else
                $this->addFlash('danger', "Cet adhérent a déjà une adhésion pour $validator->year. Merci de passer par Modifier l'adhésion");
        }
        return $this->render('treasurer/subscription/new.html.twig', [
            'adhesion' => $subscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'une adhésion
     * 
     * @Route("/editer/{id}", name="treasurer_subscription_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subscription $subscription, SubscriptionValidator $validator): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($subscription->isEqualAmount()) {
                if ($validator->validateSubscriptionPayment($subscription)) {
                    foreach ($subscription->getPayment() as $payment) {
                        $payment->setSubscription($subscription);
                        $entityManager->persist($payment);
                    }
                    $entityManager->persist($subscription);
                    $entityManager->flush();
                    return $this->redirectToRoute('treasurer_subscription_index');
                } else
                    $this->addFlash('danger', "Erreur : $validator->error");
            } else {
                $diffTotal = $subscription->getDiffTotal();
                $this->addFlash('danger', "Erreur : Il y a une différence de $diffTotal € entre le montant de l'adhésion et le paiement");
            }
        }
        return $this->render('treasurer/subscription/edit.html.twig', [
            'adhesion' => $subscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une adhésion
     * 
     * @Route("/{id}", name="treasurer_subscription_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Subscription $subscription): Response
    {
        if ($this->isCsrfTokenValid('delete' . $subscription->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($subscription->getIsValid())
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible le paiement a été remis"]);
            $entityManager->remove($subscription);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Adhésion supprimée']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression de l'adhésion"]);
        }
    }
}
