<?php

use MoveCrm\RepublicWorkflowFTP;
//include_once ('libraries/MoveCrm/LogUtils.php');

function republicFTPLeads($entityData) {

    $config = [];
    $config['host']              = getenv('REPUBLIC_API_FTP_HOST');
    $config['username']          = getenv('REPUBLIC_API_FTP_USER');
    $config['password']          = getenv('REPUBLIC_API_FTP_PASS');
    $config['port']              = getenv('REPUBLIC_API_FTP_PORT');
    $config['remoteFilePath']    = getenv('REPUBLIC_API_FTP_REMOTE_DIRECTORY');
    $config['remoteFilePrepend'] = getenv('REPUBLIC_API_FTP_REMOTE_FILE_PREPEND');
    $config['maximumTries']      = getenv('REPUBLIC_API_FTP_TRIES');
    $config['DEBUG_LOG']         = 'REPUBLIC_API_FTP_LOG';
    $config['retrySleepMin']     = 5;
    $config['retrySleepMax']     = 20;

    MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Configuration: " . print_r($config, true));

    for ($count = 0; $count < $config['maximumTries']; $count++) {
        try {
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], 'Start try: '.$count);
            $ftp = new RepublicWorkflowFTP($config);

            $ftp->setRemoteFilePath($config['remoteFilePath']);
            $ftp->setFilePrepend ($config['remoteFilePrepend']);

            $phone = preg_replace('/[^0-9]/','',$entityData->get('phone'));
            $inputString  = $entityData->get('firstname').' '.$entityData->get('lastname').','.$phone."\r\n";
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Input String to write in file: " . print_r($inputString, true));
            $remotePath   = $ftp->getRemoteFilePath($entityData);
            $remoteFile   = $ftp->generateRemoteFilename($entityData);
            $tempFilename = $ftp->getLocalFileToSend($inputString);

            $sendInfo     = [
                'remoteFile' => $remotePath.$remoteFile,
                'localFile'  => $tempFilename
            ];

            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Sending: " . print_r($sendInfo, true));
            $sendResult = $ftp->sendToRemote($sendInfo);
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Send Result: " . print_r($sendResult, true));

            return $sendResult;
        } catch (Exception $ex) {
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Exception Failure: " . $ex->getMessage());
            $randSec = mt_rand($config['retrySleepMin'], $config['retrySleepMax']);
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Sleeping: " . $randSec);
            sleep($randSec);
        } finally {
            MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Reached Finally.");
            if ($ftp) {
                MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Removing local file.");
                $ftp->removeLocalFile();
            }
        }
    }

    MoveCrm\LogUtils::LogToFile($config['DEBUG_LOG'], "Returning from republicFTPLeads.");
    return false;
}

