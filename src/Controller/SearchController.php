<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Repository\UserRepository;
use App\Repository\PaymentRepository;
use App\Repository\ContractRepository;
use App\Repository\DocumentRepository;
use App\Repository\ContractMemberRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SubscriptionPaymentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux recherches
 * 
 * @Route("/rechercher")
 */
class SearchController extends AbstractController
{
    /**
     * Envoi la liste des adhérents au fichier JS
     * 
     * @Route("/adherents", name="search_members", methods={"POST"})
     */
    public function getMembers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $members = $userRepository->findAll();
        $membersList = [];
        foreach ($members as $member) {
            $id = $member->getId();
            $membersList[$id]['id'] = $id;
            $membersList[$id]['member'] = $member->getFullName();
        }
        return new JsonResponse(['success' => 1, 'membersList' => $membersList]);
    }

    /**
     * Envoi la liste des contrats adhérents au fichier JS
     * 
     * @Route("/contrats/members", name="search_contract_members", methods={"POST"})
     */
    public function getContractMembers(ContractMemberRepository $contractMemberRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $contractMembers = $contractMemberRepository->findAll();
        $contractMembersList = [];
        foreach ($contractMembers as $contractMember) {
            if ($contractMember->getStatus() != "archivé") {
                $id = $contractMember->getId();
                $contractMembersList[$id]['id'] = $id;
                $contractMembersList[$id]['name'] = $contractMember->getContract()->getName();
                $contractMembersList[$id]['member'] = $contractMember->getSubscriber()->getFullName();
                $contractMembersList[$id]['createdAt'] = $contractMember->getCreatedAt()->format('d/m/Y');
                $contractMembersList[$id]['status'] = $contractMember->getStatus();
                $contractMembersList[$id]['statePayments'] = $contractMember->getStatePayments();
                $contractMembersList[$id]['statePaymentsClass'] = $contractMember->getStatePaymentsClass();
                $contractMembersList[$id]['balance'] = $contractMember->getBalance();
                $contractMembersList[$id]['endDate'] = $contractMember->getContract()->getEndDate()->format('d/m/Y');
            }
        }
        return new JsonResponse(['success' => 1, 'contractMembersList' => $contractMembersList]);
    }

    /**
     * Envoi la liste des adhérents avec le role au fichier JS
     * 
     * @Route("/referent/adherents", name="search_referent_members", methods={"POST"})
     */
    public function getReferentmember(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $userRole = $this->getUser()->getMemberType();
        $members = $userRepository->findAll();
        $membersList = [];
        $idAppUser = $this->getUser()->getId();
        foreach ($members as $member) {
            $id = $member->getId();
            if ($id != $idAppUser) {
                $membersList[$id]['id'] = $id;
                $membersList[$id]['member'] = $member->getFullName();
                $membersList[$id]['role'] = $member->getMemberType();
                $membersList[$id]['createdAt'] = $member->getCreatedAt()->format('d/m/Y');
            }
        }
        return new JsonResponse(['success' => 1, 'membersList' => $membersList, 'userRole' => $userRole]);
    }

    /**
     * Envoi la liste des adhérents et de leur nombre de contrat au fichier JS
     * 
     * @Route("/paiements/adherents", name="search_payment_members", methods={"POST"})
     */
    public function getPaymentmembers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $members = $userRepository->findAll();
        $membersList = [];
        foreach ($members as $member) {
            $totalContracts = 0;
            $id = $member->getId();
            $membersList[$id]['id'] = $id;
            $membersList[$id]['member'] = $member->getFullName();
            foreach ($member->getContractMembers()->toArray() as $contractMember) {
                if ($contractMember->getStatus() == 'actif') {
                    $totalContracts++;
                }
            }
            $membersList[$id]['totalContracts'] = $totalContracts;
        }
        return new JsonResponse(['success' => 1, 'membersList' => $membersList]);
    }

    /**
     * Envoi la liste des adhérents d'un contrat adhérent au fichier JS
     * 
     * @Route("/paiements/contrats/{id}", name="search_payments_contracts", methods={"POST"})
     */
    public function getPaymentsContracts(Contract $contrat): Response
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        //$contrat = $contratRepository->findOneBy(['id' => $contrat->getId()]);
        $contractListMembers = [];
        foreach ($contrat->getContractMembers()->toArray() as $contractMember) {
            $id = $contractMember->getId();
            $contractListMembers[$id]['id'] = $id;
            $contractListMembers[$id]['member'] = $contractMember->getSubscriber()->getFullName();
        }
        return new JsonResponse(['success' => 1, 'contractListMembers' => $contractListMembers]);
    }

    /**
     * Envoi la liste des paiements des contrats adhérents au fichier JS
     * 
     * @Route("/paiement/find", name="search_payment", methods={"POST"})
     */
    public function searchPayment(PaymentRepository $paymentRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $payments = $paymentRepository->findAll();
        $paymentsList = [];
        foreach ($payments as $payment) {
            $id = $payment->getId();
            $contractMember = $payment->getContractMember();
            $paymentsList[$id]['id'] = $id;
            $paymentsList[$id]['member'] = $contractMember->getSubscriber()->getFullName();
            $paymentsList[$id]['contract'] = $contractMember->getContract()->getFullName();
            $paymentsList[$id]['checkNumber'] = $payment->getCheckNumber();
            $paymentsList[$id]['amount'] = $payment->getAmount();
            $paymentsList[$id]['depositDate'] = $payment->getDepositDate()->format('d/m/Y');
            $paymentsList[$id]['compareDate'] = $payment->getDepositDate()->format('Y-m-d');
            $paymentsList[$id]['status'] = $payment->getStatus();
        }
        return new JsonResponse(['success' => 1, 'paymentsList' => $paymentsList]);
    }

    /**
     * Envoi la liste des paiements des adhesions au fichier JS
     * 
     * @Route("/paiements/adhesions", name="search_payment_subscription", methods={"POST"})
     */
    public function searchSubscriptionPayment(SubscriptionPaymentRepository $spr)
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $paymentsList = [];
        foreach ($spr->findAll() as $payment) {
            $id = $payment->getId();
            $paymentsList[$id]['id'] = $id;
            $paymentsList[$id]['member'] = $payment->getSubscription()->getSubscriber()->getFullName();
            $paymentsList[$id]['checkNumber'] = $payment->getCheckNumber();
            $paymentsList[$id]['amount'] = $payment->getAmount();
            $paymentsList[$id]['depositDate'] = $payment->getDepositDate()->format('d/m/Y');
            $paymentsList[$id]['compareDate'] = $payment->getDepositDate()->format('Y-m-d');
            $paymentsList[$id]['status'] = $payment->getStatus();
        }
        return new JsonResponse(['success' => 1, 'paymentsList' => $paymentsList]);
    }

    /**
     * Envoi la liste des documents au fichier JS
     * 
     * @Route("/document", name="search_document", methods={"POST"})
     */
    public function getDocument(DocumentRepository $documentRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $documents = $documentRepository->findAll();
        $listDocuments = [];
        foreach ($documents as $document) {
            $id = $document->getId();
            $updateDate = $document->getUpdateDate();
            $listDocuments[$id]['id'] = $id;
            $listDocuments[$id]['document'] = $document->getName();
            $listDocuments[$id]['createdAt'] = $document->getCreatedAt()->format('d/m/Y');
            $listDocuments[$id]['updatedAt'] = $updateDate ? $updateDate->format('d/m/Y') : $document->getCreatedAt()->format('d/m/Y');
            $listDocuments[$id]['type'] = $document->getType();
            $listDocuments[$id]['icon'] = $document->getIcon();
        }
        return new JsonResponse(['success' => 1, 'listDocuments' => $listDocuments]);
    }
}
