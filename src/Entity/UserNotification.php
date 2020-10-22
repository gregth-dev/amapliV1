<?php

namespace App\Entity;


use DateTime;
use DateTimeZone;
use App\Entity\User;

/**
 * classe de notification d'email de l'entité User
 */
class UserNotification
{

    /**
     * champ email
     *
     * @var string
     */
    private $email;

    /**
     * champ mot de passe 
     *
     * @var string
     */
    private $password;

    /**
     * type de compte
     *
     * @var string
     */
    private $accountType;

    /**
     * nom complet
     *
     * @var string
     */
    private $fullName;

    /**
     * User
     *
     * @var User
     */
    private $user;

    /**
     * date de la notification
     *
     * @var datetime
     */
    private $createdAt;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
    }

    public function getEmail(): ?string
    {
        $this->email = $this->user->getEmail();
        return $this->email;
    }

    /**
     * Génére un mot de passe aléatoire de 6 chiffres
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        if (!$this->password) {
            $int = [];
            for ($i = 0; $i < 6; $i++)
                $int[] = rand(0, 9);
            $this->password = implode('', $int);
        }
        return $this->password;
    }

    public function getAccountType(): ?string
    {
        $this->accountType = $this->user->getMemberType();

        return $this->accountType;
    }

    /**
     * Get the value of fullName
     */
    public function getFullName()
    {
        $this->fullName = $this->user->getFullName();

        return $this->fullName;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
