<?php

namespace App\Controller;

use mysqli;
use App\Service\Cron\Database\MySQLDump;
use App\Service\FileUploader;
use DateTime;
use DateTimeZone;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BackupDBController extends AbstractController
{

    /**
     * @Route("/backup",name="backup")
     */
    public function backup(FileUploader $fileUploader)
    {
        $db = new mysqli('localhost', 'root', '', 'escalamap');
        $db = new mysqli('db5000832228.hosting-data.io', 'dbu1109546', '1981bobY!es2Mx5E4', 'dbs735633');
        $dump = new MySQLDump($db);
        $time = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $file = 'export.sql.gz';
        $dump->save($fileUploader->getTargetBackupDirectory() . '/' . $time->format('d-m-Y') . '-' . $file);
    }
}
