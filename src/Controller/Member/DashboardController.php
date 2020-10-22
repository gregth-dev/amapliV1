<?php

namespace App\Controller\Member;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités du tableau de bord de l'adhérent
 * 
 * @Route("/adherent/tableaudebord")
 */
class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord de l'adhérent
     * 
     * @Route("/", name="member_dashboard_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('member/index.html.twig');
    }
}
