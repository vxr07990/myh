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


//Hotfix_Add_Orders_Cancellation_Table.php

$db = PearDatabase::getInstance();
echo "<br /> Adding vtiger_orders_cancelation_log table (Orders) <br />";

$db->pquery("CREATE TABLE `vtiger_orders_cancelation_log` (
  `id` int(11) NOT NULL,
  `ordersid` int(10) NOT NULL,
  `action` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `user` int(10) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";