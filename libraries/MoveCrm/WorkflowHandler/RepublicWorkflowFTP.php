<?php

namespace MoveCrm;

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

use MoveCrm\WorkflowHandler\WorkflowFTPBridge;

class RepublicWorkflowFTP extends WorkflowFTPBridge {
    private $remoteFilePath    = '/Sirva/';
    private $defaultTimeZone    = 'America/Los_Angeles';
    private $fileSpaceCharacter = '_';
    private $filePrepend        = 'Priority';
    private $fileAppend         = '.csv';

    public function setRemoteFilePath ($dir) {
        $this->remoteFilePath = $dir;
        return true;
    }

    public function getRemoteFilePath () {
        return $this->remoteFilePath;
    }

    public function setFileTimeZone ($tz) {
        if (!$tz) {
            //@TODO: add some better check
            return false;
        }
        $this->defaultTimeZone = $tz;
        return true;
    }

    public function getFileTimeZone () {
        return $this->defaultTimeZone;
    }

    public function setFilePrepend ($value) {
        $this->filePrepend = $value;
        return true;
    }

    public function getFilePrepend () {
        return $this->filePrepend;
    }

    public function generateRemoteFilename () {

        $filename   = $this->getFilePrepend().$this->fileSpaceCharacter;
        $dataObject = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));
        $filename   .= $dataObject->format('Y'.$this->fileSpaceCharacter.'m'.$this->fileSpaceCharacter.'d'.$this->fileSpaceCharacter.'His').$this->fileAppend;

        return $filename;
    }
}
