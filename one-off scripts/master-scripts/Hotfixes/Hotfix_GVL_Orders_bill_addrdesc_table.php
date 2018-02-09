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

$db = PearDatabase::getInstance();
if (!Vtiger_Utils::CheckTable('vtiger_bill_addrdesc')) {
    $stmt = '
CREATE TABLE IF NOT EXISTS `vtiger_bill_addrdesc` (
  `bill_addrdescid` int(11) NOT NULL AUTO_INCREMENT,
  `bill_addrdesc` varchar(200) NOT NULL,
  `sortorderid` int(11) DEFAULT NULL,
  `presence` int(11) NOT NULL DEFAULT "1",
  PRIMARY KEY (`bill_addrdescid`)
)';
    $db->query($stmt);
    }

if (!Vtiger_Utils::CheckTable('vtiger_bill_addrdesc_seq')) {
    $stmt = '
CREATE TABLE IF NOT EXISTS `vtiger_bill_addrdesc_seq` (
  `id` int(11) NOT NULL
)';
    $db->query($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";