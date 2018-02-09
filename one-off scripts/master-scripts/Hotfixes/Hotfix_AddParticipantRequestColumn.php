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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();
$result = $db->pquery('DESCRIBE `vtiger_participatingagents`');
$requestColumnExists = false;
echo "<br> checking to see if oa survey request id column exists in vtiger_participating_agents <br>";
while ($row =& $result->fetchRow()) {
    if ($row['Field'] == 'oasurveyrequest_id') {
        $requestColumnExists = true;
    }
}
if (!$requestColumnExists) {
    echo "<br> No, that column is missing <br> Adding it now...";
    Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_participatingagents` ADD oasurveyrequest_id int(11)");
    echo "DONE!<br>";
} else {
    echo "<br>Yeah that column already exists, no action taken";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";