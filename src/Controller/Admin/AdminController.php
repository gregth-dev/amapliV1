<?php

namespace App\Controller\Admin;

use App\Entity\HTFile;
use App\Entity\User;
use App\Entity\Producer;
use App\Form\HtAccessType;
use App\Service\HtFileMaker;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées à l'administrateur
 * 
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * Affiche la liste des administrateur du site
     * 
     * @Route("/administrateurs/{page<\d+>?1}", name="admin_admin_index", methods={"GET"})
     */
    public function indexAdmin(Pagination $pagination, $page): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination
            ->setOptions(['memberType' => 'Administrateur'])
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('admin/index_admin.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/referents/{page<\d+>?1}", name="admin_referent_index", methods={"GET"})
     */
    public function indexReferent(Pagination $pagination, $page): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination
            ->setOptions(['memberType' => 'Référent'])
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('admin/index_referent.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/tresoriers/{page<\d+>?1}", name="admin_treasurer_index", methods={"GET"})
     */
    public function indexTresorier(Pagination $pagination, $page): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination
            ->setOptions(['memberType' => 'Trésorier'])
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('admin/index_tresorier.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_admin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles([]);
            $user->setMemberType();
            if ($producers = $user->getProducersReferent()->toArray()) {
                $producer = $producers[0]->getName();
                return new JsonResponse(['error' => 1, 'text' => "Suppression impossible ce référent est lié à $producer"]);
            }
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Rôle utilisateur supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du rôle de l'utiliseur"]);
        }
    }

    /**
     * @Route("/htaccess/creer", name="admin_htaccess_create", methods={"GET","POST"})
     */
    public function htAccessCreate(Request $request, HtFileMaker $htFileMaker): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $htFile = new HTFile();
        $form = $this->createForm(HtAccessType::class, $htFile)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $htFileMaker->load($htFile);
            $htFileMaker->createHTFiles();
        }

        return $this->render('admin/ht_access.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
