<?php

namespace App\Controller\Member;

use App\Entity\User;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités d'affichage des référents
 * 
 * @Route("/adherent/referents")
 */
class ReferentController extends AbstractController
{
    /**
     * Affiche la liste des référents
     * 
     * @Route("/{page<\d+>?1}", name="referent_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setOptions(['memberType' => 'Référent'])
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('member/referent/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
