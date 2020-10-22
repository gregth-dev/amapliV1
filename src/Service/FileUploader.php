<?php

namespace App\Service;

use App\Service\FileUploadException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    /**
     * Répertoire pour les backups de la BDD
     *
     * @var string
     */
    private $targetBackupDirectory;
    private $slugger;
    private $extension;
    const AUTORIZE_EXTENSIONS = ['docx', 'doc', 'xls', 'xlsx', 'pdf', 'odt', 'ods', 'jpg', 'png', 'txt', 'zip', 'rar', 'sql'];

    public function __construct($targetDirectory, SluggerInterface $slugger, $targetBackupDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->targetBackupDirectory = $targetBackupDirectory;
        $this->slugger = $slugger;
        if (!file_exists($this->getTargetDirectory()))
            mkdir($this->getTargetDirectory(), 0777, true);
    }

    /**
     * Enregistre le fichier
     *
     * @param UploadedFile $file
     * @return string|false
     */
    public function upload(UploadedFile $file)
    {
        $this->setExtension($file->guessExtension());
        if (in_array($this->getExtension(), self::AUTORIZE_EXTENSIONS)) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getTargetDirectory(), $fileName);
            } catch (FileException $e) {
                throw new FileUploadException(FileUploadException::SAVE_FAILED);
            }
            return $fileName;
        }
        throw new FileUploadException(FileUploadException::EXTENSION_FAILED);
        return false;
    }

    /**
     * Supprime le fichier du répertoire
     *
     * @param string $fileName Nom du fichier
     * @return void
     */
    public function delete(string $fileName)
    {
        // On supprime le fichier du répertoire
        unlink($this->getTargetDirectory() . $fileName);
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getTargetBackupDirectory()
    {
        return $this->targetBackupDirectory;
    }

    /**
     * Get the value of extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the value of extension
     *
     * @return  self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }
}
