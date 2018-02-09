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
echo "<br /> Adding vtiger_claims_summarygrid table (Claims) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS`vtiger_claims_summarygrid` (
  `id` int(11) NOT NULL,
  `claims_id` int(11) NOT NULL,
  `array_id` varchar(30) NOT NULL,
  `claim_class` varchar(30) NOT NULL,
  `agent_chargeback` varchar(255) NOT NULL,
  `service_providerchargeBack` varchar(255) NOT NULL,
  `effective_date` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";