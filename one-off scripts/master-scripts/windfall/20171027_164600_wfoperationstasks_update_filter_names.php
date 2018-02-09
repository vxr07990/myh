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

//OT5307 - Modify Local Operations Task

$db = PearDatabase::getInstance();
$moduleName = 'WFOperationsTasks';

$viewNames = ['Open Local Operation Tasks' => 'Open Warehouse Tasks',
              'Closed Local Operation Tasks' => 'Closed Warehouse Tasks'];

foreach($viewNames as $oldView => $newView){
    $checkSQL = "SELECT * FROM `vtiger_customview` WHERE viewname =? AND entitytype = ?";
    $result   = $db->pquery($checkSQL, [$oldView, $moduleName]);
    while($row = $db->fetch_row($result)){
        $replaceSQL = "UPDATE `vtiger_customview` SET viewname =? WHERE cvid = ?";
        $db->pquery($replaceSQL, [$newView, $row['cvid']]);
    }
}

