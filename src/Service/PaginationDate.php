<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\orderProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationDate
{

    /**
     * Entité sur laquelle on applique la pagination
     *
     * @var string
     */
    private $entityClass;

    /**
     * Manager Doctrine
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Gestionnaire de template twig
     *
     * @var Environment
     */
    private $twig;

    /**
     * Chemin du template de pagination depuis service.yaml
     *
     * @var string
     */
    private $templatePathDateYears;

    /**
     * Chemin du template de pagination depuis service.yaml
     *
     * @var string
     */
    private $templatePathDateDefault;

    /**
     * Chemin du template de pagination depuis service.yaml
     *
     * @var string
     */
    private $templatePathDateWithUser;

    /**
     * Utilisateur
     *
     * @var User
     */
    private $user;

    private $years = [];

    private $months = [];

    private $year;

    private $path;

    private $month;

    private $data = [];

    /**
     * Contructeur public
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePathDateYears, $templatePathDateDefault, $templatePathDateWithUser)
    {
        $this->request = $request;
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePathDateDefault = $templatePathDateDefault;
        $this->templatePathDateWithUser = $templatePathDateWithUser;
        $this->templatePathDateYears = $templatePathDateYears;
    }

    /**
     * Affiche la pagination dans la page
     * id permet de passer un id au template
     * @return void
     */
    public function renderByYear()
    {
        $this->twig->display($this->getTemplatePathDate(), [
            'years' => $this->getYears(),
            'year' => $this->getYear(),
        ]);
    }

    /**
     * Affiche la pagination dans la page
     * id permet de passer un id au template
     * @return void
     */
    public function renderWithUser()
    {
        $this->twig->display($this->getTemplatePathDate(), [
            'user' => $this->getUser(),
            'path' => $this->getPath(),
            'years' => $this->getOrderYears(),
            'year' => $this->getYear(),
            'months' => $this->getMonths(),
            'month' => $this->getMonth(),
        ]);
    }

    /**
     * Affiche la pagination dans la page
     * id permet de passer un id au template
     * @return void
     */
    public function renderDefault()
    {
        $this->twig->display($this->getTemplatePathDate(), [
            'path' => $this->getPath(),
            'years' => $this->years,
            'year' => $this->getYear(),
            'months' => $this->getMonths(),
            'month' => $this->getMonth()
        ]);
    }

    /**
     * Get the value of entityClass
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of repo
     * Renvoie le repository de la class concernée
     */
    public function getRepo()
    {
        if (!$this->entityClass)
            throw new \Exception(("La classe pour la pagination n'a pas été renseignée dans Pagination::setEntityClass()"));
        return $this->manager->getRepository($this->entityClass);
    }

    /**
     * Get chemin du template de pagination depuis service.yaml
     *
     * @return  string
     */
    public function getTemplatePathDate()
    {
        return $this->templatePathDate;
    }


    public function setTemplatePathDate($type)
    {
        if ($type == 'default')
            $this->templatePathDate = $this->templatePathDateDefault;
        if ($type == 'withUser')
            $this->templatePathDate = $this->templatePathDateWithUser;
        if ($type == 'years')
            $this->templatePathDate = $this->templatePathDateYears;

        return $this;
    }

    /**
     * Get the value of years
     */
    public function getYears(): array
    {
        foreach ($this->getRepo()->findAll() as $contract) {
            if ($contract->getstatus() != 'archivé')
                $this->years[] = $contract->getYear();
        }
        return array_unique($this->years);
    }

    /**
     * Get the value of years
     */
    public function getOrderYears(): array
    {
        $ordersYears = $this->getRepo()->findAll();
        foreach ($ordersYears as $order) {
            $year = date('Y', $order->getDate()->getTimestamp());
            $this->years[] = $year;
        }
        return array_unique($this->years);
    }

    /**
     * Get the value of years
     */
    public function getPermanenceYears(): array
    {
        $permanenceYears = $this->getRepo()->findAll();
        foreach ($permanenceYears as $permanence) {
            $year = date('Y', $permanence->getDate()->getTimestamp());
            $this->years[] = $year;
        }
        return array_unique($this->years);
    }

    /**
     * Set the value of years
     *
     * @return  self
     */
    public function setYears($years)
    {
        $this->years = $years;

        return $this;
    }

    /**
     * Get the value of months
     */
    public function getMonths()
    {
        $this->months = [
            'janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        return $this->months;
    }

    /**
     * Set the value of months
     *
     * @return  self
     */
    public function setMonths($months)
    {
        $this->months = $months;

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        if (!$this->year)
            $this->year = date('Y');
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of month
     */
    public function getMonth()
    {
        if (!$this->month)
            $this->month = date('m', time());
        return $this->month;
    }

    /**
     * Set the value of month
     *
     * @return  self
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

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
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
