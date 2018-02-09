<?php


if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();
echo "<br /> Adding vtiger_claims_status_change table (Claims) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claims_status_change` (
  `claimsID` int(19) NOT NULL,
  `status` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `effective_date` varchar(255) NOT NULL,
  KEY (claimsID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";