<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer",message="La valeur de ce champ est incorrect.")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $unitPrice;

    /**
     * @ORM\ManyToMany(targetEntity=Delivery::class, inversedBy="orders", cascade={"persist"})
     */
    private $deliveries;

    /**
     * @ORM\ManyToOne(targetEntity=ContractMember::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contractMember;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrder::class, mappedBy="command", orphanRemoval=true)
     */
    private $productOrders;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
        $this->productOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): self
    {
        if ($this->deliveries->contains($delivery)) {
            $this->deliveries->removeElement($delivery);
        }

        return $this;
    }

    public function getContractMember(): ?ContractMember
    {
        return $this->contractMember;
    }

    public function setContractMember(?ContractMember $contractMember): self
    {
        $this->contractMember = $contractMember;

        return $this;
    }

    /**
     * @return Collection|ProductOrder[]
     */
    public function getProductOrders(): Collection
    {
        return $this->productOrders;
    }

    public function addProductOrder(ProductOrder $productOrder): self
    {
        if (!$this->productOrders->contains($productOrder)) {
            $this->productOrders[] = $productOrder;
            $productOrder->setCommand($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->productOrders->contains($productOrder)) {
            $this->productOrders->removeElement($productOrder);
            // set the owning side to null (unless already changed)
            if ($productOrder->getCommand() === $this) {
                $productOrder->setCommand(null);
            }
        }

        return $this;
    }
}
