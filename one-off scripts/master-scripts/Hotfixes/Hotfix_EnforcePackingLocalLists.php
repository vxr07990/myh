<?php
require_once 'includes/main/WebUI.php';
require_once('include/Webservices/Retrieve.php');

if (!getenv('ENFORCE_STANDARD_LOCAL_PACKING')) {
    print "\e[33mNOT RUNNING BY CONFIG: " . __FILE__ . "<br />\n\e[0m";
    return;
}

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = &PearDatabase::getInstance();
$sql = 'SELECT tariffservicesid,rate_type FROM vtiger_tariffservices
        JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_tariffservices.tariffservicesid
        WHERE vtiger_crmentity.deleted != 1 AND
        (
        vtiger_tariffservices.rate_type = "Packing Items"
        )';

$result = $db->query($sql);
if($db->num_rows($result) == 0) {
    echo "No tariff services exist to FIX.<br/>\n";
}else {
    if (method_exists($result, 'fetchRow')) {
        $user         = new Users();
        $admin_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        while ($row = $result->fetchRow()) {
            $tariffservicesid = $row['tariffservicesid'];
            try {
                //print "Trying to save: " . $tariffservicesid . "\n";
                //$recordModel      = Vtiger_Record_Model::getInstanceById($tariffservicesid, 'TariffServices');
                //$recordModel->set('mode', 'edit');
                //$recordModel->save();
                $wsId = vtws_getWebserviceEntityId(('TariffServices'), $tariffservicesid);
                $serviceRecordArray = vtws_retrieve($wsId, $admin_user);
                //$serviceRecordArray = vtws_retrieve('44x'.$tariffservicesid, $admin_user);
                if ($serviceRecordArray) {
//                    list ($ws_id, $userId) = explode('x', $serviceRecordArray['assigned_user_id']);
//                    if ($ws_id == 19 && $userId) {
//                        $updateUser = $user->retrieveCurrentUserInfoFromFile($userId);
//                    }
                    $updateResult = vtws_update($serviceRecordArray, $admin_user);
                    //print "HERE: Update/Saved: " . $tariffservicesid . "\n";
                } else {
                    //print "HERE: unable to pull: " . $tariffservicesid . "\n";
                }
            } catch (Exception $e) {
                //print "FAIL: " . $e->getMessage() . PHP_EOL;
                //print "FAIL: " . $e->getTraceAsString() . PHP_EOL;
            }
        }
    }
}


print "\e[36mFINISHED: " . __FILE__ . "<br />\n\e[0m";