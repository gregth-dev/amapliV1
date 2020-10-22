<?php

namespace App\Entity;

use App\Repository\ProductOrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductOrderRepository::class)
 */
class ProductOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="productOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriber;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="productOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $command;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * class css à appliquer en fonction du statut
     *
     * @var string
     */
    private $class;

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

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
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
