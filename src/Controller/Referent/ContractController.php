<?php

namespace App\Controller\Referent;

use App\Entity\Contract;
use App\Service\PaginationDate;
use App\Service\OrderDeliveryMaker;
use App\Repository\ContractRepository;
use App\Form\ContractForm\ContractType;
use App\Service\ContractDeliveryMaker;
use App\Repository\DeliveryRepository;
use App\Service\ContractMemberMaker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux contrats producteurs
 * 
 * @Route("/referent/contrat")
 */
class ContractController extends AbstractController
{
    /**
     * Affiche les contrats producteurs actifs
     * 
     * @Route("/actifs/{year}", name="referent_contract_index", methods={"GET"})
     */
    public function index(PaginationDate $paginationDate, $year = null, ContractRepository $cr): Response
    {
        $paginationDate
            ->setTemplatePathDate('years')
            ->setEntityClass(Contract::class)
            ->setYear($year);
        $data = $cr->findByActiveYear($paginationDate->getYear());
        $paginationDate->setData($data);
        return $this->render('referent/contract/index.html.twig', ['paginationDate' => $paginationDate]);
    }

    /**
     * Affiche et traite le formulaire de création d'un contrat producteur
     * 
     * @Route("/nouveau", name="referent_contract_new", methods={"GET","POST"})
     */
    public function new(Request $request, ContractDeliveryMaker $cd): Response
    {
        $contract = new Contract();
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($cd->validateDeliveries($contract)) {
                if ($contract->getProducer()->getProducts()->toArray()) {
                    $cd->setDeliveries($contract);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($contract);
                    $entityManager->flush();
                    $cd->deleteDouble($contract);
                    $this->addFlash('success', 'Nouveau contrat créé');
                    return $this->redirectToRoute('referent_contract_index');
                }
                $this->addFlash('danger', "Création du contrat impossible ce producteur n'est associé à aucun produit");
            }
            $this->addFlash('danger', "Dates de distribution incorrectes, elles doivent être comprises entre la date de début et la date de fin du contrat");
        }

        return $this->render('referent/contract/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le détail d'un contrat producteur
     * 
     * @Route("/{id}/consulter", name="contract_show", methods={"GET"})
     */
    public function show(Contract $contract, DeliveryRepository $dr): Response
    {
        $deliveries = $dr->findBy(['contract' => $contract], ['date' => 'ASC']);
        return $this->render('referent/contract/show.html.twig', [
            'contract' => $contract,
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'un contrat producteur
     * 
     * @Route("/{id}/editer", name="contract_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Contract $contract,
        ContractDeliveryMaker $cd,
        OrderDeliveryMaker $odm,
        ContractMemberMaker $contractMemberMaker
    ): Response {
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($cd->validateDeliveries($contract)) {
                if ($contract->getProducer()->getProducts()->toArray()) {
                    $cd->setDeliveries($contract);
                    $entityManager->persist($contract);
                    $entityManager->flush();
                    $cd->deleteDouble($contract);
                    $odm->updateOrderDeliveries($contract);
                    $contractMemberMaker->updateBalance($contract);
                    $this->addFlash('success', 'Le contrat a été mis à jour');
                    return $this->redirectToRoute('referent_contract_index');
                }
                $this->addFlash('danger', "Mise à jour du contrat impossible ce producteur n'est associé à aucun produit");
            }
            $this->addFlash('danger', 'Dates de distribution incorrectes, 
                elles doivent être comprises entre la date de début et la date de fin du contrat');
        }

        return $this->render('referent/contract/edit.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un contrat producteur
     * 
     * @Route("/{id}", name="referent_contract_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Contract $contract): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contract->getId(), $request->request->get('_token'))) {
            if (count($contract->getContractMembers()))
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible : un ou plusieurs contrats adhérents sont rattachés à ce contrat"]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contract);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Contrat supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du contrat"]);
        }
    }

    /**
     * Affiche la liste des contrats producteurs archivés
     * 
     * @Route("/archives/{year}", name="referent_contract_index_archive", methods={"GET"})
     */
    public function indexArchive(ContractRepository $cr, $year = null): Response
    {
        $years = [];
        foreach ($cr->findAll() as $contract) {
            if ($contract->getstatus() == 'archivé')
                $years[] = $contract->getYear();
        }
        if (!$year)
            $year = $years[0] ?? date('Y', time());
        $contracts = $cr->findByArchiveYear($year);
        return $this->render('referent/contract/index_archive.html.twig', [
            'contracts' => $contracts,
            'years' => array_reverse(array_unique($years))
        ]);
    }

    /**
     * Archive un contrat producteur
     * 
     * @Route("/{id}/archiver", name="referent_contract_archive", methods={"GET","POST"})
     */
    public function archive(Contract $contract): Response
    {
        if ($contract->getstatus() == 'à archiver') {
            $contracts = $contract->getContractMembers();
            foreach ($contracts as $contractMember) {
                $payments = $contractMember->getPayments()->toArray();
                $id = $contractMember->getId();
                foreach ($payments as $payment) {
                    if ($payment->getStatus() != 'remis')
                        return new JsonResponse(['error' => 1, 'text' => "Archivage impossible : Tous les chèques du contrat adhérent n°$id n'ont pas été remis"]);
                    if ($contractMember->getBalance() > 0)
                        return new JsonResponse(['error' => 1, 'text' => "Archivage impossible : le contrat adhérent n°$id n'est pas soldé"]);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $contract->setstatus('archivé');
            $entityManager->persist($contract);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Contrat archivé']);
        } else
            return new JsonResponse(['error' => 1, 'text' => "Ce contrat ne peut pas être archivé"]);
    }

    /**
     * Supprime un contrat producteur archivé
     * 
     * @Route("/{id}/archive", name="referent_contract_delete_archive", methods={"DELETE"})
     */
    public function deleteArchive(Request $request, Contract $contract): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contract->getId(), $request->request->get('_token'))) {
            if (count($contract->getContractMembers()))
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible : un ou plusieurs contrats adhérents sont rattachés à ce contrat"]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contract);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Contrat supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du contrat"]);
        }
    }
}
