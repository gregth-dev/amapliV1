<?php

namespace App\Controller\Member;

use App\Entity\Permanence;
use App\Service\PaginationDate;
use App\Repository\PermanenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux permanences
 * 
 * @Route("/adherent/permanence")
 */
class PermanenceController extends AbstractController
{
    /**
     * Affiche les permanences de l'adhérent
     * 
     * @Route("/adherent", name="member_permanence_list", methods={"GET"})
     */
    public function listByUser(PermanenceRepository $pr): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $permanences = $pr->findByParticipant($user);

        return $this->render('member/permanence/member.html.twig', [
            'permanences' => $permanences,
        ]);
    }

    /**
     * Affiche la liste des permanences disponibles
     * 
     * @Route("/liste/{year}/{month}", name="permanence_index", methods={"GET"})
     */
    public function index($month = null, $year = null, PaginationDate $paginationDate): Response
    {
        $paginationDate
            ->setTemplatePathDate('default')
            ->setEntityClass(Permanence::class)
            ->setPath('permanence_index')
            ->setYears($paginationDate->getPermanenceYears())
            ->setYear($year)
            ->setMonth($month);
        $data = $this->getDoctrine()->getManager()->getRepository(Permanence::class)->findByDate($month, $year);
        $paginationDate->setData($data);
        return $this->render('member/permanence/index.html.twig', [
            'paginationDate' => $paginationDate,
        ]);
    }

    /**
     * Affiche le détail de la permanence
     * 
     * @Route("/{id}", name="permanence_show", methods={"GET"})
     */
    public function show(Permanence $permanence): Response
    {
        return $this->render('member/permanence/show.html.twig', [
            'permanence' => $permanence,
        ]);
    }

    /**
     * Inscrit un adhérent à la permanence
     * 
     * @Route("/{id}/sinscrire", name="permanence_subscribe", methods={"GET"})
     */
    public function subscribe(Permanence $permanence): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $participants = $permanence->getParticipants()->toArray();
        if (!in_array($user, $participants)) {
            if (count($permanence->getParticipants()->toArray()) < $permanence->getNumberPlaces()) {
                $permanence->addParticipant($user);
                $this->addFlash('success', 'Merci pour votre inscription');
                $this->getDoctrine()->getManager()->flush();
            } else {
                $this->addFlash('danger', 'Nombre maximum de participants atteint');
            }
        } else
            $this->addFlash('danger', 'Vous êtes déjà inscrit à cette permanence');
        return $this->redirectToRoute('permanence_index');
    }

    /**
     * Désinscrit un adhérent de la permanence
     * 
     * @Route("/{id}/desinscrire", name="permanence_unsubscribe", methods={"GET"})
     */
    public function unsubscribe(Permanence $permanence): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $date = $permanence->getDate()->format('Y-m-d');
        $dateLimite = date('Y-m-d', strtotime($date . '-1 months'));
        if (date('Y-m-d') < $dateLimite) {
            $permanence->removeParticipant($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Vous vous êtes désinscrit');
        } else {
            $this->addFlash('danger', "Erreur : La date limite d'un mois pour vous désinscrire est dépassée");
        }

        return $this->redirectToRoute('permanence_index');
    }
}
