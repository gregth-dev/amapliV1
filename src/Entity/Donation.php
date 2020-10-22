<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\DonationPayment;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DonationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DonationRepository::class)
 */
class Donation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float",message="Cette valeur n'est pas valide.")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Positive(message="Le nombre doit être positif")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="donations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $donor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=DonationPayment::class, mappedBy="donation", orphanRemoval=true, cascade="persist")
     */
    private $payment;

    /**
     * total des paiements
     *
     * @var float
     */
    private $totalPayments;

    /**
     * différence entre les paiements et la donation
     *
     * @var float
     */
    private $diffTotal;

    public function __construct()
    {
        $this->payment = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDonor(): ?User
    {
        return $this->donor;
    }

    public function setDonor(?User $donor): self
    {
        $this->donor = $donor;

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
     * @return Collection|DonationPayment[]
     */
    public function getPayment(): Collection
    {
        return $this->payment;
    }

    public function addPayment(DonationPayment $payment): self
    {
        if (!$this->payment->contains($payment)) {
            $this->payment[] = $payment;
            $payment->setDonation($this);
        }

        return $this;
    }

    public function removePayment(DonationPayment $payment): self
    {
        if ($this->payment->contains($payment)) {
            $this->payment->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getDonation() === $this) {
                $payment->setDonation(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of totalPayments
     */
    public function getTotalPayments()
    {
        $totalPayments = 0;
        foreach ($this->getPayment() as $payment) {
            $totalPayments += $payment->getAmount();
        }
        $this->totalPayments = $totalPayments;
        return $this->totalPayments;
    }

    public function isEqualAmount()
    {
        if (!($this->amount == $this->getTotalPayments())) {
            $this->diffTotal = $this->totalPayments - $this->amount;
            return false;
        }

        return true;
    }

    /**
     * Get the value of diffTotal
     */
    public function getDiffTotal()
    {
        return $this->diffTotal;
    }
}
