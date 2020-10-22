<?php

namespace App\Controller\Referent;

use App\Entity\Contract;
use App\Service\Pagination;
use App\Entity\ContractMember;
use App\Service\OrderDeliveryMaker;
use App\Repository\ContractRepository;
use App\Repository\ContractMemberRepository;
use App\Form\ContractForm\ContractMemberType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ContractForm\ContractMember2Type;
use App\Form\ContractForm\ContractMember3Type;
use App\Form\ContractForm\ContractMember4Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ContractForm\ContractMemberMultipleType;
use App\Service\ContractMaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux contrats adhérents
 * 
 * @Route("/referent/contrats/adherents")
 */
class ContractMemberController extends AbstractController
{
    /**
     * Affiche la liste des contrats adhérents
     * 
     * @Route("/liste/{page<\d+>?1}", name="referent_contract_member_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setEntityClass(ContractMember::class)
            ->setCurrentPage($page);
        return $this->render('referent/contract_member/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche et traite le formulaire de création d'un contrat adhérent
     * 
     * @Route("/nouveau", name="referent_contract_member_new", methods={"GET","POST"})
     */
    public function new(Request $request, ContractMemberRepository $cmr): Response
    {
        $contractMember = new ContractMember();
        $form = $this->createForm(ContractMemberType::class, $contractMember);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (!$cmr->findBy(['contract' => $contractMember->getContract(), 'subscriber' => $contractMember->getSubscriber()])) {
                $entityManager->persist($contractMember);
                $contractMember->getContract()->setStatus('actif');
                $contractMember->setBalance(0);
                $entityManager->flush();
                if ($contractMember->getContract()->getMultidistribution())
                    return $this->redirectToRoute('referent_contract_member_new2', ['id' => $contractMember->getId()]);
                else
                    return $this->redirectToRoute('referent_contract_member_new3', ['id' => $contractMember->getId()]);
            } else
                $this->addFlash('danger', 'Cet Adhérent a déjà souscrit à ce contrat');
        }

