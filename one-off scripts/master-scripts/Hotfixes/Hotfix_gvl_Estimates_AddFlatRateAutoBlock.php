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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

$scriptName = 'AddFlatRateAutoBlock';

echo "<h3>Starting $scriptName</h3>\n";

$moduleName = 'Estimates';
$blockName = 'LBL_AUTO_RATE_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block already exists</p>\n";
} else {
    echo "<p>Adding $blockName to estimates</p>\n";

    $block = new Vtiger_Block();
    $block->label = $blockName;
    $module->addBlock($block);

    echo "<p>The $blockName block has been added to estimates</p>\n";
}



echo "<h3>Ending $scriptName</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";