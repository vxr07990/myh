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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

echo '<h2>Add New column for packing items tables</h2>';
$sql = $adb->pquery("SHOW COLUMNS FROM vtiger_packing_items LIKE 'pack_rate'");
$exists = $adb->num_rows($sql) ? true : false;

if ($exists) {
    echo '<li>The "pack_rate" column already exists on vtiger_packing_items table</li><br>';
} else {
    $sql = $adb->pquery("ALTER TABLE `vtiger_packing_items` ADD COLUMN `pack_rate` DECIMAL(12,2) NULL DEFAULT NULL AFTER `custom_rate`;");

    echo '<h2>Add "pack_rate" column to vtiger_packing_items table - SUCCESS</h2>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";