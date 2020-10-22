<?php

namespace App\Controller\Referent;

use App\Entity\User;
use App\Repository\ContractMemberRepository;
use App\Repository\ContractRepository;
use App\Repository\ProducerRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux dashboards référent
 * 
 * @Route("/referent/tableaudebord")
 */
class DashboardController extends AbstractController
{
    /**
     * Affiche le dashboard référent
     * 
     * @Route("/", name="referent_dashboard_index", methods={"GET"})
     */
    public function index(ContractRepository $cr, UserRepository $ur, ProducerRepository $pr, ProductRepository $pdtr): Response
    {
        return $this->render('referent/dashboard/index.html.twig', [
            'totalContract' => count($cr->findBy(['status' => 'actif'])),
            'totalMember' => count($ur->findAll()),
            'totalProducer' => count($pr->findAll()),
            'totalProduct' => count($pdtr->findAll()),
        ]);
    }

    /**
     * Affiche le dashboard des archives
     * 
     * @Route("/archive", name="referent_dashboard_archive", methods={"GET"})
     */
    public function archive(ContractRepository $cr, ContractMemberRepository $car): Response
    {
        return $this->render('referent/dashboard/archive.html.twig', [
            'totalContracts' => count($cr->findBy(['status' => 'archivé'])),
            'totalContractMembers' => count($car->findBy(['status' => 'archivé'])),
        ]);
    }
}
