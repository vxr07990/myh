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

$moduleName = 'WFConditions';
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
    `wfconditionsdefaultid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `abbreviation` varchar(3),
    `description` text,
    `is_default` varchar(3)
);";


$db->pquery($createSQL, []);

$defaultRows = [
    ['abbreviation' => 'E', 'description' => 'Excellent', 'is_default' => 1],
    ['abbreviation' => 'G', 'description' => 'Good', 'is_default' => 1],
    ['abbreviation' => 'F', 'description' => 'Fair', 'is_default' => 1],
    ['abbreviation' => 'P', 'description' => 'Poor', 'is_default' => 1],
];


foreach($defaultRows as $row) {
    $fieldNames = [];
    $fieldVals  = [];
    foreach ($row as $fieldName => $fieldVal) {
        $fieldNames[] = $fieldName;
        $fieldVals[]  = $fieldVal;
    }
    $sql = 'INSERT INTO `'.$defaultsTable.'` ('.implode(',', $fieldNames).') VALUES('.generateQuestionMarks($fieldVals).')';
    $db->pquery($sql, [$fieldVals]);
}
