<?php

namespace App\Controller;

use App\Form\UserForm\LostPasswordType;
use App\Entity\UserNotification;
use App\Repository\UserRepository;
use App\Notification\CreateUserNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller des fonctionnalités liées au login, au mot de passe perdu et au CGU
 */
class HomeController extends AbstractController
{
    /**
     * Redirige vers app_login ou le tableau de bord de l'utilisateur
     * 
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        if ($user = $this->getUser()) {
            switch ($user->getMemberType()) {
                case 'Adhérent':
                    return $this->redirectToRoute('member_dashboard_index');
                    break;
                case 'Producteur':
                    return $this->redirectToRoute('producer_dashboard_index');
                    break;
                default:
                    return $this->redirectToRoute('referent_dashboard_index');
                    break;
            }
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * Affiche la page de récupération du mot de passe
     * 
     * @Route("perdu/motdepasse", name="lost_password", methods={"GET","POST"})
     */
    public function lostPassword(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder, CreateUserNotification $notification)
    {
        $form = $this->createForm(LostPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            if ($user = $userRepository->findOneBy(['email' => $email])) {
                $entityManager = $this->getDoctrine()->getManager();
                $userNotification = new UserNotification($user);
                $user->setPassword($encoder->encodePassword($user, $userNotification->getPassword()));
                $notification->lostPassword($userNotification);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', "Un email vous a été envoyé pour renouveler votre mot de passe");
                return $this->redirectToRoute('home');
            }
            $this->addFlash('danger', "Echec : cet email n'est pas associé à un compte");
        }

        return $this->render('security/lost.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les CGU de l'application
     * 
     * @Route("/cgu", name="cgu", methods={"GET"})
     */
    public function cgu()
    {
        return $this->render('cgu.html.twig');
    }
}
