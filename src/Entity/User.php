<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields = {"email"}, groups={"registration"}, message ="Cet email existe déjà")
 * @UniqueEntity(fields = {"email2"}, groups={"registration"}, message ="Cet email existe déjà")
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Email(message = "L'email n'est pas valide.")
     * @AppAssert\Email(groups={"registration"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, nullable=true, unique=true)
     * @Assert\Email(message = "L'email n'est pas valide.")
     * @AppAssert\Email2(groups={"registration"})
     * 
     */
    private $email2;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Le prénom doit faire au minimum 2 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="Le nom doit faire au minimum 2 caractères")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=5, minMessage="L'adresse doit faire au minimum 5 caractères")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=2, minMessage="La ville doit faire au minimum 2 caractères")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="#^[0-9]{5}$#",
     *     match=true,
     *     message="Code Postal invalide"
     * )
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @AppAssert\Telephone()
     */
    private $phone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $memberType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Producer::class, mappedBy="referent")
     */
    private $producersReferent;

    /**
     * @ORM\OneToMany(targetEntity=Producer::class, mappedBy="substitute")
     */
    private $producersSubstitute;

    /**
     * @ORM\OneToMany(targetEntity=ContractMember::class, mappedBy="subscriber", orphanRemoval=true)
     */
    private $contractMembers;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrder::class, mappedBy="subscriber", orphanRemoval=true)
     */
    private $productOrders;

    /**
     * @ORM\ManyToMany(targetEntity=Permanence::class, mappedBy="participants")
     */
    private $permanences;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="subscriber", orphanRemoval=true)
     */
    private $subscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Donation::class, mappedBy="donor")
     */
    private $donations;

    /**
     * nom et prénom
     *
     * @var string
     */
    private $fullName;

    /**
     * Champ role de UserRoleType
     *
     * @var string
     */
    private $role;

    public function __construct()
    {
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $this->memberType = 'Adhérent';
        $this->producers = new ArrayCollection();
        $this->contractMembers = new ArrayCollection();
        $this->productOrders = new ArrayCollection();
        $this->permanences = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->donations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_MEMBER
        $roles[] = 'ROLE_MEMBER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return ucfirst($this->firstName);
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return ucfirst($this->lastName);
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(string $phone1): self
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getMemberType(): ?string
    {
        return $this->memberType;
    }

    public function setMemberType(): self
    {
        switch (strtolower(substr($this->getRoles()[0], 5))) {
            case 'member':
                $this->memberType = 'Adhérent';
                break;
            case 'producer':
                $this->memberType = 'Producteur';
                break;
            case 'referent':
                $this->memberType = 'Référent';
                break;
            case 'treasurer':
                $this->memberType = 'Trésorier';
                break;
            case 'admin':
                $this->memberType = 'Administrateur';
                break;
            default:
                $this->memberType = 'Adhérent';
                break;
        }

        return $this;
    }

    /**
     * Get the value of fullName
     */
    public function getFullName()
    {
        return ucfirst($this->firstName)  . ' ' . ucfirst($this->lastName);
    }


    /**
     * Get champ role de UserRoleType
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set champ role de UserRoleType
     *
     * @param  string  $role  Champ role de UserRoleType
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        $this->role = $role;

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
     * @return Collection|Producer[]
     */
    public function getProducersReferent(): Collection
    {
        return $this->producersReferent;
    }

    public function addProducerReferent(Producer $producer): self
    {
        if (!$this->producersReferent->contains($producer)) {
            $this->producersReferent[] = $producer;
            $producer->setReferent($this);
        }

        return $this;
    }

    public function removeProducerReferent(Producer $producer): self
    {
        if ($this->producersReferent->contains($producer)) {
            $this->producersReferent->removeElement($producer);
            // set the owning side to null (unless already changed)
            if ($producer->getReferent() === $this) {
                $producer->setReferent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Producer[]
     */
    public function getProducersSubstitute(): Collection
    {
        return $this->producersSubstitute;
    }

    public function addProducerSubstitute(Producer $producer): self
    {
        if (!$this->producersSubstitute->contains($producer)) {
            $this->producersSubstitute[] = $producer;
            $producer->setSubstitute($this);
        }

        return $this;
    }

    public function removeProducerSubstitute(Producer $producer): self
    {
        if ($this->producersSubstitute->contains($producer)) {
            $this->producersSubstitute->removeElement($producer);
            // set the owning side to null (unless already changed)
            if ($producer->getSubstitute() === $this) {
                $producer->setSubstitute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContractMember[]
     */
    public function getContractMembers(): Collection
    {
        return $this->contractMembers;
    }

    public function addContractMember(ContractMember $contractMember): self
    {
        if (!$this->contractMembers->contains($contractMember)) {
            $this->contractMembers[] = $contractMember;
            $contractMember->setSubscriber($this);
        }

        return $this;
    }

    public function removeContractMember(ContractMember $contractMember): self
    {
        if ($this->contractMembers->contains($contractMember)) {
            $this->contractMembers->removeElement($contractMember);
            // set the owning side to null (unless already changed)
            if ($contractMember->getSubscriber() === $this) {
                $contractMember->setSubscriber(null);
            }
        }

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
            $productOrder->setSubscriber($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->productOrders->contains($productOrder)) {
            $this->productOrders->removeElement($productOrder);
            // set the owning side to null (unless already changed)
            if ($productOrder->getSubscriber() === $this) {
                $productOrder->setSubscriber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Permanence[]
     */
    public function getPermanences(): Collection
    {
        return $this->permanences;
    }

    public function addPermanence(Permanence $permanence): self
    {
        if (!$this->permanences->contains($permanence)) {
            $this->permanences[] = $permanence;
            $permanence->addParticipant($this);
        }

        return $this;
    }

    public function removePermanence(Permanence $permanence): self
    {
        if ($this->permanences->contains($permanence)) {
            $this->permanences->removeElement($permanence);
            $permanence->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setSubscriber($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            // set the owning side to null (unless already changed)
            if ($subscription->getSubscriber() === $this) {
                $subscription->setSubscriber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Donation[]
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): self
    {
        if (!$this->donations->contains($donation)) {
            $this->donations[] = $donation;
            $donation->setDonor($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): self
    {
        if ($this->donations->contains($donation)) {
            $this->donations->removeElement($donation);
            // set the owning side to null (unless already changed)
            if ($donation->getDonor() === $this) {
                $donation->setDonor(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of email2
     */
    public function getEmail2(): ?string
    {
        return $this->email2;
    }

    /**
     * Set the value of email2
     *
     * @return  string|null
     */
    public function setEmail2(?string $email2)
    {
        $this->email2 = $email2;

        return $this;
    }
}
