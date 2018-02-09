<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class AWSDocs_DownloadFile_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $browserSafeMimes = array(
            'image/jpeg', 'application/pdf', 'image/tiff', 'image/png', 'image/gif', 'text/html', 'text/plain'
        );

        $filePath = sys_get_temp_dir();

        if (substr($filePath, -1) != '/') {
            $filePath = $filePath . '/';
        }
        //echo $filePath; die;

        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM vtiger_awsdocsattach 
               INNER JOIN vtiger_awsdocs  ON vtiger_awsdocsattach.awsdoc_id = vtiger_awsdocs.awsdocsid
               WHERE filename !='' AND awsdoc_id=?", array($record));

        if ($db->num_rows($result) > 0) {
            $savedFile = $db->query_result($result, 0, 'filename');
            $bucketName = $db->query_result($result, 0, 'bucketname');
            $fileName = $db->query_result($result, 0, 'awsdocs_filename');


            $AWSDocRecordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);

            $downloadFile = $AWSDocRecordModel->downloadFile($savedFile, $bucketName, $filePath . $savedFile);

            $fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));

            $fileSize = filesize($filePath . $savedFile);
            $fileSize = $fileSize + ($fileSize % 1024);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMime = finfo_file($finfo, $filePath . $savedFile);

            if (fopen($filePath . $savedFile, "r")) {
                $fileContent = fread(fopen($filePath . $savedFile, "r"), $fileSize);


                if ($request->get('mode') == 'preview' && !in_array($fileMime, $browserSafeMimes)) {
                    $fileContent = 'Preview not supported. Click filename to download the file';
                } else {
                    header("Content-type: " . $fileMime);
                    header("Content-Length: " . filesize($filePath . $savedFile));
                    header("Pragma: public");
                    header("Cache-Control: private");
                    if ($request->get('mode') == 'preview') {
                        header("Content-Disposition: inline; filename=$fileName");
                    } else {
                        header("Content-Disposition: attachment; filename=$fileName");
                    }
                    header("Content-Description: PHP Generated Data");
                }

                echo $fileContent;
            }
        }
    }
}
