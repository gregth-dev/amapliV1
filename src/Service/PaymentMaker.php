<?php

namespace App\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PaymentMaker
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function setStatus(string $repository)
    {
        $flush = false;
        $payments = $this->manager->getRepository($repository)->findAll();
        foreach ($payments as $payment) {
            if ($payment->getStatus() == 'non remis') {
                $payment->setStatus('non remis');
                $this->manager->persist($payment);
                $flush = true;
            }
        }
        if ($flush)
            $this->manager->flush();
    }
}
