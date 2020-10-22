<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentPDF
{
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
     *     message="La date de la dernière distribution doit être supérieur ou égale à la date de la 1ère distribution"
     * )
     *
     * @var datetime
     */
    private $endDate;

    /**
     * status du paiement dans la recherche
     *
     * @return string
     */
    private $status;

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
     * Get status du paiement dans la recherche
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status du paiement dans la recherche
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
