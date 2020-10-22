<?php

namespace App\Controller\Referent;

use App\Entity\User;
use App\Service\Pagination;
use App\Form\UserForm\UserType;
use App\Entity\UserNotification;
use App\Form\UserForm\UserRoleType;
use App\Notification\CreateUserNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller des foncionnalités liées aux adhérents
 * 
 * @Route("referent/adherents")
 */
class MemberController extends AbstractController
{
    /**
     * Affiche la liste des adhérents
     * 
     * @Route("/{page<\d+>?1}", name="referent_member_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setOrderData(['lastName' => 'ASC'])
            ->setEntityClass(User::class)
            ->setCurrentPage($page);
        return $this->render('referent/members/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un adhérent
     * Envoi un mail au nouvel adhérent avec son mot de passe provisoire
     * 
     * @Route("/nouveau", name="referent_member_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        CreateUserNotification $notification
    ): Response {
        $user = new User();
        $form = $this
            ->createForm(UserType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $userNotification = new UserNotification($user);
            $user->setPassword($encoder->encodePassword($user, $userNotification->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            $notification->notify($userNotification);
            $this->addFlash('success', "Nouvel adhérent ajouté");
            return $this->redirectToRoute('referent_member_index');
        }

        return $this->render('referent/members/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le détail d'un adhérent
     * 
     * @Route("/consulter/{id}", name="referent_member_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('referent/members/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Affiche et traite le formulaire de msie à jour d'un adhérent
     * 
     * @Route("/{id}/editer", name="referent_member_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, CreateUserNotification $notification): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userNotification = new UserNotification($user);
            $notification->notifyUpdate($userNotification);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'adhérent a été mis à jour");

            return $this->redirectToRoute('referent_member_index');
        }

        return $this->render('referent/members/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de modification du rôle de l'adhérent
     * 
     * @Route("/{id}/editer/role", name="referent_member_edit_role", methods={"GET","POST"})
     */
    public function editRole(Request $request, User $user, CreateUserNotification $notification): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($role = $user->getRole()) {
                if ($user->getMemberType() != 'Producteur') {
                    $user->setRoles([$role]);
                    $user->setMemberType();
                    $userNotification = new UserNotification($user);
                    $notification->notifyUpdateRole($userNotification);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', "Le rôle de l'adhérent a été mis à jour");
                    return $this->redirectToRoute('referent_member_index');
                }
                $this->addFlash('danger', "Un producteur ne peut être Référent, Trésorier ou Administrateur");
            }
        }

        return $this->render('referent/members/edit_role.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un adhérent
     * 
     * @Route("/{id}", name="referent_member_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $role = $user->getMemberType();
        if ($contracts = $user->getContractMembers()->toArray()) {
            $contract = $contracts[0]->getcontract()->getName();
            return new JsonResponse(['error' => 1, 'text' => "Suppression impossible cet adhérent est lié au $contract"]);
        }
        if ($producers = $user->getProducersReferent()->toArray()) {
            $producer = $producers[0]->getName();
            return new JsonResponse(['error' => 1, 'text' => "Suppression impossible cet adhérent est référent auprès du $producer"]);
        }
        if ($role != 'Adhérent')
            return new JsonResponse(['error' => 1, 'text' => "Vous devez modifier le rôle $role avant la suppression"]);
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Adhérent supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression de l'adhérent"]);
        }
    }
}
