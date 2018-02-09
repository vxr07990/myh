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

$db = PearDatabase::getInstance();

$fieldUpdates = array(
    'LBL_TARIFFSERVICES_CUFT' => array('cuft_rate'),
    'LBL_TARIFFSERVICES_SIT_ITEM' => array(
        'cartage_cwt_rate',
        'first_day_rate',
        'additional_day_rate'
    )
);

$sql = "SELECT blockid, tabid FROM `vtiger_blocks` WHERE blocklabel=?";

foreach($fieldUpdates as $blocklabel => $fieldArray) {
    $result = $db->pquery($sql, [$blocklabel]);
    $blockid = $result->fields['blockid'];
    $tabid = $result->fields['tabid'];
    foreach($fieldArray as $field) {
        $sqlField = "UPDATE `vtiger_field` SET block=? WHERE tabid=? AND fieldname=?";
        $db->pquery($sqlField, [$blockid, $tabid, $field]);
    }
}

//Remove Per Cu Ft and SIT Item from rate_type list
$rateTypesToRemove = ['Per Cu Ft', 'SIT Item'];

foreach($rateTypesToRemove as $rateType) {
    $db->pquery("DELETE FROM `vtiger_rate_type` WHERE rate_type=?", [$rateType]);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";