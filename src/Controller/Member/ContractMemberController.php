<?php

namespace App\Controller\Member;

use App\Entity\Payment;
use App\Service\Pagination;
use App\Entity\ContractMember;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux contrats de l'adhérent
 * 
 * @Route("/adherent/contrat")
 */
class ContractMemberController extends AbstractController
{
    /**
     * Affiche la liste des contrats de l'adhérent
     * 
     * @Route("/", name="contract_member_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        return $this->render('member/contract_member/index.html.twig', [
            'contractMembers' => $user->getContractMembers(),
        ]);
    }

    /**
     * Affiche le détail d'un contrat adhérent
     * 
     * @Route("/{id}/consulter", name="contract_member_show", methods={"GET"})
     */
    public function show(ContractMember $contractMember): Response
    {
        return $this->render('member/contract_member/show.html.twig', [
            'contractMember' => $contractMember,
            'contract' => $contractMember->getContract(),
        ]);
    }

    /**
     * Affiche la liste des paiements du contrat adhérent
     * 
     * @Route("/{id}/paiements/{page<\d+>?1}", name="contract_member_payments", methods={"GET"})
     * 
     */
    public function listPayments(ContractMember $contractMember, Pagination $pagination, $page)
    {
        $adherent = $contractMember->getSubscriber();
        $pagination
            ->setOptions(['contractMember' => $contractMember])
            ->setEntityClass(Payment::class)
            ->setCurrentPage($page);
        return $this->render('member/contract_member/payments.html.twig', [
            'pagination' => $pagination,
            'adherent' => $adherent,
            'contractMember' => $contractMember,
            'contrat' => $contractMember->getContract(),
        ]);
    }
}
