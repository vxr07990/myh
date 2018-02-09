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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Add Valuation to Tariff Manager</h1><br>\n";

$db = PearDatabase::getInstance();
$moduleName = 'TariffManager';

$blockName = 'LBL_TARIFF_VALUATION_SETTINGS';
$moduleName = 'TariffManager';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($blockName, $module);

if ($block) {
    echo '<p>Block Already Exists</p>';
} else {
    echo '<p>Block Doesn\'t Exist Adding It...</p>';
    //Add Block
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $module->addBlock($block);
}

$block = Vtiger_Block::getInstance($blockName, $module);


echo "<br><h1>End Hotfix Add Valuation to Tariff Manager</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";