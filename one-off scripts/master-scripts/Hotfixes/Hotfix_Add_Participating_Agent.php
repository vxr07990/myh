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

if (!Vtiger_Utils::CheckTable('vtiger_participating_agents')) {
    echo "<li>vtiger_participating_agents being created</li><br>";
    Vtiger_Utils::CreateTable('vtiger_participating_agents',
                              '(id INT NOT NULL AUTO_INCREMENT,
                              	crmentity_id INT(10),
                                inbox_id INT(10),
								                agent_id INT(10),
                                agent_type INT(15),
                								permission TINYINT(4),
                                modified_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                modified_by INT(10),
                								status INT(1) DEFAULT 0,
                								PRIMARY KEY(id)
                               )', true);
}


if (!Vtiger_Utils::CheckTable('vtiger_inbox_read')) {
    echo "<li>vtiger_inbox_read being created</li><br>";
    Vtiger_Utils::CreateTable('vtiger_inbox_read',
                              '(inbox_id INT(10),
                                user_id INT(10)
                                )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";