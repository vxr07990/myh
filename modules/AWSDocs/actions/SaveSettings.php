<?php

/**
 * vTiger backup Module
 *
 *
 * @package        VGSBackup Module
 * @author         Conrado Maggi
 * @license        Comercial / VPL
 * @copyright      2014 VGS Global
 * @version        Release: 1.0
 */
include_once 'modules/AWSDocs/lib/S3.php';

class AWSDocs_SaveSettings_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        global $current_user;
        $moduleName = $request->getModule();

        if (!is_admin($current_user)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $this->validateInput($request);
        $this->saveRecord($request);

        header("Location: index.php?module=AWSDocs&view=SettingsDetail&parent=Settings");
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        $infoArray = array(
            
            $request->get('aws_key'),
            $request->get('aws_secret'),
            $request->get('aws_bucket'),
        );

        $db = PearDatabase::getInstance();

        $db->pquery("DELETE FROM vtiger_awsdocsettings", array());

        $db->pquery("INSERT INTO vtiger_awsdocsettings(aws_key,aws_secret,aws_bucket) 
     VALUES(?,?,?)", $infoArray);
    }

    public function validateInput($request)
    {
        $s3 = new S3($request->get('aws_key'), $request->get('aws_secret'));
        $buckets = $s3->listBuckets();

        if (!$buckets) {
            header("Location: index.php?module=AWSDocs&view=SettingsEdit&parent=Settings&msg=credentials-error");
            exit;
        } elseif (!in_array($request->get('aws_bucket'), $buckets)) {
            header("Location: index.php?module=AWSDocs&view=SettingsEdit&parent=Settings&msg=bucket-error");
            exit;
        }
    }
}
