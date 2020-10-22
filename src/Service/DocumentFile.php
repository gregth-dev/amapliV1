<?php

namespace App\Service;

use DateTime;
use DateTimeZone;
use App\Entity\File;
use App\Entity\Document;
use App\Entity\Emargement;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Associe un document et un fichier du type file
 */
class DocumentFile
{
    private $fileUploader;
    private $entityManager;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $entityManager)
    {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }

    public function deleteFile(Document $document)
    {
        $fileNames = $document->getFiles()->toArray();
        foreach ($fileNames as $fileName) {
            $this->fileUploader->delete($fileName->getName());
            $document->removeFile($fileName);
        }
    }

    public function updateFile(Document $document, $file)
    {
        $document->setUpdateDate(new DateTime('now', new DateTimeZone('Europe/Paris')));
        $this->deleteFile($document);
        if (!$this->newFile($document, $file))
            return false;
        return true;
    }

    public function newFile(Document $document, UploadedFile $file)
    {
        if (!$fileName = $this->fileUploader->upload($file))
            return false;
        $newFile = new File();
        $newFile->setName($fileName);
        $document->setType($this->fileUploader->getExtension());
        $document->addFile($newFile);
        return true;
    }

    public function getDocumentFromObject(Object $object, $output)
    {
        $documentName = str_replace('/', '-', $object->getDocName());
        $fileName = uniqid() . '-' . $documentName . '.pdf';
        file_put_contents($this->fileUploader->getTargetDirectory() . '/' . $fileName, $output);
        $document = new Document();
        $file = new File();
        $file->setName($fileName);
        $document->setName($documentName);
        $document->setType('pdf');
        $document->addFile($file);
        $this->entityManager->persist($file);
        $this->entityManager->persist($document);
        $this->entityManager->flush();
    }
}
