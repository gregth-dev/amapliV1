<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentList
{
    /**
     * Producer
     *
     * @var Producer
     */
    private $producer;

    /**
     * statut du paiement
     *
     * @var string
     */
    private $status;

    /**
     * @Assert\Type("\DateTimeInterface")
     * 
     * @var datetime
     */
    private $startDate;

    /**
     * *@Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startDate",
     *     message="La date de fin de période doit être supérieur à celle de début de période"
     * )
     *
     * @var datetime
     */
    private $endDate;

    /**
     * Get the value of startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @return  self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @return  self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get producer
     *
     * @return  Producer
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * Set producer
     *
     * @param  Producer  $producer  Producer
     *
     * @return  self
     */
    public function setProducer(Producer $producer)
    {
        $this->producer = $producer;

        return $this;
    }

    /**
     * Get statut du paiement
     *
     * @return  string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set statut du paiement
     *
     * @param  string  $status  statut du paiement
     *
     * @return  self
     */
    public function setStatus(?string $status)
    {
        $this->status = $status;

        return $this;
    }
}