        return $this->render('referent/contract_member/new.html.twig', [
            'contractMember' => $contractMember,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un produit au contrat adhérent multidistribution
     * 
     * @Route("/{id}/nouveau/2", name="referent_contract_member_new2", methods={"GET","POST"})
     */
    public function new2(ContractMember $contractMember, Request $request, OrderDeliveryMaker $odm): Response
    {
        if ($contractMember->getStatus() == 'actif')
            return $this->redirectToRoute('referent_contract_member_index');
        if (!$contractMember->getContract()->getMultiDistribution())
            return $this->redirectToRoute('referent_contract_member_new3', ['id' => $contractMember->getId()]);
        $idContract = $contractMember->getContract()->getId();
        $idProducer = $contractMember->getContract()->getProducer()->getId();
        $startDate = $contractMember->getStartDate() ?? $contractMember->getContract()->getStartDate();
        $contractMember->setStartDate($startDate);
        $form = $this->createForm(ContractMember2Type::class, $contractMember, [
            'idContract' => $idContract,
            'idProducer' => $idProducer,
            'startDate' => $startDate,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($orders = $request->request->get('contract_member2')['orders'] ?? [])
                $odm->setOrderDeliveries($orders, $contractMember);
            if ($contractMember->getOrders()->toArray()) {
                $entityManager->persist($contractMember);
                $entityManager->flush();
                return $this->redirectToRoute('referent_contract_member_payments', ['id' => $contractMember->getId()]);
            } else
                $this->addFlash('danger', 'Veuillez ajouter un produit');
        }

        return $this->render('referent/contract_member/new2.html.twig', [
            'contractMember' => $contractMember,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un produit au contrat adhérent simple distribution
     * 
     * @Route("/{id}/nouveau/3", name="referent_contract_member_new3", methods={"GET","POST"})
     */
    public function new3(ContractMember $contractMember, Request $request, OrderDeliveryMaker $odm): Response
    {
        if ($contractMember->getStatus() == 'actif')
            return $this->redirectToRoute('referent_contract_member_index');
        if ($contractMember->getContract()->getMultiDistribution())
            return $this->redirectToRoute('referent_contract_member_new2', ['id' => $contractMember->getId()]);
        $idProducer = $contractMember->getContract()->getProducer()->getId();
        $startDate = $contractMember->getStartDate() ?? $contractMember->getContract()->getStartDate();
        $contractMember->setStartDate($startDate);
        $form = $this->createForm(ContractMember3Type::class, $contractMember, [
            'idProducer' => $idProducer
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($orders = $request->request->get('contract_member3')['orders'] ?? [])
                $odm->setOrderDeliveries($orders, $contractMember);
            if ($contractMember->getOrders()->toArray()) {
                $entityManager->persist($contractMember);
                $entityManager->flush();
                return $this->redirectToRoute('referent_contract_member_payments', ['id' => $contractMember->getId()]);
            } else
                $this->addFlash('danger', 'Veuillez ajouter un produit');
        }

        return $this->render('referent/contract_member/new3.html.twig', [
            'contractMember' => $contractMember,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout des paiements au contrat adhérent
     * 
     * @Route("/{id}/nouveau/paiements", name="referent_contract_member_payments", methods={"GET","POST"})
     */
    public function payments(ContractMember $contractMember, Request $request): Response
    {
        $error = false;
        $amountOrders = $contractMember->getAmountOrders();
        $form = $this->createForm(ContractMember4Type::class, $contractMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payments = $contractMember->getPayments()->toArray();
            $amountActivePayment = $contractMember->getAmountActivePayments();
            $amountPayments = $contractMember->getAmountPayments();
            $amountDeposit = $contractMember->getAmountDeposit();
            if (!($amountPayments > $amountOrders)) {
                $contractMember->setBalance(($amountOrders + $amountDeposit) - $amountActivePayment);
                $producer = $contractMember->getContract()->getProducer();
                foreach ($payments as $payment) {
                    $payment->setProducer($producer);
                }
                $contractMember->setStatus("actif");
                $this->addFlash('success', 'Contrat adhérent créé');
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('referent_contract_member_index');
            } else {
                $this->addFlash('danger', "La somme des paiements ne peut être supérieure au montant du contrat");
                $error = true;
            }
        }

        return $this->render('referent/contract_member/new4.html.twig', [
            'contractMember' => $contractMember,
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * Archive un contrat adhérent
     * 
     * @Route("/{id}/archiver", name="referent_contract_member_archive", methods={"GET","POST"})
     */
    public function archive(ContractMember $contractMember): Response
    {
        if ($contractMember->getStatus() == 'à archiver') {
            $contract = $contractMember->getContract();
            if ($contract->getStatus() != 'archivé') {
                $idContract = $contract->getId();
                return new JsonResponse(['error' => 1, 'text' => "Archivage impossible : le contrat producteur n°$idContract n'a pas été archivé"]);
            }
            $idContractMember = $contractMember->getId();
            if ($contractMember->getBalance() > 0)
                return new JsonResponse(['error' => 1, 'text' => "Archivage impossible : le contrat adhérent n°$idContractMember n'est pas soldé"]);
            foreach ($contractMember->getPayments() as $payment) {
                if ($payment->getStatus() != 'remis') {
                    $idPayment = $payment->getId();
                    return new JsonResponse(['error' => 1, 'text' => "Archivage impossible : le chèque n°$idPayment du contrat adhérent n°$idContractMember n'a pas été remis"]);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $contractMember->setStatus('archivé');
            $entityManager->persist($contractMember);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Contrat Adhérent archivé']);
        } else
            return new JsonResponse(['error' => 1, 'text' => "Ce contrat ne peut pas être archivé"]);
    }

    /**
     * Archive plusieurs contrats adhérents
     * 
     * @Route("/{id}/archiver/contrats", name="referent_contract_member_archive_list", methods={"GET","POST"})
     */
    public function archiveListe(Contract $contract): Response
    {
        if ($contract->getStatus() != 'archivé') {
            $id = $contract->getId();
            $this->addFlash('danger', "Archivage impossible : le contrat producteur n°$id n'a pas été archivé");
            return $this->redirectToRoute('referent_contract_member_list_archive', ['id' => $contract->getId()]);
        }
        foreach ($contract->getContractMembers() as $contractMember) {
            $idContractMember = $contractMember->getId();
            if ($contractMember->getBalance() > 0) {
                $this->addFlash('danger', "Archivage impossible : le contrat adhérent n°$idContractMember n'est pas soldé");
                return $this->redirectToRoute('referent_contract_member_list_archive', ['id' => $contractMember->getContract()->getId()]);
            }
            foreach ($contractMember->getPayments() as $payment) {
                if ($payment->getStatus() != 'remis') {
                    $id = $payment->getId();
                    $this->addFlash('danger', "Archivage impossible : le chèque n°$id du contrat adhérent n°$idContractMember n'a pas été remis");
                    return $this->redirectToRoute('referent_contract_member_list_archive', ['id' => $contractMember->getContract()->getId()]);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $contractMember->setStatus('archivé');
            $entityManager->persist($contractMember);
        }
        $entityManager->flush();
        $this->addFlash('success', "Tous les contrats adhérents ont été archivés");
        return $this->redirectToRoute('referent_contract_member_list_archive', ['id' => $contractMember->getContract()->getId()]);
    }

    /**
     * Affiche la liste des contrats adhérents archivés
     * 
     * @Route("/archives/{year}/{page<\d+>?1}", name="referent_contract_member_index_archive", methods={"GET"})
     */
    public function indexArchive(ContractRepository $cr, $year = null, Pagination $pagination, $page): Response
    {
        $years = [];
        foreach ($cr->findAll() as $contract) {
            if ($contract->getStatus() == 'archivé')
                $years[] = $contract->getYear();
        }
        if (!$year)
            $year = $years[0] ?? date('Y', time());
        $pagination
            ->setYear($year)
            ->setEntityClass(ContractMember::class)
            ->setCurrentPage($page);
        return $this->render('referent/contract_member/index_archive.html.twig', [
            'pagination' => $pagination,
            'years' => array_reverse(array_unique($years))
        ]);
    }

    /**
     * Affiche la liste des contrats adhérents archivés à partir du contrat producteur
     * 
     * @Route("/archives/{id}/liste/{page<\d+>?1}", name="referent_contract_member_list_archive", methods={"GET"})
     */
    public function listArchive(Contract $contract, Pagination $pagination, $page): Response
    {
        $pagination
            ->setOptions(['contract' => $contract])
            ->setEntityClass(ContractMember::class)
            ->setCurrentPage($page);
        return $this->render('referent/contract_member/list_contract_archive.html.twig', [
            'pagination' => $pagination,
            'contract' => $contract
        ]);
    }

    /**
     * Affiche le détail d'un contrat adhérent archivé ou non
     * 
     * @Route("/{id}/consulter/{archive}", name="referent_contract_member_show", methods={"GET"})
     */
    public function show(ContractMember $contractMember, $archive = null): Response
    {
        return $this->render('referent/contract_member/show.html.twig', [
            'contractMember' => $contractMember,
            'archive' => $archive,
        ]);
    }

    /**
     * Supprime un contrat adhérent
     * 
     * @Route("/{id}", name="referent_contract_member_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ContractMember $contractMember): Response
    {
        if ($contractMember->getStatus() != 'actif') {
            if ($this->isCsrfTokenValid('delete' . $contractMember->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $orders = $contractMember->getOrders()->toArray();
                foreach ($orders as $order) {
                    $deliveries = $order->getDeliveries()->toArray();
                    foreach ($deliveries as $delivery) {
                        $order->removeDelivery($delivery);
                    }
                    $productOrders = $order->getProductOrders();
                    foreach ($productOrders as $productOrder) {
                        $entityManager->remove($productOrder);
                    }
                    $entityManager->remove($order);
                }
                $payments = $contractMember->getPayments();
                foreach ($payments as $payment) {
                    $entityManager->remove($payment);
                }
                $contract = $contractMember->getContract();
                if (count($contract->getContractMembers()->toArray()) == 1) {
                    $contract->setStatus('non actif');
                    $entityManager->persist($contract);
                }
                $entityManager->remove($contractMember);
                $entityManager->flush();
            }
            return new JsonResponse(['success' => 1, 'text' => 'Contrat Adhérent supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du contrat adhérent"]);
        }
    }

    /**
     * Créer plusieurs contrats adhérents depuis un formulaire unique
     * 
     * @Route("/nouveau/multiple", name="referent_contract_member_new_multiple", methods={"GET","POST"})
     */
    public function newMultiple(Request $request, ContractMaker $cm): Response
    {
        $contractMember = new ContractMember();
        $form = $this->createForm(ContractMemberMultipleType::class, $contractMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $contractMember->getProduct();
            $producer = $contractMember->getContract()->getProducer();
            $products = $producer->getProducts()->toArray();
            if (in_array($product, $products)) {
                $cm->setMultipleContract($product, $contractMember);
                $this->addFlash('success', "Nouveaux contrats créés");
                return $this->redirectToRoute('referent_contract_member_index');
            } else
                $this->addFlash('danger', "Le produit ne correspond pas au contrat sélectionné");
        }

        return $this->render('referent/contract_member/new_multiple.html.twig', [
            'contractMember' => $contractMember,
            'form' => $form->createView(),
        ]);
    }
}
