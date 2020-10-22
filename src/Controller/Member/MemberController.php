<?php

namespace App\Controller\Member;


use App\Entity\User;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités d'affichage des adhérents
 * 
 * @Route("/adherent")
 */
class MemberController extends AbstractController
{
    /**
     * Affiche la liste des adhérents
     * 
     * @Route("/liste/{page<\d+>?1}", name="member_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('member/members/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
