<?php

namespace App\Entity;

use App\Repository\DeliveryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryRepository::class)
 */
class Delivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Contract::class, inversedBy="deliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contract;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="deliveries")
     */
    private $orders;

    /**
     * Date formatée
     *
     * @var string
     */
    private $dateString;

    /**
     * class css à appliquer en fonction du statut
     *
     * @var string
     */
    private $class;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getStatus(): ?string
    {
        if ($this->status == null)
            $this->status = "Validée";
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
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
            $order->addDelivery($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeDelivery($this);
        }

        return $this;
    }

    public function getDateString()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get class css à appliquer en fonction du statut
     *
     * @return  string
     */
    public function getClass()
    {
        switch ($this->getStatus()) {
            case 'Validée':
                $this->class = 'badge-success my-2';
                break;
            case 'Annulée':
                $this->class = 'badge-danger my-2';
                break;
            case 'Reportée':
                $this->class = 'badge-warning my-2';
                break;
            case 'A Confirmer':
                $this->class = 'badge-dark my-2';
                break;
        }

        return $this->class;
    }
}
