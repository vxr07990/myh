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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//this checks
$db = PearDatabase::getInstance();
$sql = "SHOW INDEXES FROM  `vtiger_participating_agents` WHERE Key_name =  'PRIMARY'";
$result = $db->pquery($sql, []);
if($result) {
    $row = $result->fetchRow();
    if ($row) {
        //table exists and has a primary key
        Vtiger_Utils::ExecuteQuery("ALTER TABLE vtiger_participating_agents MODIFY id INT(19) NOT NULL");
        Vtiger_Utils::ExecuteQuery("ALTER TABLE vtiger_participating_agents DROP PRIMARY KEY");
    } else {
        //doesn't have a primary key, we don't know if it exists
        if (!Vtiger_Utils::CheckTable('vtiger_participating_agents')) {
            echo "<li>vtiger_participating_agents being created</li><br>";
            Vtiger_Utils::CreateTable('vtiger_participating_agents',
                                      '(id INT(19),
                                    crmentity_id INT(10),
                                    inbox_id INT(10),
                                    agent_id INT(10),
                                    agent_type INT(15),
                                    permission TINYINT(4),
                                    modified_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    modified_by INT(10),
                                    status INT(1) DEFAULT 0
                                   )',
                                      true);
        }
    }
}

if (!Vtiger_Utils::CheckTable('vtiger_inbox_read')) {
    echo "<li>vtiger_inbox_read being created</li><br>";
    Vtiger_Utils::CreateTable('vtiger_inbox_read',
                              '(inbox_id INT(10),
                                user_id INT(10)
                                )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";