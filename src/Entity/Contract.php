<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use App\Entity\Delivery;
use App\Entity\Producer;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ContractRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContractRepository::class)
 */
class Contract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Valeur du champ incorrect: minimum 2 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $frequency;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * 
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startDate",
     *     message="La date de fin de distribution doit être supérieure ou égale à celle de début de distribution"
     * )
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\Column(type="boolean")
     */
    private $multidistribution;

    /**
     * @ORM\ManyToOne(targetEntity=Producer::class, inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $producer;

    /**
     * @ORM\OneToMany(targetEntity=Delivery::class, mappedBy="contract", orphanRemoval=true)
     */
    private $deliveries;

    /**
     * année du contrat
     *
     * @var string
     */
    private $year;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=ContractMember::class, mappedBy="contract", orphanRemoval=true)
     */
    private $contractMembers;

    /**
     * Nom et année du contrat
     *
     * @var string
     */
    private $fullName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $emailAuto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frequencyEmailAuto;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $this->status = 'non actif';
        $this->contractMembers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        if ($this->endDate < new DateTime('now') && $this->status == 'actif')
            $this->status = 'à archiver';
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    public function getMultidistribution(): ?bool
    {
        return $this->multidistribution;
    }

    public function setMultidistribution(bool $multidistribution): self
    {
        $this->multidistribution = $multidistribution;

        return $this;
    }

    public function getProducer(): ?Producer
    {
        return $this->producer;
    }

    public function setProducer(?Producer $producer): self
    {
        $this->producer = $producer;

        return $this;
    }

    /**
     * @return Collection|Delivery[]
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function addDelivery(Delivery $delivery): self
    {
        if (!$this->deliveries->contains($delivery)) {
            $this->deliveries[] = $delivery;
            $delivery->setContract($this);
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): self
    {
        if ($this->deliveries->contains($delivery)) {
            $this->deliveries->removeElement($delivery);
            // set the owning side to null (unless already changed)
            if ($delivery->getContract() === $this) {
                $delivery->setContract(null);
            }
        }

        return $this;
    }

    public function getYear(): ?string
    {
        $this->year = date('Y', $this->startDate->getTimestamp());
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|ContractMember[]
     */
    public function getContractMembers(): Collection
    {
        return $this->contractMembers;
    }

    public function addContractMember(ContractMember $contractMember): self
    {
        if (!$this->contractMembers->contains($contractMember)) {
            $this->contractMembers[] = $contractMember;
            $contractMember->setContract($this);
        }

        return $this;
    }

    public function removeContractMember(ContractMember $contractMember): self
    {
        if ($this->contractMembers->contains($contractMember)) {
            $this->contractMembers->removeElement($contractMember);
            // set the owning side to null (unless already changed)
            if ($contractMember->getContract() === $this) {
                $contractMember->setContract(null);
            }
        }

        return $this;
    }

    public function getFullName()
    {
        return ucfirst($this->name) . ' ' . $this->getYear();
    }

    public function getEmailAuto(): ?bool
    {
        return $this->emailAuto;
    }

    public function setEmailAuto(?bool $emailAuto): self
    {
        $this->emailAuto = $emailAuto;

        return $this;
    }

    public function getFrequencyEmailAuto(): ?string
    {
        return $this->frequencyEmailAuto;
    }

    public function setFrequencyEmailAuto(?string $frequencyEmailAuto): self
    {
        $this->frequencyEmailAuto = $frequencyEmailAuto;

        return $this;
    }
}
