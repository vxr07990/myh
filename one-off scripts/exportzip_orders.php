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



require_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Package.php');
//require_once('vtlib/Vtiger/Language.php');
$package = new Vtiger_Package();
$package->export(
      Vtiger_Module::getInstance('Orders'),
      'test1/vtlib',
      'Orders-Export.zip',
      true
);
