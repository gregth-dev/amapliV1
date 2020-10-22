<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;


/**
 * Class Pagination
 * Affiche une pagination dans la page en fonction d'une classe donnée en paramètre
 * Ne pas oublier de configurer services.yaml en lien avec cette class
 * getTemplatePath --> le templatePath provient de services.yaml
 */
class Pagination
{

    /**
     * Entité sur laquelle on applique la pagination
     *
     * @var string
     */
    private $entityClass;

    /**
     * Nombre d'éléments à afficher par page
     *
     * @var integer
     */
    private $limit = 10;

    /**
     * Page en cours de lecture
     *
     * @var integer
     */
    private $currentPage = 1;

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
    private $templatePath;

    /**
     * Chemin du template de pagination depuis service.yaml
     *
     * @var string
     */
    private $templatePathArchive;

    /**
     * Données à ordonner lors de l'affichage
     *
     * @var array
     */
    private $orderData = [];

    /**
     * critères de la requête
     *
     * @var array
     */
    private $options = [];

    private $data = [];


    /**
     * Contructeur public
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath, $templatePathArchive)
    {
        $this->request = $request;
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
        $this->templatePathArchive = $templatePathArchive;
        $this->orderData = null;
        $this->order = null;
    }

    /**
     * Affiche la pagination dans la page
     * id permet de passer un id au template
     * @return void
     */
    public function render($id = null)
    {
        $this->twig->display($this->getTemplatePath(), [
            'page' => $this->getCurrentPage(),
            'pages' => $this->getPages(),
            'route' => $this->getRoute(),
            'id' => $id
        ]);
    }

    /**
     * Renvoie les données de la classe renseignée
     *
     * @return object[]
     */
    public function getData()
    {
        $offset = $this->currentPage * $this->limit - $this->limit;
        $this->data = $this->getRepo()->findBy($this->options, $this->orderData, $this->limit, $offset);
        return $this->data;
    }

    /**
     * Renvoie le nombre de page
     *
     * @return integer
     */
    public function getPages()
    {
        return ceil(count($this->getRepo()->findBy($this->options, $this->orderData)) / $this->limit);
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
     * Get the value of limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

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
     * Get the value of route
     */
    public function getRoute()
    {
        return $this->request->getCurrentRequest()->attributes->get('_route');
    }

    /**
     * Get the value of templatePath
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Get the value of templatePath
     */
    public function getTemplatePathArchive()
    {
        return $this->templatePathArchive;
    }

    /**
     * Set the value of templatePath
     *
     * @return  self
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Get données à ordonner lors de l'affichage
     *
     * @return  array
     */
    public function getOrderData()
    {
        return $this->orderData;
    }

    /**
     * Set données à ordonner lors de l'affichage
     *
     * @param  array  $orderData  Données à ordonner lors de l'affichage
     *
     * @return  self
     */
    public function setOrderData(array $orderData)
    {
        $this->orderData = $orderData;

        return $this;
    }

    /**
     * Get the value of options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @return  self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Affiche la pagination des archives contrat adhérent dans la page
     * year permet de passer une year au template
     * @return void
     */
    public function renderArchive()
    {
        $this->twig->display($this->getTemplatePathArchive(), [
            'page' => $this->getCurrentPage(),
            'pages' => $this->getPagesArchive(),
            'route' => $this->getRoute(),
            'year' => $this->year
        ]);
    }

    /**
     * Renvoie les données de la classe renseignée
     *
     * @return object[]
     */
    public function getDataArchive()
    {
        $offset = $this->currentPage * $this->limit - $this->limit;
        $this->data = $this->getRepo()->findByYearArchive($this->year, $this->limit, $offset);
        return $this->data;
    }

    /**
     * Renvoie le nombre de page
     *
     * @return integer
     */
    public function getPagesArchive()
    {
        return ceil(count($this->getRepo()->findByYearArchive($this->year)) / $this->limit);
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setyear($year)
    {
        $this->year = $year;

        return $this;
    }
}
