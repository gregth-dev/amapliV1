<?php

declare(strict_types=1);

namespace App\Service;

use Exception;

/**
 * Exceptions en lien avec FileUploader. Classe 100% statique.
 * 
 */
class FileUploadException extends Exception
{
    /**
     * La lecture du fichier image a échoué (utilisé par les enfants).
     */
    public const SAVE_FAILED = "L'enregistrement du fichier a échoué.";
    /**
     * La lecture du fichier image a échoué (utilisé par les enfants).
     */
    public const EXTENSION_FAILED = "Format du fichier non accepté";
}
