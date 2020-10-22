<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SubscriptionPaymentRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubscriptionPaymentRepository::class)
 */
class SubscriptionPayment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer",message="Cette valeur n'est pas valide.")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Positive(message="Le nombre doit être positif")
     */
    private $checkNumber;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float",message="Cette valeur n'est pas valide.")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Positive(message="Le nombre doit être positif")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "La date doit être supérieure ou égale à aujourd'hui"
     * )
     */
    private $depositDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Valeur du champ incorrect: minimum 2 caractères")
     */
    private $checkOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="payment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscription;

    /**
     * Class css
     *
     * @var string
     */
    private $class;

    public function __construct()
    {
        $this->status = "à remettre";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckNumber(): ?string
    {
        return $this->checkNumber;
    }

    public function setCheckNumber(string $checkNumber): self
    {
        $this->checkNumber = $checkNumber;

        return $this;
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

    public function getDepositDate(): ?\DateTimeInterface
    {
        return $this->depositDate;
    }

    public function setDepositDate(\DateTimeInterface $depositDate): self
    {
        $this->depositDate = $depositDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        if ($this->status == 'à remettre' && $this->depositDate < new DateTime('now'))
            $this->status = 'non remis';
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

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
            case 'remis':
                $this->class = 'badge-success';
                break;
            case 'non remis':
                $this->class = 'badge-danger';
                break;
            case 'à remettre':
                $this->class = 'badge-secondary';
                break;
        }

        return $this->class;
    }
}
