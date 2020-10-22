<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EmailSimple
{

    /**
     * Liste de destinataires
     *
     * @var array
     */
    private $recipients = [];

    /**
     * Destinataires groupés
     *
     * @var array
     */
    private $recipientsGroup = [];

    /**
     * Destinataires par contrat
     *
     * @var array
     */
    private $recipientsContracts = [];

    /**
     * Template du mail
     *
     * @var array
     */
    private $template = [];

    /**
     * message texte
     * @Assert\NotBlank()
     * @Assert\Length(min=10,minMessage="Votre message doit faire 10 caractères minimum")
     * @var string
     */
    private $message;

    /**
     * Document de la BDD
     *
     * @var array
     */
    private $documentBase = [];

    /**
     * Nom du document
     *
     * @var string
     */
    private $docName;

    /**
     * Fichier joint
     *
     * @var UploadedFile
     */
    private $file;

    /**
     * Valide la sauvegarde du document
     *
     * @var bool
     */
    private $saveDoc;

    /**
     * Valide la sauvegarde du document
     *
     * @var User
     */
    private $sender;

    /**
     * Valide la sauvegarde du document
     *
     * @var bool
     */
    private $copyMail;

    /**
     * Get the value of destinataires
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * Set the value of destinataires
     *
     * @return  self
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Get the value of destinataireGroup
     */
    public function getRecipientsGroup(): array
    {
        return $this->recipientsGroup;
    }

    /**
     * Set the value of destinataireGroup
     *
     * @return  self
     */
    public function setRecipientsGroup(array $recipientsGroup)
    {
        $this->recipientsGroup = $recipientsGroup;

        return $this;
    }

    /**
     * Get the value of recipientsContracts
     */
    public function getRecipientsContracts(): array
    {
        return $this->recipientsContracts;
    }

    /**
     * Set the value of recipientsContracts
     *
     * @return  self
     */
    public function setRecipientsContracts(array $recipientsContracts)
    {
        $this->recipientsContracts = $recipientsContracts;

        return $this;
    }

    /**
     * Get the value of template
     */
    public function getTemplate(): array
    {
        return $this->template;
    }

    /**
     * Set the value of template
     *
     * @return  self
     */
    public function setTemplate(array $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of documentBase
     */
    public function getDocumentBase(): array
    {
        return $this->documentBase;
    }

    /**
     * Set the value of documentBase
     *
     * @return  self
     */
    public function setDocumentBase(array $documentBase)
    {
        $this->documentBase = $documentBase;

        return $this;
    }

    /**
     * Get the value of docName
     */
    public function getDocName(): ?string
    {
        return $this->docName;
    }

    /**
     * Set the value of docName
     *
     * @return  self
     */
    public function setDocName(?string $docName)
    {
        $this->docName = $docName;

        return $this;
    }

    /**
     * Get the value of file
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return UploadedFile
     */
    public function setFile(?UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of saveDoc
     */
    public function getSaveDoc(): bool
    {
        return $this->saveDoc;
    }

    /**
     * Set the value of saveDoc
     *
     * @return  self
     */
    public function setSaveDoc(bool $saveDoc)
    {
        $this->saveDoc = $saveDoc;

        return $this;
    }

    /**
     * Get valide la sauvegarde du document
     *
     * @return  User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set valide la sauvegarde du document
     *
     * @param  User  $sender  Valide la sauvegarde du document
     *
     * @return  self
     */
    public function setSender(User $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get valide la sauvegarde du document
     *
     * @return  bool
     */
    public function getCopyMail()
    {
        return $this->copyMail;
    }

    /**
     * Set valide la sauvegarde du document
     *
     * @param  bool  $copyMail  Valide la sauvegarde du document
     *
     * @return  self
     */
    public function setCopyMail(bool $copyMail)
    {
        $this->copyMail = $copyMail;

        return $this;
    }
}
