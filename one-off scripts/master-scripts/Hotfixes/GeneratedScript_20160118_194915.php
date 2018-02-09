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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleLeads = Vtiger_Module::getInstance('Leads');

$blockLeads300 = Vtiger_Block::getInstance('LBL_LEADS_BLOCK_LMPDETAILS', $moduleLeads);
if ($blockLeads300) {
    echo "<br> The LBL_LEADS_BLOCK_LMPDETAILS block already exists in Leads <br>";
} else {
    $blockLeads300 = new Vtiger_Block();
    $blockLeads300->label = 'LBL_LEADS_BLOCK_LMPDETAILS';
    $moduleLeads->addBlock($blockLeads300);
}

$blockLeads301 = Vtiger_Block::getInstance('LBL_LEADS_BLOCK_TODELETE', $moduleLeads);
if ($blockLeads301) {
    echo "<br> The LBL_LEADS_BLOCK_TODELETE block already exists in Leads <br>";
} else {
    $blockLeads301 = new Vtiger_Block();
    $blockLeads301->label = 'LBL_LEADS_BLOCK_TODELETE';
    $moduleLeads->addBlock($blockLeads301);
}

/*Language Strings

    'LBL_LEADS_BLOCK_LMPDETAILS' => 'LMP Details',
    'LBL_LEADS_BLOCK_TODELETE' => 'To Delete',
*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";