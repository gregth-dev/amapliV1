<?php

namespace App\Service;

use App\Entity\HTFile;
use App\Service\FileUploadException;

class HtFileMaker
{
    private $username;
    private $plainPassword;
    private $path;
    private $htAccessPath;

    public function __construct(string $htAccessPath)
    {
        $this->htAccessPath = $htAccessPath;
    }

    public function load(HTFile $hTFile)
    {
        $this->setUsername($hTFile->getUsername());
        $this->setPlainPassword($hTFile->getPlainPassword());
        $this->setPath('uploads/files');
    }

    public function createHTFiles()
    {
        $this->createPath();
        $this->setHtpasswd();
        $this->setHtaccess();
    }

    public function createPath()
    {
        if (!file_exists($this->getPath()))
            mkdir($this->getPath(), 0777, true);
    }


    public function setHtpasswd()
    {
        $password = crypt($this->getPlainPassword(), base64_encode($this->getPlainPassword()));
        $output = $this->getUsername() . ":$password";
        file_put_contents($this->getPath() . '/.htpasswd', $output);
    }

    public function setHtaccess()
    {
        $username = $this->getUsername();
        $plainPassword = $this->getPlainPassword();
        $path = $this->getPath();
        $absolutePath = $this->htAccessPath . '.htpasswd';

        try {
            $this->setHtpasswd($username, $plainPassword, $path);
        } catch (\Throwable $th) {
            throw new FileUploadException(FileUploadException::SAVE_FAILED);
        }
        $output =
            "AuthType Basic
AuthName 'Saisissez vos identifiants'
AuthUserFile $absolutePath
require user $username";
        $fileName = '.htaccess';
        file_put_contents($path . '/' . $fileName, $output);
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
