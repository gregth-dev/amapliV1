<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\Organism;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\SubscriptionPayment;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriber;

    /**
     * @ORM\ManyToMany(targetEntity=Organism::class, inversedBy="subscriptions")
     */
    private $organism;

    /**
     * @ORM\OneToMany(targetEntity=SubscriptionPayment::class, mappedBy="subscription", orphanRemoval=true)
     */
    private $payment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $year;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    /**
     * Total à payer lors de l'adhésion
     *
     * @var float
     */
    private $totalOrganismPayment;

    /**
     * Total des paiements lors de l'adhésion
     *
     * @var float
     */
    private $totalPayments;

    /**
     * Difference entre le total à payer et le total des paiements
     *
     * @var float
     */
    private $diffTotal;

    public function __construct()
    {
        $this->organism = new ArrayCollection();
        $this->payment = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $this->year = date('Y', $this->createdAt->getTimestamp());
        $this->isValid = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?User $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * @return Collection|Organism[]
     */
    public function getOrganism(): Collection
    {
        return $this->organism;
    }

    public function addOrganism(Organism $organism): self
    {
        if (!$this->organism->contains($organism)) {
            $this->organism[] = $organism;
        }

        return $this;
    }

    public function removeOrganism(Organism $organism): self
    {
        if ($this->organism->contains($organism)) {
            $this->organism->removeElement($organism);
        }

        return $this;
    }

    /**
     * @return Collection|SubscriptionPayment[]
     */
    public function getPayment(): Collection
    {
        return $this->payment;
    }

    public function addPayment(SubscriptionPayment $payment): self
    {
        if (!$this->payment->contains($payment)) {
            $this->payment[] = $payment;
            $payment->setSubscription($this);
        }

        return $this;
    }

    public function removePayment(SubscriptionPayment $payment): self
    {
        if ($this->payment->contains($payment)) {
            $this->payment->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getSubscription() === $this) {
                $payment->setSubscription(null);
            }
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

    public function getYear(): ?string
    {
        $this->year = date('Y', $this->createdAt->getTimestamp());

        return $this->year;
    }

    /**
     * Get the value of totalOrganismPayment
     */
    public function getTotalOrganismPayment(): float
    {
        $this->totalOrganismPayment = 0;
        foreach ($this->getOrganism() as $organism) {
            $this->totalOrganismPayment += $organism->getAmount();
        }
        return $this->totalOrganismPayment;
    }

    /**
     * Get the value of totalPayments
     */
    public function getTotalPayments(): float
    {
        $this->totalPayments = 0;
        foreach ($this->getPayment() as $payment) {
            $this->totalPayments += $payment->getAmount();
        }
        return $this->totalPayments;
    }

    public function isEqualAmount()
    {
        if (!($this->getTotalOrganismPayment() == $this->getTotalPayments())) {
            $this->setDiffTotal($this->totalOrganismPayment - $this->totalPayments);
            return false;
        }

        return true;
    }

    /**
     * Get the value of diffTotal
     */
    public function getDiffTotal(): float
    {
        return $this->diffTotal;
    }

    /**
     * Get indique si l'adhésion a été validée
     *
     * @return  bool
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set indique si l'adhésion a été validée
     *
     * @param  bool  $isValid  indique si l'adhésion a été validée
     *
     * @return  self
     */
    public function setIsValid(bool $isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Set the value of diffTotal
     *
     * @return  self
     */
    public function setDiffTotal(int $diffTotal)
    {
        $this->diffTotal = $diffTotal;

        return $this;
    }
}
