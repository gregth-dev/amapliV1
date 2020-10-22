<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @UniqueEntity(fields = {"name"},message ="Le nom du document existe déjà")
 */
class Document
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=5, minMessage="Le nom du document doit faire au minimum 5 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="document", orphanRemoval=true,cascade={"persist"})
     *
     */
    private $files;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

    /**
     * icon fontawesome
     *
     * @var string
     */
    private $icon;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setDocument($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getDocument() === $this) {
                $file->setDocument(null);
            }
        }

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get the value of icon
     */
    public function getIcon()
    {
        switch ($this->getType()) {
            case 'pdf':
                $this->icon = '-pdf';
                break;
            case 'jpeg':
                $this->icon = '-image';
                break;
            case 'png':
                $this->icon = '-image';
                break;
            case 'docx':
                $this->icon = '-word';
                break;
            case 'xlsx':
                $this->icon = '-excel';
                break;
            default:
                $this->icon = '';
                break;
        }
        return $this->icon;
    }
}
