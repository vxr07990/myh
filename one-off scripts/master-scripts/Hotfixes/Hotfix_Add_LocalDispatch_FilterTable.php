<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

echo "<br /> Creating vtiger_localdispatch_selectedcolumns table (OrdersTask) <br />";

$p = $db->query("CREATE TABLE IF NOT EXISTS`vtiger_localdispatch_selectedcolumns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(19) NOT NULL,
  `default_filter` varchar(1) NOT NULL,
  `date_time` varchar(20) NOT NULL,
  `table_type` varchar(1) NOT NULL,
  `filter_name` varchar(50) NOT NULL,
  `columns` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

echo "<br /> Inserting Default Filter for each table (Crew, Equipment, Vendor)  <br />";

$q = $db->query("INSERT INTO `vtiger_localdispatch_selectedcolumns` (`user_id`, `default_filter`, `date_time`, `table_type`, `filter_name`, `columns`) VALUES
(1, '1', '2016-12-14 07:16:49', 'A', 'Default View', 'name,employee_lastname,employee_type,agentid'),
(1, '1', '2016-12-14 07:16:49', 'E', 'Default View', 'vechiles_unit,vehicle_type,agentid,vehicle_number'),
(1, '1', '2016-12-14 07:26:49', 'V', 'Default View', 'vendorname,vendor_no,agentid');");


echo "<br /> Done!  <br />";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";