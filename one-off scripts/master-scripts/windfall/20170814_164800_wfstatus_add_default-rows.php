<?php
if (!checkIsWindfallActive()) {
    return;
}
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$moduleName = 'WFStatus';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    return;
}
$defaultsTable = 'vtiger_'.strtolower($moduleName).'_defaults';
$db = PearDatabase::getInstance();
$result = $db->pquery('SHOW TABLES LIKE ?', [$defaultsTable]);
if ($db->num_rows($result) > 0){
    Vtiger_Utils::ExecuteQuery("DROP TABLE $defaultsTable");
}
$createSQL = "CREATE TABLE `$defaultsTable` (
          `wfstatusid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `wfstatus_code` varchar(100),
          `wfstatus_description` TEXT
        );";

$db->pquery($createSQL, []);

$defaultRows = [
    ['wfstatus_code' => 'PPU', 'wfstatus_description' => 'Pending Pick Up'],
    ['wfstatus_code' => 'PD', 'wfstatus_description' => 'Pending Delivery'],
    ['wfstatus_code' => 'R', 'wfstatus_description' => 'Reserved'],
    ['wfstatus_code' => 'IU', 'wfstatus_description' => 'In Use'],
    ['wfstatus_code' => 'IS', 'wfstatus_description' => 'In Storage'],
    ['wfstatus_code' => 'OP', 'wfstatus_description' => 'Off Property'],
];




$db = PearDatabase::getInstance();
foreach($defaultRows as $row){
    $fieldNames = [];
    $fieldVals = [];
    foreach ($row as $fieldName=>$fieldVal){
        $fieldNames[] = $fieldName;
        $fieldVals[] = $fieldVal;
    }
    $sql = 'INSERT INTO `'.$defaultsTable.'` ('.implode(',', $fieldNames).') VALUES('.generateQuestionMarks($fieldVals).')';
    $db->pquery($sql, [$fieldVals]);
}

