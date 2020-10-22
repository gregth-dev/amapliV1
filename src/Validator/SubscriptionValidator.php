<?php

namespace App\Validator;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;

class SubscriptionValidator
{

    private $sr;
    public $year;
    public $error;

    public function __construct(SubscriptionRepository $sr)
    {
        $this->sr = $sr;
    }

    /**
     * Valide les paiements lors d'une adhésion
     *
     * @param Subscription $subscription
     * @return boolean
     */
    public function validateSubscriptionPayment(Subscription $subscription): bool
    {

        $subscriptionOrganisms = $subscription->getOrganism()->toArray();
        $arrayOrganisms = [];
        $subscriptionPayments = $subscription->getPayment()->toArray();
        $arrayPayments = [];
        foreach ($subscriptionOrganisms as $organism) {
            $arrayOrganisms[] = $organism->getName();
        }
        foreach ($subscriptionPayments as $payment) {
            $arrayPayments[] = $payment->getCheckOrder();
        }
        if (count($subscriptionOrganisms) != count($subscriptionPayments)) {
            $this->error = "Le nombre de chèques doit être égal au nombre d'associations";
            return false;
        }
        foreach ($subscriptionPayments as $payment) {
            if (!in_array($payment->getCheckOrder(), $arrayOrganisms)) {
                $this->error = "Vérifier l'ordre des chèques";
                return false;
            }
        }
        foreach ($subscriptionOrganisms as $organism) {
            if (!in_array($organism->getName(), $arrayPayments)) {
                $this->error = "Vérifier l'ordre des chèques";
                return false;
            }
        }
        return true;
    }

    /**
     * Valide l'adhérent lors d'une adhésion
     *
     * @param Subscription $subscription
     * @param SubscriptionRepository $sr
     * @return boolean
     */
    public function validateSubscriptionSubscriber(Subscription $subscription): bool
    {
        $this->year = $subscription->getYear();
        if ($this->sr->findOneBy(['subscriber' => $subscription->getSubscriber(), 'year' => $this->year])) {
            return false;
        }
        return true;
    }
}
