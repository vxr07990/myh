<?php
if (!checkIsWindfallActive()) {
    return;
}
if (function_exists("call_ms_function_ver")) {
    $version = 3;
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
$moduleName = 'WFLocationTypes';
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
          `wflocationtypesdefaultid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `wflocationtypes_type` varchar(100),
          `wflocationtypes_prefix` varchar(100),
          `fixed` varchar(3),
          `base` varchar(3),
          `container` varchar(3)
        );";

$db->pquery($createSQL, array());

$defaultRows = [
    ['wflocationtypes_type' => 'Vault', 'wflocationtypes_prefix' => 'V', 'base' => 0, 'container' => 0],
    ['wflocationtypes_type' => 'Floor', 'wflocationtypes_prefix' => 'F', 'base' => 1, 'container' => 1],
    ['wflocationtypes_type' => 'Cage', 'wflocationtypes_prefix' => 'C', 'base' => 1, 'container' => 1],
    ['wflocationtypes_type' => 'Rack', 'wflocationtypes_prefix' => 'R', 'base' => 1, 'container' => 1],
    ['wflocationtypes_type' => 'Record Storage', 'wflocationtypes_prefix' => 'X', 'base' => 1, 'container' => 0],
    ['wflocationtypes_type' => 'Trailer ', 'wflocationtypes_prefix' => 'T', 'base' => 1, 'container' => 1],
    ['wflocationtypes_type' => 'Pallet', 'wflocationtypes_prefix' => 'P', 'base' => 0, 'container' => 0]
];

AddRowsWFLTADR($defaultsTable, $defaultRows);


function AddRowsWFLTADR($tableName, $rowData){
    $db = PearDatabase::getInstance();
    foreach($rowData as $row){
        $fieldNames = [];
        $fieldVals = [];
        foreach ($row as $fieldName=>$fieldVal){
            $fieldNames[] = $fieldName;
            $fieldVals[] = $fieldVal;
        }
        $sql = 'INSERT INTO `'.$tableName.'` ('.implode(',', $fieldNames).') VALUES('.generateQuestionMarks($fieldVals).')';
        $db->pquery($sql, [$fieldVals]);
    }
}
