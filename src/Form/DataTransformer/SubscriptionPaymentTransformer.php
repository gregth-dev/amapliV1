<?php

namespace App\Form\DataTransformer;

use App\Entity\Organism;
use App\Repository\OrganismRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class qui gère la transformation du champ checkOrder
 */
class SubscriptionPaymentTransformer implements DataTransformerInterface
{
    private $manager;
    private $or;

    public function __construct(EntityManagerInterface $manager, OrganismRepository $or)
    {
        $this->manager = $manager;
        $this->or = $or;
    }

    public function transform($organism)
    {
        $organism = $this->or->findAll();
        return $organism[0];
    }

    /**
     * On récupère le nom
     *
     * @param Organism $organism
     * @return void
     */
    public function reverseTransform($organism)
    {
        if (!$organism)
            throw new TransformationFailedException("Vous devez sélectionner une association");
        $name = $organism->getName();
        if (!$name)
            throw new TransformationFailedException("Format de name incorrect");
        return $name;
    }
}
