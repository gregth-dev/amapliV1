<?php

namespace App\Entity;

use App\Repository\PermanenceRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PermanenceRepository::class)
 */
class Permanence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "La date doit être supérieure ou égale à aujourd'hui"
     * )
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer",message="Cette valeur n'est pas valide.")
     * @Assert\Positive(message="Le nombre doit être positif")
     */
    private $numberPlaces;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="permanences")
     */
    private $participants;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * Date de début
     *
     * @var datetime
     */
    private $startDate;

    /**
     * date de fin
     *
     * @var datetime
     */
    private $endDate;

    /**
     * Fréquence de la permanence
     *
     * @var string
     */
    private $frequency;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNumberPlaces(): ?int
    {
        return $this->numberPlaces;
    }

    public function setNumberPlaces(int $numberPlaces): self
    {
        $this->numberPlaces = $numberPlaces;

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

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

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
     * Get the value of startDate
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @return  self
     */
    public function setStartDate(\DateTimeInterface $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @return  self
     */
    public function setEndDate(\DateTimeInterface $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of frequency
     */
    public function getFrequency(): string
    {
        return $this->frequency;
    }

    /**
     * Set the value of frequency
     *
     * @return  self
     */
    public function setFrequency(string $frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }
}
