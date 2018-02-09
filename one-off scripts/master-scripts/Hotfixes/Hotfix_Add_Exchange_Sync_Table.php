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

require_once 'include/database/PearDatabase.php';

$db = \PearDatabase::getInstance();

$result = $db->pquery(" SELECT *
                        FROM information_schema.tables
                        WHERE table_schema = 'QA_Sirva'
                        AND table_name = 'exchange_pid'
                        LIMIT 1;
                      ");

if ($result->numRows() == 0) {
    $db->pquery("CREATE TABLE `exchange_pid` (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `pid` int(10) NOT NULL,
      `user_id` int(10) NOT NULL,
      `start_time` datetime NOT NULL
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

} else {
  print "<br><br> Table `exchange_pid` already exists. Continuing.";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";