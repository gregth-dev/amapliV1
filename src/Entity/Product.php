<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @Assert\Length(min=2, minMessage="Valeur du champ incorrect: minimum 2 caractères")
     */
    private $details;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float",message="La valeur {{ value }} n'est pas une valeur valide.")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Positive(message="Le nombre doit être positif")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Producer::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $producer;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="product", orphanRemoval=true)
     */
    private $orders;

    /**
     * Nom de produit et détails
     *
     * @var string
     */
    private $fullName;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deposit;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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
            $order->setProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getProduct() === $this) {
                $order->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of fullName
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->details;
    }

    public function getDeposit(): ?float
    {
        return $this->deposit;
    }

    public function setDeposit(?float $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }
}
