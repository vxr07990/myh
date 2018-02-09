<?php
/**
 * FAKE NEWS
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/23/2017
 * Time: 3:32 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$tables = [
    'vtiger_servicecodes' => '(code INT(11), active TINYINT(1), description TEXT, PRIMARY KEY (code))',
    'vtiger_servicecodes_update' => '(updateid INT(11) AUTO_INCREMENT, userid INT(11), update_time DATETIME, PRIMARY KEY (updateid))'
];

foreach($tables as $tableName => $tableDescription) {
    if (!Vtiger_Utils::CheckTable($tableName)) {
        Vtiger_Utils::CreateTable($tableName, $tableDescription, true);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
