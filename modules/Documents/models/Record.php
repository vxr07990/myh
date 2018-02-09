<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Record_Model extends Vtiger_Record_Model
{

    /**
     * Function to get the Display Name for the record
     * @return <String> - Entity Display Name for the record
     */
    public function getDisplayName()
    {
        return Vtiger_Util_Helper::getLabel($this->getId());
    }

    public function getDownloadFileURL()
    {
        if ($this->get('filelocationtype') == 'I') {
            $fileDetails = $this->getFileDetails();
            return 'index.php?module='. $this->getModuleName() .'&action=DownloadFile&record='. $this->getId() .'&fileid='. $fileDetails['attachmentsid'];
        } else {
            return $this->get('filename');
        }
    }

    public function checkFileIntegrityURL()
    {
        return "javascript:Documents_Detail_Js.checkFileIntegrity('index.php?module=".$this->getModuleName()."&action=CheckFileIntegrity&record=".$this->getId()."')";
    }

    public function checkFileIntegrity()
    {
        $recordId = $this->get('id');
        $downloadType = $this->get('filelocationtype');
        $returnValue = false;

        if ($downloadType == 'I') {
            $fileDetails = $this->getFileDetails();
            if (!empty($fileDetails)) {
                $filePath = $fileDetails['path'];

                $savedFile = $fileDetails['attachmentsid']."_".$this->get('filename');

                if (fopen($filePath.$savedFile, "r")) {
                    $returnValue = true;
                }
            }
        }
        return $returnValue;
    }

    public function getFileDetails()
    {
        $db = PearDatabase::getInstance();
        $fileDetails = array();

        $result = $db->pquery("SELECT * FROM vtiger_attachments
							INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
							WHERE crmid = ?", array($this->get('id')));

        if ($db->num_rows($result)) {
            $fileDetails = $db->query_result_rowdata($result);
        }
        return $fileDetails;
    }

    public function downloadFile()
    {
        list ($fileContent, $fileDetails) = $this->retrieveDocumentContents();
        if ($fileContent) {
            header("Content-type: ".$fileDetails['type']);
            header("Pragma: public");
            header("Cache-Control: private");
            header("Content-Disposition: attachment; filename=".html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset')));
            header("Content-Description: PHP Generated Data");
        }
        echo $fileContent;
    }

    public function retrieveDocumentContents()
    {
        $fileDetails = $this->getFileDetails();
        $fileContent = false;

        if (!empty($fileDetails)) {
            $filePath = $fileDetails['path'];
            $fileName = $fileDetails['name'];

            if ($this->get('filelocationtype') == 'I') {
                $fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));
                $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                if (is_readable($filePath.$savedFile)) {
                    $fileContent = file_get_contents($filePath.$savedFile);
                }
            }
        }
        return [$fileContent, $fileDetails];
    }

    public function updateFileStatus($status)
    {
        $db = PearDatabase::getInstance();

        $db->pquery("UPDATE vtiger_notes SET filestatus = ? WHERE notesid= ?", array($status, $this->get('id')));
    }

    public function updateDownloadCount()
    {
        $db = PearDatabase::getInstance();
        $notesId = $this->get('id');

        $result = $db->pquery("SELECT filedownloadcount FROM vtiger_notes WHERE notesid = ?", array($notesId));
        $downloadCount = $db->query_result($result, 0, 'filedownloadcount') + 1;

        $db->pquery("UPDATE vtiger_notes SET filedownloadcount = ? WHERE notesid = ?", array($downloadCount, $notesId));
    }

    public function getDownloadCountUpdateUrl()
    {
        return "index.php?module=Documents&action=UpdateDownloadCount&record=".$this->getId();
    }

    public function get($key)
    {
        $value = parent::get($key);
        if ($key === 'notecontent') {
            return decode_html($value);
        }
        return $value;
    }

    public function getRelatedRecords()
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_senotesrel` JOIN `vtiger_crmentity` ON `vtiger_senotesrel`.crmid=`vtiger_crmentity`.crmid WHERE notesid=?";
        $params[] = $this->get('id');

        $result = $db->pquery($sql, $params);

        $recordIds = array();
        while ($row = $result->fetchRow()) {
            $recordIds[] = array('id'=>$row['crmid'], 'module'=>$row['setype'], 'label'=>$row['label'],
            'link'=>'index.php?module='.$row['setype'].'&view=Detail&record='.$row['crmid'].'&mode=showDetailViewByMode&requestMode=summary&tab_label=Order%20Summary');
         }

        return $recordIds;
    }
}
