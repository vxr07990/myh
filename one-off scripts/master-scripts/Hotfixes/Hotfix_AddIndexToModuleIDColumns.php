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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/30/2016
 * Time: 12:52 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;
echo 'Checking module table keys'.PHP_EOL;

$db = PearDatabase::getInstance();

$sql = 'SELECT name FROM `vtiger_tab`';
$res = $db->pquery($sql);
while ($row = $res->fetchRow()) {
    $module = Vtiger_Module::getInstance($row['name']);
    if (!$module) {
        echo 'Failed to find module with name ' . $row['name'] . PHP_EOL;
    }

    if (!$module->basetable) {
        echo $module->name.' does not have a base table defined. '.PHP_EOL;
        continue;
    }
    echo 'At module ' . $module->name . ' with base table `'.$module->basetable.'`'.PHP_EOL;
    if (!$module->basetableid) {
        echo 'Base table id field not found'.PHP_EOL;
    }
    $stmt = 'SELECT COLUMN_KEY FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" 
            AND table_name = ? AND column_name = ? LIMIT 1';
    $column = $db->pquery($stmt, [$module->basetable, $module->basetableid])->fetchRow();
    if (!$column['COLUMN_KEY']) {
        echo $module->basetable.' does not have a key ... adding PRIMARY'.PHP_EOL;
        $db->pquery('ALTER TABLE `'.$module->basetable.'` ADD PRIMARY KEY('.$module->basetableid.')');
    } else {
        echo $module->basetable.' has a key of ' . $column['COLUMN_KEY'].PHP_EOL;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";