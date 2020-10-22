<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class HTFile
{

    /**
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=5, minMessage="le nom d'utilisateur doit faire au minimum 5 caractères")
     * @var string
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\NotCompromisedPassword
     * @Assert\Regex(pattern="/^(?=.*[A-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/",match=true,message="Sécurité du mot de passe incorrect. Minimum 8 caractères, 1 chiffre, 1 majuscule, 1 caractère spécial")
     * @var string
     */
    private $plainPassword;

    /**
     * Chemin du répertoire
     *
     * @var string
     */
    private $path;

    /**
     * Chemin absolu vers le répertoire
     *
     * @var string
     */
    private $absolutePath;

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  string
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  string
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  string
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of absolutePath
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * Set the value of absolutePath
     *
     * @return  string
     */
    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;

        return $this;
    }
}
