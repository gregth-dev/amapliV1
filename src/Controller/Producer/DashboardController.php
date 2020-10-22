<?php

namespace App\Controller\Producer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller du tableau de bord du producteur
 * 
 * @Route("/producteur/tableaudebord")
 */
class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord du producteur
     * 
     * @Route("/", name="producer_dashboard_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('producer/dashboard.html.twig');
    }
}
