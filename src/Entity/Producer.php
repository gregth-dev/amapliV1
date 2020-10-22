<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProducerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProducerRepository::class)
 * @UniqueEntity(fields = {"name"},message ="Ce nom existe déjà")
 * @UniqueEntity(fields = {"checkOrder"},message ="Cet ordre existe déjà")
 */
class Producer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Valeur du champ incorrect: minimum 2 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Valeur du champ incorrect: minimum 2 caractères")
     */
    private $checkOrder;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="producersReferent")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $referent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="producersSubstitute")
     */
    private $substitute;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="producer")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Contract::class, mappedBy="producer", orphanRemoval=true)
     */
    private $contracts;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="producer")
     */
    private $payments;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->payments = new ArrayCollection();
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

    public function getCheckOrder(): ?string
    {
        return $this->checkOrder;
    }

    public function setCheckOrder(string $checkOrder): self
    {
        $this->checkOrder = $checkOrder;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReferent(): ?User
    {
        return $this->referent;
    }

    public function setReferent(?User $referent): self
    {
        $this->referent = $referent;

        return $this;
    }

    public function getSubstitute(): ?User
    {
        return $this->substitute;
    }

    public function setSubstitute(?User $substitute): self
    {
        $this->substitute = $substitute;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setProducer($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getProducer() === $this) {
                $product->setProducer(null);
            }
        }

        return $this;
    }

    /**
     * Met à jour les rôles des users lors de la création
     *
     * @param User $user
     * @param User $newUser
     * @return void
     */
    public function defineRole()
    {
        $this->getUser()->setRoles(['ROLE_PRODUCER']);
        $this->getUser()->setMemberType('Producteur');
    }

    /**
     * Met à jour les rôles des users lors de l'update
     *
     * @param User $user
     * @param User $newUser
     * @return void
     */
    public function updateRole($user, $newUser)
    {
        $user->setRoles([]);
        $user->setMemberType('Adhérent');
        $newUser->setRoles(['ROLE_PRODUCER']);
        $newUser->setMemberType('Producteur');
    }

    /**
     * Met à jour les rôles des users lors de la suppression
     *
     * @param User $user
     * @param User $newUser
     * @return void
     */
    public function deleteRole()
    {
        $this->getUser()->setRoles([]);
        $this->getUser()->setMemberType('Adhérent');
    }

    /**
     * @return Collection|Contract[]
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts[] = $contract;
            $contract->setProducer($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->contains($contract)) {
            $this->contracts->removeElement($contract);
            // set the owning side to null (unless already changed)
            if ($contract->getProducer() === $this) {
                $contract->setProducer(null);
            }
        }

        return $this;
    }

    /**
     * Renvoi un text si des produits sont associés, null dans le cas contraire
     *
     * @return void
     */
    public function checkProduct()
    {
        $products = $this->getProducts()->toArray();
        if ($products) {
            $ids = [];
            foreach ($products as $product) {
                $ids[] = $product->getId();
            }
            $idList = implode(" et ", $ids);
            if (count($ids) > 1) {
                $text =  "les produits n°$idList sont associés à ce producteur";
            } else {
                $text =  "le produit n°$idList est associé à ce producteur";
            }
            return $text;
        }
        return null;
    }

    /**
     * Renvoi un text si des contrats sont associés, null dans le cas contraire
     *
     * @return void
     */
    public function checkContract()
    {
        $contracts = $this->getContracts()->toArray();
        if ($contracts) {
            $ids = [];
            foreach ($contracts as $contract) {
                $ids[] = $contract->getId();
            }
            $idList = implode(" et ", $ids);
            if (count($ids) > 1) {
                $text =  "des contrats n°$idList sont associés à ce producteur";
            } else {
                $text =  "un contrat n°$idList est associé à ce producteur";
            }
            return $text;
        }
        return null;
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
            $payment->setProducer($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getProducer() === $this) {
                $payment->setProducer(null);
            }
        }

        return $this;
    }
}
