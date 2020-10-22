<?php

namespace App\Controller;

use DateTime;
use App\Service\PDFMaker;
use App\Entity\EmailSimple;
use App\Form\EmailSimpleType;
use App\Notification\CreateEmail;
use App\Repository\ContractRepository;
use App\Service\ProducerOrderMaker;
use App\Repository\ProducerRepository;
use App\Repository\PermanenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux emails
 * 
 * @Route("/email")
 */
class EmailController extends AbstractController
{
    /**
     * Affiche et traite le formulaire d'envoi d'email
     * 
     * @Route("/", name="email_new", methods={"GET","POST"})
     */
    public function new(Request $request, CreateEmail $createEmail)
    {
        $this->denyAccessUnlessGranted('ROLE_REFERENT');
        $emailSimple = new EmailSimple();
        $form = $this->createForm(EmailSimpleType::class, $emailSimple);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$emailSimple->getRecipientsGroup() && !$emailSimple->getRecipients() && !$emailSimple->getRecipientsContracts()) {
                $this->addFlash('danger', "Envoi impossible : aucun destinataire");
                return $this->render('email/new.html.twig', ['form' => $form->createView()]);
            }
            if ($emailSimple->getSaveDoc() && !$emailSimple->getDocName()) {
                $this->addFlash('danger', 'Veuillez donner un nom au document que vous souhaitez sauvegarder');
                return $this->render('email/new.html.twig', ['form' => $form->createView()]);
            }
            if ($file = $form->get('file')->getData())
                $emailSimple->setFile($file);
            $emailSimple->setSender($this->getUser());
            $createEmail->sendEmail($emailSimple);
            $this->addFlash('success', "Message envoyé");
            return $this->redirectToRoute('email_new');
        }
        return $this->render('email/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Traite l'envoi d'email automatique aux producteurs
     * 
     * @Route("/livraisons/producteur", name="email_livraison_auto", methods={"GET"})
     */
    public function emailLivraisonsProducteur(ContractRepository $cr, ProducerOrderMaker $pom, CreateEmail $createEmail, PDFMaker $pdfMaker)
    {
        $contracts = $cr->findBy(['emailAuto' => 1]);
        $d = (int) date('d');
        $m = (int) date('m');
        $y = (int) date('Y');
        //$today = new \DateTimeImmutable("$y-$m-$d-01T00:00:00");
        $today = new \DateTimeImmutable("2020-09-28-01T00:00:00");
        foreach ($contracts as $contract) {
            $producer = $contract->getProducer();
            $emailReferent = $contract->getProducer()->getReferent()->getEmail();
            $deliveries = [];
            $emailFrequency = $contract->getFrequencyEmailAuto();
            $timestamp = strtotime($today->format('Y-m-d') . "$emailFrequency");
            $deliveryDate = new DateTime();
            $deliveryDate->setTimestamp($timestamp);
            foreach ($contract->getDeliveries() as $delivery)
                $deliveries[] = $delivery;
            foreach ($deliveries as $delivery) {
                if ($delivery->getDate()->format('Y-m-d') == $deliveryDate->format('Y-m-d')) {
                    $deliveriesList = $pom->getListOrdersByPeriode($producer, $deliveryDate, $deliveryDate);
                    if ($deliveriesList) {
                        $html = $this->renderView('editdoc/deliveries_pdf.html.twig', [
                            'deliveries' => $deliveriesList,
                            'producer' => $producer->getName()
                        ]);
                        $documentName = $pdfMaker->newPDFForEmail($html, "livraisons");
                    }
                    $createEmail->sendEmailLivraisons($producer->getUser(), $documentName, $deliveryDate, $emailReferent);
                }
            }
        }
        return $this->redirectToRoute('home');
    }

    /**
     * Traite l'envoi d'email automatique aux adhérents
     * 
     * @Route("/permanences", name="email_permanence_auto", methods={"GET"})
     */
    public function emailPermanenceAuto(PermanenceRepository $pr, CreateEmail $createEmail)
    {
        //$today = DateTime::createFromFormat('Y-m-d', "2020-09-23");
        $today = new DateTime();
        $timestamp = strtotime($today->format('Y-m-d') . '+6days');
        $permanenceDate = new DateTime();
        $permanenceDate->setTimestamp($timestamp);
        if ($today->format('Y-m-d') == $permanenceDate->format('Y-m-d'))
            $permanences = $pr->findByPeriod($permanenceDate);
        if ($permanences) {
            $participants = $permanences[0]->getParticipants()->toArray();
            foreach ($participants as $participant)
                $createEmail->sendEmailPermanence($participant, $permanences[0]->getDate(), $participant->getFirstName());
        }
        return $this->redirectToRoute('home');
    }
}
