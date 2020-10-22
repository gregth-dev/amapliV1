<?php

namespace App\Service;

use DateTime;
use App\Entity\Contract;
use App\Entity\Delivery;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class dédiée à la configuration des dates de livraison d'un contrat producteur
 */
class ContractDeliveryMaker
{

    private $manager;
    private $dr;

    public function __construct(EntityManagerInterface $manager, DeliveryRepository $dr)
    {
        $this->manager = $manager;
        $this->dr = $dr;
    }

    /**
     * Créé les livraisons entre la date de début et de fin basé sur la fréquence
     *
     * @param Contract $contract
     * @return void
     */
    public function setDeliveries(Contract $contract)
    {
        $startDate = $contract->getStartDate()->format('Y-m-d');
        $endDate = $contract->getEndDate()->format('Y-m-d');
        $this->setDelivery($contract, $startDate);

        if (($frequency = $contract->getFrequency()) != 'null') {
            while ($startDate < $endDate) {
                $startDate = date('Y-m-d', strtotime($startDate . $frequency));
                if ($startDate <= $endDate) {
                    $this->setDelivery($contract, $startDate);
                }
            }
        }
        if ($startDate != $endDate)
            $this->setDelivery($contract, $endDate);
        foreach ($contract->getDeliveries() as $delivery) {
            $delivery->setContract($contract);
            $this->manager->persist($delivery);
        }
    }

    /**
     * Créé et persist une livraison
     *
     * @param Contract $contract
     * @param string $startDate
     * @return void
     */
    public function setDelivery(Contract $contract, string $startDate)
    {
        $delivery = new Delivery();
        $delivery->setDate(new DateTime($startDate));
        $delivery->setContract($contract);
        $delivery->setStatus('Validée');
        $contract->addDelivery($delivery);
        $this->manager->persist($delivery);
    }

    public function deleteDelivery(Contract $contract)
    {
        $deliveries = $this->dr->findBy(['contract' => $contract->getId()]);
        foreach ($deliveries as $delivery) {
            $this->manager->remove($delivery);
            $this->manager->flush();
        }
    }

    /**
     * Elimine les dates en double lors de la création ou mise à jour d'un contrat
     *
     * @param Contract $contract
     * @return void
     */
    public function deleteDouble(Contract $contract)
    {
        //utilisé à la place de getDeliveries pour garder l'ordre croissant lors du tri
        $deliveries = $this->dr->findBy(['contract' => $contract]);
        $tab = [];
        //on créé un tableau de dates
        foreach ($deliveries as $delivery) {
            $tab[$delivery->getId()] = $delivery->getDate()->format('Y-m-d');
        }
        //on créé un tableau des dates à éliminer, différence entre toutes les dates et les dates sans doubles.
        foreach (array_diff_key($tab, array_unique($tab)) as $key => $value) {
            $delivery = $this->dr->find($key);
            $this->manager->remove($delivery);
        }
        $this->manager->flush();
    }

    /**
     * Vérifie que les dates ajoutée se trouve entre la date de début et celle de fin
     *
     * @param Contract $contract
     * @return void
     */
    public function validateDeliveries(Contract $contract)
    {
        foreach ($contract->getDeliveries() as $delivery) {
            $deliverieDate = $delivery->getDate()->format('Y-m-d');
            if (!($deliverieDate >= $contract->getStartDate()->format('Y-m-d') &&
                $deliverieDate <= $contract->getEndDate()->format('Y-m-d')))
                return false;
        }
        return true;
    }
}
