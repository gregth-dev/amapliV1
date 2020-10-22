<?php

namespace App\Controller\Referent;

use App\Entity\Emargement;
use App\Service\PDFMaker;
use App\Form\EmargementType;
use App\Entity\EditLivraison;
use App\Form\EditLivraisonType;
use App\Service\EmargementMaker;
use App\Service\ProducerOrderMaker;
use App\Form\EditLivraisonProducerType;
use App\Repository\ProducerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées à l'édition des documents
 * 
 * @Route("/editdoc")
 */
class EditController extends AbstractController
{
    /**
     * Affiche, traite et génére la feuille d'émargement en PDF
     * 
     * @Route("/emargement/pdf", name="editdoc_emargement_pdf", methods={"GET","POST"})
     */
    public function emargementPDF(Request $request, EmargementMaker $emargementMaker, PDFMaker $pdfMaker)
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $emargement = new Emargement();
        $form = $this->createForm(EmargementType::class, $emargement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emargementList = $emargementMaker->setEmargement($emargement);
            if ($emargementList) {
                $html = $this->renderView(
                    'editdoc/emargement_pdf.html.twig',
                    ['list' => $emargementList['list'], 'dates' => $emargementList['dates'], 'producer' => $emargementList['producer']]
                );
                if (!$pdfMaker->newPDFAndSaveDoc($html, "emargement.pdf", $emargement))
                    $this->addFlash('danger', "Erreur lors de l'enregistrement");
            } else
                $this->addFlash('danger', 'aucune distribution de prévue aux dates choisies');
        }

        return $this->render('editdoc/index.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Affiche, traite et génére la liste des livraisons en PDF
     * 
     * @Route("/deliveries/pdf", name="editdoc_deliveries_pdf", methods={"GET","POST"})
     */
    public function editLivraisons(Request $request, ProducerOrderMaker $pom, PDFMaker $pdfMaker)
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $editLivraison = new EditLivraison();
        $form = $this->createForm(EditLivraisonType::class, $editLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $producer = $editLivraison->getProducer();
            $deliveries = $pom->getListOrdersByPeriode($producer, $editLivraison->getStartDate(), $editLivraison->getEndDate());
            if ($deliveries) {
                $html = $this->renderView('editdoc/deliveries_pdf.html.twig', ['deliveries' => $deliveries, 'producer' => $producer->getName()]);
                if (!$pdfMaker->newPDFAndSaveDoc($html, "distributions.pdf", $editLivraison))
                    $this->addFlash('danger', "Erreur lors de l'enregistrement");
            }
            $this->addFlash('danger', 'Aucune livraison pour la période sélectionnée');
        }
        return $this->render('editdoc/deliveries.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche, traite et génére la liste des livraisons pour le producteur en PDF
     * 
     * @Route("/deliveries/producer/pdf", name="editdoc_orders_producer_pdf", methods={"GET","POST"})
     */
    public function editProducerLivraisons(Request $request, ProducerOrderMaker $pom, PDFMaker $pdfMaker, ProducerRepository $pr)
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCER');
        $editLivraison = new EditLivraison();
        $form = $this->createForm(EditLivraisonProducerType::class, $editLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $producer = $pr->findOneBy(['user' => $this->getUser()]);
            $deliveries = $pom->getListOrdersByPeriode($producer, $editLivraison->getStartDate(), $editLivraison->getEndDate());
            if ($deliveries) {
                $html = $this->renderView('editdoc/deliveries_pdf.html.twig', ['deliveries' => $deliveries, 'producer' => $producer->getName()]);
                if (!$pdfMaker->newPDFAndSaveDoc($html, "distributions.pdf", $editLivraison))
                    $this->addFlash('danger', "Erreur lors de l'enregistrement");
            }
            $this->addFlash('danger', 'Aucune livraison pour la période sélectionnée');
        }
        return $this->render('editdoc/deliveries_producer.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
