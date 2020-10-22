<?php

namespace App\Notification;

use App\Entity\Document;
use App\Entity\EmailSimple;
use App\Entity\File;
use App\Entity\User;
use App\Service\FileUploader;
use App\Service\DocumentFile;
use DateTime;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class CreateEmail
{

    private $mailer;
    private $documentFile;
    private $manager;
    private $fileUploader;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer, DocumentFile $documentFile, FileUploader $fileUploader)
    {
        $this->mailer = $mailer;
        $this->documentFile = $documentFile;
        $this->manager = $manager;
        $this->fileUploader = $fileUploader;
    }

    public function sendEmail(EmailSimple $emailSimple)
    {
        $users = $emailSimple->getRecipients();
        if ($choices = $emailSimple->getRecipientsGroup()) {
            $ur = $this->manager->getRepository(User::class);
            foreach ($choices as $choice) {
                switch ($choice) {
                    case 'all':
                        $usersGroup = $ur->findAll();
                        break;
                    case 'adherents':
                        $usersGroup = $ur->findBy(['memberType' => 'Adhérent']);
                        break;
                    case 'referents':
                        $usersGroup = $ur->findBy(['memberType' => 'Référent']);
                        break;
                    case 'producteurs':
                        $usersGroup = $ur->findBy(['memberType' => 'Producteur']);
                        break;
                    case 'tresoriers':
                        $usersGroup = $ur->findBy(['memberType' => 'Trésorier']);
                        break;
                    case 'admin':
                        $usersGroup = $ur->findBy(['memberType' => 'Administrateur']);
                        break;
                }
                foreach ($usersGroup as $userGroup) {
                    $users[] = $userGroup;
                }
            }
        }
        if ($contracts = $emailSimple->getRecipientsContracts()) {
            foreach ($contracts as $contract) {
                $contractAdherents = $contract->getContractMembers()->toArray();
                foreach ($contractAdherents as $contractAdherent) {
                    $users[] = $contractAdherent->getSubscriber();
                }
            }
        }
        $message = $emailSimple->getMessage();
        $template = $emailSimple->getTemplate()[0] ?? 'email/template/simple.html.twig';
        //on traite l'ajout de fichier depuis la base de données
        $path = $this->fileUploader->getTargetDirectory();
        $documentNames = [];
        if ($documentBase = $emailSimple->getDocumentBase()) {
            foreach ($documentBase as $document)
                $documentNames[] = $document->getFiles()[0]->getName();
        }
        //on traite les fichiers depuis le champ input
        $fileUpload = null;
        if ($emailSimple->getFile()) {
            if ($fileUpload = $this->fileUploader->upload($emailSimple->getFile())) {
                $saveDoc = $emailSimple->getSaveDoc();
                $documentNames[] = $fileUpload;
                if ($saveDoc) {
                    $document = new Document();
                    $document->setName($emailSimple->getDocName());
                    $newFile = new File();
                    $newFile->setName($fileUpload);
                    $document->setType($this->fileUploader->getExtension());
                    $document->addFile($newFile);
                    $this->manager->persist($document);
                    $this->manager->flush();
                }
            }
        }
        $sender = $emailSimple->getSender()->getEmail();
        $emails = [];
        if ($emailSimple->getCopyMail())
            $emails[] = $sender;
        foreach ($users as $user) {
            $emails[] = $user->getEmail();
            if ($email2 = $user->getEmail2())
                $emails[] = $email2;
        }
        $emails = array_unique($emails);
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <$sender>"))
            ->bcc(...$emails)
            ->subject("Contact depuis EscaleAmap")
            ->htmlTemplate($template)
            ->context([
                'content' => $message
            ]);
        if ($documentNames) {
            foreach ($documentNames as $documentName) {
                $email->attachFromPath($path . $documentName);
            }
        }
        $this->mailer->send($email);
        //si il y a un fichier uploadé et qu'il ne doit pas être sauvegardé on le supprime
        if ($fileUpload && !$saveDoc)
            $this->fileUploader->delete($fileUpload);
    }

    public function sendEmailLivraisons(User $user, string $documentName, DateTime $date, string $emailReferent)
    {
        $template = 'email/template/simple.html.twig';
        $path = $this->fileUploader->getTargetDirectory();
        $date = $date->format('d-m-Y');
        $message = "En pièce jointe la liste des produits pour la prochaine livraison du $date";
        $emails = [$user->getEmail()];
        if ($email2 = $user->getEmail2())
            $emails[] = $email2;
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <$emailReferent>"))
            ->bcc(...$emails)
            ->subject("Liste de livraisons")
            ->htmlTemplate($template)
            ->context([
                'content' => $message,
            ]);
        $email->attachFromPath($path . $documentName);
        $this->mailer->send($email);
        $this->fileUploader->delete($documentName);
    }

    public function sendEmailPermanence(User $participant, DateTime $date, string $firstName)
    {
        $template = 'email/template/simple.html.twig';
        $path = $this->fileUploader->getTargetDirectory();
        $date = $date->format('d-m-Y');
        $emails = [$participant->getEmail()];
        if ($email2 = $participant->getEmail2())
            $emails[] = $email2;
        $message = "Bonjour $firstName,<br> vous recevez ce mail car vous êtes inscrit à la prochaine permanence du $date.<br> Merci pour votre partipation ! <br> Ceci est un mail automatique merci de ne pas y répondre.";
        $email = (new TemplatedEmail())
            ->from(Address::fromString("EscaleAmap <contact@escalamap.org>"))
            ->to(...$emails)
            ->subject("Participation à la prochaine permanence")
            ->htmlTemplate($template)
            ->context([
                'content' => $message,
            ]);
        $this->mailer->send($email);
    }
}
