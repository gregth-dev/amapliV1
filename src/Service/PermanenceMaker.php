<?php

namespace App\Service;

use DateTime;
use App\Entity\Contract;
use App\Entity\Delivery;
use App\Entity\Permanence;
use App\Repository\PermanenceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Configure les dates de livraison du contrat
 */
class PermanenceMaker
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function setPermanences(Permanence $permanence)
    {
        $startDate = $permanence->getStartDate()->format('Y-m-d');
        $endDate = $permanence->getEndDate()->format('Y-m-d');
        $frequency = $permanence->getFrequency();
        $numberPlaces = $permanence->getNumberPlaces();
        $this->setPermanence($numberPlaces, $startDate);
        while ($startDate < $endDate) {
            $startDate = date('Y-m-d', strtotime($startDate . $frequency));
            if ($startDate <= $endDate) {
                $this->setPermanence($numberPlaces, $startDate);
            }
        }
        if ($startDate != $endDate)
            $this->setPermanence($numberPlaces, $endDate);
    }

    public function setPermanence($numberPlaces, $startDate)
    {
        $permanence = new Permanence();
        $permanence->setDate(new DateTime($startDate));
        $permanence->setNumberPlaces($numberPlaces);
        $this->manager->persist($permanence);
    }
}
