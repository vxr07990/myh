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
 * Date: 9/6/2016
 * Time: 10:15 AM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Documents');
if ($module) {
    $field1 = Vtiger_Field::getInstance('filestatus', $module);
    if ($field1) {
        $db = PearDatabase::getInstance();
        $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldname="filestatus" AND columnname=? AND tabid=?',
                    [$field1->column, $module->id]);
        echo 'Hiding Documents Active field'.PHP_EOL;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";