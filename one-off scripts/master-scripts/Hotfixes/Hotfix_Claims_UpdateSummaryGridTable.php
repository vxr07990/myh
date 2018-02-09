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


echo "<br /> Updating vtiger_claims_summarygrid table (Claims) <br />";

Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_claims_summarygrid` ADD `distribution` varchar(3) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_claims_summarygrid` ADD `distribution_date` date NOT NULL;");
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_claims_summarygrid` ADD `agent_type` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `claims_id`;');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_claims_summarygrid` ADD `agent_id` INT(10)  NULL  DEFAULT NULL  AFTER `agent_type`;');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_claims_summarygrid` ADD `serviceprovider_id` INT(10)  NULL  DEFAULT NULL  AFTER `agent_id`;');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_claims_summarygrid` CHANGE `id` `id` INT(11) UNSIGNED  NOT NULL  AUTO_INCREMENT  PRIMARY KEY;');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";