<?php

namespace Core\Libraries;

use DateTime;
use DateTimeZone;

class File
{

    protected $filetype = array();
    protected $maxsize;
    protected $urlfile = "";
    protected $destination = false;

    public $errormsg = "";

    public function __construct($destination, array $filetype = array(), $maxsize = 0)
    {
        if (!$this->destination)
            $this->destination = $destination;

        $this->filetype = $filetype;
        $this->maxsize = $maxsize;
    }

    public function upload(array $files, $usePrefixName = true)
    {

        if ($this->maxsize != 0 && $files['size'] > $this->maxsize) {

            $this->errormsg = clang('File.size_too_large');
            return false;
        }

        if (!empty($filetype))
            if (!in_array($this->getExtension($files), $this->filetype)) {

                $this->errormsg = clang('File.extension_not_allowed');
                return false;
            }

        if (!file_exists(ROOT . DS . $this->destination))
            mkdir(ROOT . DS . $this->destination, 0777, true);

        $nameex = "";
        if ($usePrefixName) {
            $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $nameex = $date->format("Ymd_His");
        }

        if (move_uploaded_file($files['tmp_name'], ROOT . DS . $this->destination . DS . $nameex . $files['name'])) {

            $this->urlfile = $this->destination . "/" . $nameex . $files['name'];
            return true;
        }

        return false;
    }

    public function getFileUrl()
    {
        return $this->urlfile;
    }

    public function getErrorMessage()
    {
        return $this->errormsg;
    }

    public function getExtension(array $files)
    {
        return pathinfo($files['name'], PATHINFO_EXTENSION);
    }

    public function setExtension($fileType)
    {
        if (is_array($fileType)) {
            $this->filetype = $fileType;
            return $this;
        }

        $this->filetype = [];
        array_push($this->filetype, $fileType);
        return $this;
    }

    public function addExtension($fileType)
    {
        array_push($this->filetype, $fileType);
        return $this;
    }
}
