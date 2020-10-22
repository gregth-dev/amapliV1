<?php

namespace App\Controller\Member;

use App\Entity\PasswordUpdate;
use App\Entity\UserNotification;
use App\Form\UserForm\UserEmailType;
use App\Form\UserForm\UserIdentityType;
use App\Form\UserForm\UserPasswordType;
use App\Notification\CreateUserNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller des fonctionnalités liées au compte
 * 
 * @Route("/adherent/compte")
 */
class AccountController extends AbstractController
{
    /**
     * Affiche la vue qui permet d'éditer les coordonnées de l'adhérent
     * 
     * @Route("/editer/coordonnees", name="member_account_identity", methods={"GET","POST"})
     */
    public function editIdentity(Request $request, CreateUserNotification $notification): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $form = $this->createForm(UserIdentityType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $userNotification = new UserNotification($user);
            $notification->notifyUpdate($userNotification);
            $this->addFlash('success', 'Votre profil a été mis à jour');
            return $this->redirectToRoute('member_dashboard_index');
        }

        return $this->render('member/account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche la vue qui permet d'éditer le mail de l'adhérent
     * 
     * @Route("/editer/email", name="member_account_email", methods={"GET","POST"})
     */
    public function editEmail(Request $request, CreateUserNotification $notification): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $form = $this->createForm(UserEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $userNotification = new UserNotification($user);
            $notification->notifyUpdate($userNotification);
            $this->addFlash('success', 'Votre email a été mis à jour');
            return $this->redirectToRoute('member_dashboard_index');
        }

        return $this->render('member/account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche la vue qui permet d'éditer le mot de passe de l'adhérent
     * 
     * @Route("/editer/motdepasse", name="member_account_password", methods={"GET","POST"})
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder, CreateUserNotification $notification): Response
    {
        $passwordupdate = new PasswordUpdate();
        $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class, $passwordupdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            if (!password_verify($passwordupdate->getOldPassword(), $user->getPassword())) {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect');
            } else {
                $hash = $encoder->encodePassword($user, $passwordupdate->getNewPassword());
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
                $userNotification = new UserNotification($user);
                $notification->notifyUpdate($userNotification);
                $this->addFlash('success', 'Votre mot de passe a été mis à jour');
                return $this->redirectToRoute('member_dashboard_index');
            }
        }

        return $this->render('member/account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
