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
require_once 'vendor/autoload.php';

$db    = PearDatabase::getInstance();
$sql   = 'SELECT COUNT(fieldid) FROM vtiger_field WHERE columnname = "is_parent" ORDER BY fieldid DESC';
$count = $db->getOne($sql);

if (!is_numeric($count)) {
    throw new UnexpectedValueException('Did not receive a numeric result.');
}

if ($count > 1) {
    $sql = 'SELECT fieldid FROM vtiger_field WHERE columnname = "is_parent" ORDER BY fieldid DESC';
    $id  = $db->getOne($sql);

    // -----

    /**
     * @todo Verify this delete operation is successful.
     */
    $sql    = 'DELETE FROM vtiger_field WHERE fieldid = ?';
    $result = $db->pquery($sql, [$id]);

    echo PHP_EOL, 'Deleted duplicate `is_parent` field from `vtiger_field`.', PHP_EOL;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";