<?php

include_once 'modules/AWSDocs/lib/S3.php';

class AWSDocs_Record_Model extends Vtiger_Record_Model
{
    public function uploadAWSSaveFile($record_id, $filename, $filetmpName)
    {
        global $log, $current_user, $upload_badext;
        
        $db = PearDatabase::getInstance();
        
        $log->debug("Entering into uploadAWSSaveFile($filename,$filetype,$filesize,$filetmpName) method.");

        //delete files associated with the record
        $this->deleteOldFile($record_id);

        //get the file path inwhich folder we want to upload the file
        $upload_file_path = $this->decideAWSFilePath();

        //upload the file in server
        $upload_status = $this->uploadFileAWS($filetmpName, $upload_file_path, $filename);


        if ($upload_status == 'true') {
            $sql2 = "INSERT INTO vtiger_awsdocsattach(awsdoc_id, filename, bucketname) values(?, ?, ?)";
            $params2 = array($record_id, $filename, $upload_file_path);
            $result = $db->pquery($sql2, $params2);
            
            $realFileName = trim(str_replace($record_id . '_', '', $filename));
            
            $db->pquery("UPDATE vtiger_awsdocs SET awsdocs_filename = ? WHERE awsdocsid =?", array($realFileName, $record_id));
            
            
            
            return true;
        } else {
            $log->debug("Skip the save attachment process.");
            return false;
        }
    }

    public function deleteOldFile($record_id)
    {
        $db = PearDatabase::getInstance();

        $result = $db->pquery("SELECT * FROM vtiger_awsdocsattach WHERE awsdoc_id=?", array($record_id));
        if ($db->num_rows($result) > 0) {
            $s3 = $this->getAWSInstance();
            $s3->deleteObject($db->query_result($result, 0, 'bucketname'), $db->query_result($result, 0, 'filename'));

            $delquery = 'delete from vtiger_awsdocsattach where awsdoc_id = ?';
            $delparams = array($record_id);
            $db->pquery($delquery, $delparams);
        }
    }

    public function uploadFileAWS($filetmpName, $bucketName, $uploadName)
    {
        $s3 = $this->getAWSInstance();

        return $s3->putObject($s3->inputFile($filetmpName, false), $bucketName, $uploadName, $s3->ACL_PUBLIC_READ);
    }

    /**
     * return an initiate instance of the S3 class
     */
    public function getAWSInstance()
    {
        $AWSCredentials = $this->getAWSCredentials();

        $s3 = new S3($AWSCredentials['awsAccessKey'], $AWSCredentials['awsSecretKey']);

        return $s3;
    }

    /**
     * Returns the credentials for the AWS Services tied to this account.
     */
    /*
    function getAWSCredentials() {
        $AWSCredentials = array(
            'awsAccessKey' => 'AKIAIPW256ZFQB6P3ZOA',
            'awsSecretKey' => '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'
        );

        return $AWSCredentials;
    }*/

     public function getAWSCredentials()
     {
         $db = pearDatabase::getInstance();
         $result = $db->pquery('SELECT * FROM vtiger_awsdocsettings');
        
         if ($db->num_rows($result) > 0) {
             $AWSCredentials = array(
                'awsAccessKey' => $db->query_result($result, 0, 'aws_key'),
                'awsSecretKey' => $db->query_result($result, 0, 'aws_secret')
                );
         }
        
         return $AWSCredentials;
     }

    /**
     * Function that returns the bucket name where we need to upload the file
     */
    public function decideAWSFilePath()
    {
        return 'vgsawsdocs';
    }
    
    public function downloadFile($fileName, $bucket, $resource)
    {
        $s3 = $this->getAWSInstance();
        return $s3->getObject($bucket, $fileName, $resource);
    }
}
