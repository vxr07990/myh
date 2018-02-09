<?php
if (!checkIsWindfallActive()) {
    return;
}
if (function_exists("call_ms_function_ver")) {
    $version = 5;
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
$moduleName = 'WFSlotConfiguration';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    return;
}
$defaultsTable = 'vtiger_'.strtolower($moduleName).'_defaults';
$db = PearDatabase::getInstance();
$result = $db->pquery('SHOW TABLES LIKE ?', [$defaultsTable]);
if ($db->num_rows($result) > 0){
    Vtiger_Utils::ExecuteQuery("DROP TABLE `$defaultsTable`");
}
$createSQL = "CREATE TABLE `$defaultsTable` (
          `wfslotconfigurationid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `code` varchar(20),
          `description` varchar(255),
          `label1` varchar(20),
          `slotpercentage1` int(3) DEFAULT 0,
          `label2` varchar(20),
          `slotpercentage2` int(3) DEFAULT 0,
          `label3` varchar(20),
          `slotpercentage3` int(3) DEFAULT 0,
          `label4` varchar(20),
          `slotpercentage4` int(3) DEFAULT 0,
          `label5` varchar(20),
          `slotpercentage5` int(3) DEFAULT 0,
          `label6` varchar(20),
          `slotpercentage6` int(3) DEFAULT 0
        );";

$db->pquery($createSQL, array());

$defaultRows = [
    ['code' => '1', 'description' => 'FULL', 'label1' => '1', 'slotpercentage1' => 100],
    ['code' => 'LR', 'description' => 'Left-Right', 'label1' => 'L', 'slotpercentage1' => 50,
     'label2' => 'R', 'slotpercentage2' => 50],
    ['code' => 'LCR', 'description' => 'Left-Center-Right', 'label1' => 'L', 'slotpercentage1' => 33,
     'label2' => 'C', 'slotpercentage2' => 34, 'label3' => 'R', 'slotpercentage3' => 33],
    ['code' => 'ABCDEF', 'description' => 'A-B-C-D-E-F', 'label1' => 'A', 'slotpercentage1' => '16',
     'label2' => 'B', 'slotpercentage2' => '17', 'label3' => 'C', 'slotpercentage3' => '17',
     'label4' => 'D', 'slotpercentage4' => '17', 'label5' => 'E', 'slotpercentage5' => '17',
     'label6' => 'F', 'slotpercentage6' => '16']

];

AddRowsWFSCACDR($defaultsTable, $defaultRows);


function AddRowsWFSCACDR($tableName, $rowData){
    $db = PearDatabase::getInstance();
    foreach($rowData as $row){
        $sql = 'INSERT INTO `'.$tableName.'` ('.implode(',',array_keys($row)).') VALUES('.generateQuestionMarks(implode(',',array_values($row))).')';
        $db->pquery($sql, array_values($row));
    }
}
