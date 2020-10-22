<?php

namespace App\Controller\Producer;

use App\Entity\Payment;
use App\Entity\PaymentPDF;
use App\Service\Pagination;
use App\Form\PaymentForm\PaymentPDFType;
use App\Repository\PaymentRepository;
use App\Repository\ProducerRepository;
use App\Service\PDFMaker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctonnalités liés aux paiements du producteur
 * 
 * @Route("producteur/paiements")
 */
class PaymentController extends AbstractController
{
    /**
     * Affiche la liste des paiements et permet d'éditer la liste en PDF
     * 
     * @Route("/liste/{page<\d+>?1}", name="payment_list_payments", methods={"GET","POST"})
     * 
     */
    public function listPayments(Request $request, PaymentRepository $paymentRepository, ProducerRepository $producerRepository, ProducerRepository $pr, Pagination $pagination, $page, PDFMaker $pdfMaker)
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCER');
        $user = $this->getUser();
        $producer = $producerRepository->findOneBy(['user' => $user]);
        $paymentPDF = new PaymentPDF();
        $form = $this->createForm(PaymentPDFType::class, $paymentPDF);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $paymentPDF->getStartDate();
            $endDate = $paymentPDF->getEndDate();
            $status = $paymentPDF->getStatus();
            $payments = $paymentRepository->findByPeriodByStatus($producer, $startDate, $endDate, $status);
            if ($payments) {
                $totalPayments = 0;
                foreach ($payments as $payment) {
                    $totalPayments += $payment->getAmount();
                }
                $html = $this->renderView('producer/payment/payments_pdf.html.twig', [
                    'payments' => $payments,
                    'totalPayments' => $totalPayments,
                    'totalCheck' => count($payments)
                ]);
                $pdfMaker->newPDF($html, "payment.pdf");
            } else
                $this->addFlash('danger', "Aucun paiement sur cette période");
        }
        $producer = $pr->findOneBy(['user' => $user]);
        $pagination
            ->setOptions(['producer' => $producer])
            ->setOrderData(['depositDate' => 'ASC'])
            ->setEntityClass(Payment::class)
            ->setCurrentPage($page);
        return $this->render('producer/payment/member_payment.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }
}
