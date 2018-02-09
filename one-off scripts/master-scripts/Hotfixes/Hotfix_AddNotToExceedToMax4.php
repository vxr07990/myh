<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "Adding Report Section for Do Not Exceed to Max 4 if it does not exist...<br/>\n";
$sql = "SELECT tariffsid FROM vtiger_tariffs
        JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_tariffs.tariffsid
        WHERE vtiger_tariffs.tariff_type = 'Max 4' AND vtiger_crmentity.deleted != 1";
$result = $db->query($sql);
if($db->num_rows($result) == 0) {
    echo "No Max 4 tariff exists.<br/>\n";
}else {
    $id = $result->fetchRow()[0];
    $sql = "SELECT tariffreportsectionsid FROM vtiger_tariffreportsections WHERE tariffs_orders_tariff = ?";
    $result = $adb->pquery($sql, [$id]);
    if($adb->num_rows($result) == 0) {
        $recordModel = Vtiger_Record_Model::getCleanInstance('TariffReportSections');
        $recordModel->setID(null);
        $recordModel->set('tariff_orders_tariff',$id);
        $recordModel->set('tariff_orders_type','Do Not Exceed');
        $recordModel->set('tariff_orders_title','Do Not Exceed');
        $recordModel->set('tariff_orders_description','Do Not Exceed');
        $recordModel->set('tariff_orders_body','Do Not Exceed');
        $recordModel->set('assigned_user_id', Users::getActiveAdminId());
        $recordModel->set('record_id',null);
        try {
            $recordModel->save();
            echo "Report Section added to Max 4 tariff.<br/>\n";
        }catch(Exception $e) {
            echo "Failed to add Report Section to Max 4 tariff $id.<br/>\n";
        }
    }else{
        echo "Report Section already exists for Max 4.<br/>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";