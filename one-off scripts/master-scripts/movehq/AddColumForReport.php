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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.COLUMNS WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_report" AND column_name = "agentid" LIMIT 1';
$res = $db->pquery($stmt,array());
if ($db->num_rows($res) > 0) {
    echo 'vtiger_report existing column agentid <br>';
} else {
    $stmt = 'ALTER TABLE `vtiger_report` ADD COLUMN `agentid` INT(11) DEFAULT 0';
    $db->pquery($stmt);
    echo "add colum agentid by vtiger_report success <br>";

}
$stmt1 = 'SELECT * FROM information_schema.COLUMNS WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_reportfolder" AND column_name = "agentid" LIMIT 1';
$res1 = $db->pquery($stmt1);
if ($db->num_rows($res1) > 0) {
    echo 'vtiger_reportfolder existing column agentid';
} else {
    $stmt1 = 'ALTER TABLE `vtiger_reportfolder` ADD COLUMN `agentid` INT(11) DEFAULT 0';
    $db->pquery($stmt1);
    echo "add colum agentid by vtiger_reportfolder success";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";