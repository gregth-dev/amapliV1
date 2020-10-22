<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Emargement
{
    /**
     * Contract
     *
     * @var Contract
     */
    private $contract;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @var datetime
     */
    private $startDate;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startDate",
     *     message="La date de fin doit être supérieure ou égale à la date de début"
     * )
     *
     * @var datetime
     */
    private $endDAte;

    /**
     * Nom du document
     * @Assert\Length(min=5, minMessage="Le nom du document doit faire au minimum 5 caractères")
     * @var string
     */
    private $docName;

    /**
     * Indique si le document est à sauvegarder
     *
     * @var bool
     */
    private $saveDoc;

    /**
     * Orientation du document
     *
     * @var string
     */
    private $orientation;

    /**
     * Get the value of contract
     */
    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    /**
     * Set the value of contract
     *
     * @return  self
     */
    public function setContract(?Contract $contract)
    {
        $this->contract = $contract;

        return $this;
    }

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
     * Get the value of endDAte
     */
    public function getEndDate()
    {
        return $this->endDAte;
    }

    /**
     * Set the value of endDAte
     *
     * @return  self
     */
    public function setEndDAte($endDAte)
    {
        $this->endDAte = $endDAte;

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
