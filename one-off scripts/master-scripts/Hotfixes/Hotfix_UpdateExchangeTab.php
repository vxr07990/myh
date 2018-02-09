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

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';

echo '<h1>Updating Exchange Tab</h1>', PHP_EOL;
echo '<ul>', PHP_EOL;

$db     = PearDatabase::getInstance();
$sql    = 'UPDATE vtiger_tab SET customized = 1, ownedby = 0, isentitytype = 0, trial = 0, version = 0, parent = ""
           WHERE name = "Exchange"';
$result = $db->query($sql);

if (!($result instanceof ADORecordSet_empty)) {
    throw new UnexpectedValueException;
}

echo '<li>Updated "Exchange" in the `vtiger_tab` table.', PHP_EOL;
echo '</ul>', PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";