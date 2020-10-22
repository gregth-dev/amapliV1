<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\Contract;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\ContractMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ContractMemberRepository::class)
 */
class ContractMember
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Contract::class, inversedBy="contractMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contract;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="contractMember", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="contractMember", orphanRemoval=true, cascade={"persist"})
     */
    private $payments;

    /**
     * @ORM\Column(type="float")
     */
    private $balance;

    /**
     * @ORM\Column(type="float")
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contractMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriber;

    /**
     * Tableaux d'objet User
     */
    private $subscribers;

    /**
     * @var Product
     */
    private $product;

    private $totalOrders;
    private $totalDeliveries;
    private $amountOrders;
    private $amountPayments;
    private $amountActivePayments;
    private $amountDeposit;
    private $statePayments;
    private $statePaymentsClass;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $this->status = 'non actif';
        $this->totalAmount = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setContractMember($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getContractMember() === $this) {
                $order->setContractMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setContractMember($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getContractMember() === $this) {
                $payment->setContractMember(null);
            }
        }

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getStatus(): ?string
    {
        if ($this->contract->getStatus() == 'archivé' && $this->status == 'actif')
            $this->status = 'à archiver';

        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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
     * Get the value of amountOrders
     */
    public function getAmountOrders(): float
    {
        $orders = $this->getOrders()->toArray();
        $this->amountOrders = 0;
        foreach ($orders as $order) {
            $price = $order->getProduct()->getPrice();
            $this->amountOrders += ($price * $this->getTotalDeliveries()) * $order->getQuantity();
        }

        $this->amountOrders = floatval($this->amountOrders);
        $this->setTotalAmount($this->amountOrders);
        return $this->amountOrders;
    }

    public function getTotalOrders(): int
    {
        $orders = $this->getOrders()->toArray();
        $this->totalOrders = 0;
        foreach ($orders as $order) {
            foreach ($order->getProductOrders()->toArray() as $order) {
                $this->totalOrders += 1;
            }
        }

        return $this->totalOrders;
    }

    public function getTotalDeliveries(): int
    {
        $orders = $this->getOrders()->toArray();
        $this->totalDeliveries = [];
        foreach ($orders as $order) {
            foreach ($order->getProductOrders()->toArray() as $order) {
                $this->totalDeliveries[] = $order->getDate()->format('d-m-Y');
            }
        }

        return count(array_unique($this->totalDeliveries));
    }

    /**
     * Get the value of amountPayments
     */
    public function getAmountPayments(): float
    {
        $this->amountPayments = 0;
        foreach ($this->getPayments()->toArray() as $payment) {
            $payment->setContractMember($this);
            $this->amountPayments += $payment->getAmount();
        }
        return floatval($this->amountPayments);
    }

    public function getAmountDeposit(): float
    {
        $orders = $this->getOrders()->toArray();
        $this->amountDeposit = 0;
        foreach ($orders as $order) {
            foreach ($order->getDeliveries() as $delivery) {
                $this->amountDeposit += $order->getProduct()->getDeposit() * $order->getQuantity();
            }
        }
        return $this->amountDeposit;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }


    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }



    /**
     * Get the value of amountActivePayments
     */
    public function getAmountActivePayments()
    {
        $payments = $this->getPayments()->toArray();
        $this->amountActivePayments = 0;
        foreach ($payments as $payment) {
            if ($payment->getStatus() == 'remis')
                $this->amountActivePayments += $payment->getAmount();
        }

        return $this->amountActivePayments;
    }

    /**
     * Set the value of product
     *
     * @param  Product  $product
     *
     * @return  self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of statePayments
     */
    public function getStatePayments()
    {
        if (!$this->balance && $this->status == 'actif')
            $this->statePayments = "Soldé";
        elseif ($payments = $this->getPayments()->toArray()) {
            foreach ($payments as $payment) {
                if ($payment->getStatus() == 'non remis') {
                    return $this->statePayments = "Des chèques sont à remettre";
                } else
                    $this->statePayments = "Aucun chèque à remettre";
            }
        } elseif ($this->status == "non actif" && !$this->balance)
            $this->statePayments = "Le contrat n'a pas été enregistré";
        else
            $this->statePayments = "En attente de paiements";
        return $this->statePayments;
    }



    /**
     * Get the value of statePaymentsClass
     */
    public function getStatePaymentsClass()
    {
        switch ($this->getStatePayments()) {
            case 'Soldé':
                $this->statePaymentsClass = "badge-success";
                break;
            case 'Des chèques sont à remettre':
                $this->statePaymentsClass = "badge-danger";
                break;
            default:
                $this->statePaymentsClass = "badge-warning";
                break;
        }
        return $this->statePaymentsClass;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }
}
