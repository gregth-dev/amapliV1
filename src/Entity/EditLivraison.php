<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class EditLivraison
{
    /**
     * Producer
     *
     * @var Producer
     */
    private $producer;

    /**
     * @Assert\Type("\DateTimeInterface")
     * 
     * @var datetime
     */
    private $startDate;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startDate",
     *     message="La date de fin de période doit être supérieur à celle de début de période"
     * )
     *
     * @var datetime
     */
    private $endDate;

    /**
     * Nom du document
     * @Assert\Length(min=5, minMessage="Le nom du document doit faire au minimum 5 caractères")
     * @var string
     */
    private $docName;

    /**
     * Valide la sauvegarder
     *
     * @var bool
     */
    private $saveDoc;

    /**
     * Orientation du doc à l'édition
     *
     * @var string
     */
    private $orientation;

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
     * Get the value of docName
     */
    public function getDocName(): ?string
    {
        $this->docName ?? false;
        return $this->docName;
    }

    /**
     * Set the value of docName
     *
     * @return  self
     */
    public function setDocName(?string $docName)
    {
        $this->docName = $docName;

        return $this;
    }

    /**
     * Get the value of saveDoc
     */
    public function getSaveDoc(): bool
    {
        return $this->saveDoc;
    }

    /**
     * Set the value of saveDoc
     *
     * @return  self
     */
    public function setSaveDoc(bool $saveDoc)
    {
        $this->saveDoc = $saveDoc;

        return $this;
    }

    /**
     * Get the value of orientation
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Set the value of orientation
     *
     * @return  self
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }
}
