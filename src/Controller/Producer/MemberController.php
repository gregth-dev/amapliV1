<?php

namespace App\Controller\Producer;

use App\Service\Pagination;
use App\Entity\ContractMember;
use App\Repository\ContractRepository;
use App\Repository\ProducerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux adhérents
 * 
 * @Route("/producteur/liste")
 */
class MemberController extends AbstractController
{
    /**
     * Affiche la liste des adhérents au contrat du producteur
     * 
     * @Route("/adherents/{page<\d+>?1}", name="producer_members", methods={"GET"})
     */
    public function products(Pagination $pagination, $page, ProducerRepository $pr, ContractRepository $cr): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCER');
        $producer = $pr->findOneBy(['user' => $this->getUser()]);
        $contract = $cr->findOneByActiveYear(null, $producer);
        $pagination
            ->setOptions(['contract' => $contract])
            ->setOrderData(['createdAt' => 'ASC'])
            ->setEntityClass(ContractMember::class)
            ->setCurrentPage($page);
        return $this->render('producer/member/members.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
