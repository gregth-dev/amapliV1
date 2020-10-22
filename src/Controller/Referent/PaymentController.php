<?php

namespace App\Controller\Referent;

use App\Entity\User;
use App\Entity\Contract;
use App\Entity\ContractMember;
use App\Entity\Payment;
use App\Entity\PaymentList;
use App\Service\Pagination;
use App\Form\PaymentForm\PaymentListType;
use App\Repository\ContractRepository;
use App\Repository\PaymentRepository;
use App\Service\PaymentMaker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux paiements
 * 
 * @Route("/referent/paiement")
 */
class PaymentController extends AbstractController
{
    /**
     * Affiche la liste des adhérents
     * 
     * @Route("/adherent/{page<\d+>?1}", name="referent_payment_index_member", methods={"GET"})
     */
    public function listMember(Pagination $pagination, $page)
    {
        $pagination
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('referent/payment/index_member.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche la liste des contrats producteur
     * 
     * @Route("/contrat", name="referent_payment_index_contract", methods={"GET"})
     */
    public function listContract(ContractRepository $cr)
    {
        return $this->render('referent/payment/index_contract.html.twig', [
            'contracts' => $cr->findAll(),
        ]);
    }

    /**
     * Affiche la liste des contrats adhérents d'un adhérent
     * 
     * @Route("/{id}/liste/contrat/adherent", name="referent_payment_list_contract", methods={"GET"})
     */
    public function listMemberContract(User $user)
    {
        return $this->render('referent/payment/list_contract.html.twig', [
            'contractMembers' => $user->getContractMembers(),
            'member' => $user
        ]);
    }

    /**
     * Affiche la liste des contrats adhérents d'un contrat producteur
     * 
     * @Route("/{id}/liste/contrat/{page<\d+>?1}", name="referent_payment_list_member", methods={"GET"})
     */
    public function listContractAdherent(Contract $contract, Pagination $pagination, $page)
    {
        $pagination
            ->setOptions(['contract' => $contract])
            ->setEntityClass(ContractMember::class)
            ->setCurrentPage($page);

        return $this->render('referent/payment/list_member.html.twig', [
            'pagination' => $pagination,
            'contract' => $contract,
        ]);
    }

    /**
     * Affiche la liste des paiements d'un contrat adhérents
     * 
     * @Route("/{id}/contrats/adherents/paiements/{page<\d+>?1}", name="referent_payment_contractMember_payments", methods={"GET"})
     */
    public function listPayments(ContractMember $contractMember, Pagination $pagination, $page, PaymentMaker $pm)
    {
        $member = $contractMember->getSubscriber();
        $pagination
            ->setOptions(['contractMember' => $contractMember])
            ->setEntityClass(Payment::class)
            ->setCurrentPage($page);
        $pm->setStatus(Payment::class);
        return $this->render('referent/payment/payments_contract_member.html.twig', [
            'pagination' => $pagination,
            'member' => $member,
            'contractMember' => $contractMember
        ]);
    }

    /**
     * Affiche et traite le formulaire de sélection des paiements par période
     * 
     * @Route("/date", name="referent_payment_date", methods={"GET", "POST"})
     */
    public function paymentDate(Request $request, PaymentRepository $paymentRepository, PaymentMaker $pm)
    {
        $paymentList = new PaymentList();
        $form = $this->createForm(PaymentListType::class, $paymentList);
        $form->handleRequest($request);
        $payments = [];
        $pm->setStatus(Payment::class);
        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $paymentList->getStartDate();
            $endDate = $paymentList->getEndDate();
            $producer = $paymentList->getProducer();
            $status = $paymentList->getStatus();
            $payments = $paymentRepository->findByPeriodByStatus($producer, $startDate, $endDate, $status);
            if (!$payments)
                $this->addFlash('danger', "Aucune donnée sur cette période");
        }
        return $this->render('referent/payment/payments_date.html.twig', [
            'payments' => $payments,
            'form' => $form->createView()
        ]);
    }

    /**
     * Remet un paiement
     * 
     * @Route("/remettre/{id}", name="referent_payment_check", methods={"GET","POST"})
     */
    public function check(Payment $payment)
    {
        if ($payment->getStatus() != 'remis') {
            $payment->setStatus('remis');
            $entityManager = $this->getDoctrine()->getManager();
            $contractMember = $payment->getContractMember();
            if ($contractMember->getStatus() != 'actif')
                $contractMember->setStatus('actif');
            $newBalance = $contractMember->getTotalAmount() - $payment->getAmount();
            $contractMember->setBalance($newBalance);
            $entityManager->persist($payment);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 1]);
        }
    }

    /**
     * Affiche la liste de tous les paiements des contrats adhérents
     * 
     * @Route("/rechercher/{page<\d+>?1}", name="referent_payment_search_all", methods={"GET"})
     */
    public function searchPayment(Pagination $pagination, $page, PaymentMaker $pm)
    {
        $pagination
            ->setEntityClass(Payment::class)
            ->setCurrentPage($page);
        $pm->setStatus(Payment::class);
        return $this->render('referent/payment/search_payment.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
