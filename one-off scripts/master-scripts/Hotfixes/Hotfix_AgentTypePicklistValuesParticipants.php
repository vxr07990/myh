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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$picklistValues = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'];
$db             = PearDatabase::getInstance();
echo "<br><br> Attempting to create picklist values table for participating agent types<br>";
$picklistTable = $db->pquery('SELECT * FROM `vtiger_agent_type`')?true:false;
if (!$picklistTable) {
    echo "<br>agent_type p-list values table doesn't exist. Creating now...<br>";
    //make the table
    $db->pquery('CREATE TABLE `vtiger_agent_type` 
                    (
                      agent_typeid INT(10),
                      agent_type VARCHAR(40),
                      sortorderid INT(10),
                      presence TINYINT
                    )
              ', []);
    //make a sequence table
    $db->pquery('CREATE TABLE `vtiger_agent_type_seq` 
                    (
                      id INT(10)
                    )
              ', []);
    //initialize sequence table to 0
    $db->pquery('INSERT INTO `vtiger_agent_type_seq` (id) VALUES (0)');
    //initialize default the values
    for ($i = 0; $i < count($picklistValues); $i++) {
        $db->pquery('INSERT INTO `vtiger_agent_type` (agent_typeid, agent_type, sortorderid, presence) VALUES (?,?,?,1)', [$i + 1, $picklistValues[$i], $i + 1]);
        $db->pquery('UPDATE `vtiger_agent_type_seq` SET id = ?', [$i+1]);
    }
    echo "done<br><br>";
} else {
    echo "<br>agent_type p-list values table already exists, no action taken<br><br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";