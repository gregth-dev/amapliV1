<?php

namespace App\Notification;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\UserNotification;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Classe dédiée aux notifications par email à l'utilisateur
 */
class CreateUserNotification
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Envoi un email à l'adhérent nouvellement inscrit
     *
     * @param UserNotification $user
     * @return void
     */
    public function notify(UserNotification $user)
    {
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <contact@escalamap.org>"))
            ->to($user->getEmail())
            ->subject("Contact depuis Escalamap")
            ->htmlTemplate('notifications/member/subscribe.html.twig')
            ->context([
                'notification' => $user,
            ]);
        $this->mailer->send($email);
    }

    public function notifyUpdateRole(UserNotification $user)
    {
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <contact@escalamap.org>"))
            ->to($user->getEmail())
            ->subject("Contact depuis Escalamap")
            ->htmlTemplate('notifications/member/update_role.html.twig')
            ->context([
                'fullName' => $user->getFullName(),
                'notification' => $user,
            ]);
        $this->mailer->send($email);
    }

    public function notifyUpdate(UserNotification $user)
    {
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <contact@escalamap.org>"))
            ->to($user->getEmail())
            ->subject("Contact depuis Escalamap")
            ->htmlTemplate('notifications/member/update.html.twig')
            ->context([
                'fullName' => $user->getFullName(),
                'notification' => $user,
            ]);
        $this->mailer->send($email);
    }

    public function lostPassword(UserNotification $user)
    {
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <contact@escalamap.org>"))
            ->to($user->getEmail())
            ->subject('Escal\'AMAP - Récupération de mot de passe')
            ->htmlTemplate('notifications/member/lost_password.html.twig')
            ->context([
                'fullName' => $user->getFullName(),
                'notification' => $user,
            ]);
        $this->mailer->send($email);
    }
}
