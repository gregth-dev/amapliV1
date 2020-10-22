<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use stdClass;

class PDFMaker

{
    private $documentFile;
    private $fileUploader;

    public function __construct(DocumentFile $documentFile, FileUploader $fileUploader)
    {
        $this->documentFile = $documentFile;
        $this->fileUploader = $fileUploader;
    }

    public function newPDF($html, String $renderDocumentName, string $orientation = 'portrait', $attachment = false)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();
        $dompdf->stream($renderDocumentName, [
            "Attachment" => $attachment
        ]);
    }

    /**
     * Permet d'éditer et de sauvegarder un doc si besoin
     * l'objet doit possèder getDocName(),getSaveDoc(), getOrientation()
     * @param viewtwig $html
     * @param String $renderDocumentName
     * @param Object $object
     * @param boolean $attachment
     * @return void
     */
    public function newPDFAndSaveDoc($html, String $renderDocumentName, Object $object, $attachment = false)
    {
        $attachDocumentName = $object->getDocName();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $object->getOrientation());
        $dompdf->render();
        if ($attachDocumentName && $object->getSaveDoc()) {
            $output = $dompdf->output();
            try {
                $this->documentFile->getDocumentFromObject($object, $output);
            } catch (\Throwable $th) {
                return false;
            }
        }
        $dompdf->stream($renderDocumentName, [
            "Attachment" => $attachment
        ]);
        return true;
    }

    /**
     * Permet d'éditer et de sauvegarder un doc si besoin
     * l'objet doit possèder getDocName(),getSaveDoc(), getOrientation()
     * @param viewtwig $html
     * @param String $renderDocumentName
     * @param Object $object
     * @param boolean $attachment
     * @return void
     */
    public function newPDFForEmail($html, String $renderDocumentName, $attachment = false)
    {

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();
        $fileName = $renderDocumentName . '-' . uniqid() . '.pdf';
        file_put_contents($this->fileUploader->getTargetDirectory() . '/' . $fileName, $output);
        return $fileName;
    }
}
