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

echo "<br/>\nStarting the update of the participant picklist";
try {
    $db = PearDatabase::getInstance();
    $picklistValues = [
                        'Booking Agent',
                        'Destination Agent',
                        'Origin Agent',
                        'Estimating Agent',
                        'Hauling Agent'
    ];

    $picklistTable = $db->pquery('SELECT * FROM `vtiger_agent_type`')?true:false;
    if ($picklistTable) {
        echo "<br>Blowing out old picklist values now...";
        $db->pquery('TRUNCATE TABLE `vtiger_agent_type`', []);
        echo "<br>blowout complete, inserting new picklist values...";
        for ($i = 0; $i < count($picklistValues); $i++) {
            $db->pquery('INSERT INTO `vtiger_agent_type` (agent_typeid, agent_type, sortorderid, presence) 
                        VALUES (?,?,?,1)', [$i + 1, $picklistValues[$i], $i + 1]);
            $db->pquery('UPDATE `vtiger_agent_type_seq` SET id = ?', [$i+1]);
        }
        echo "done!<br> Sirva agent types hotfix complete.";

        echo "<br/> We are now removing the old incorrect rows!";
        $db->pquery('DELETE FROM `vtiger_participatingagents` 
                        WHERE agent_type LIKE ? 
                        OR agent_type LIKE ? 
                        OR agent_type LIKE ?                         
                        OR agent_type LIKE ?',
                        ['Destination Storage Agent', 'Invoicing Agent', 'Origin Storage Agent']);
        echo "<br/> Old rows have been removed!";
    } else {
        echo "<br/>\nThe picklist table does not exist. ";
    }
} catch (Exception $e) {
    echo "<br/>\nFailed to update the particpant picklist ".$e->getMessage();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";