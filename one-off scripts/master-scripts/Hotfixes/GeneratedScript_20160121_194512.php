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

$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');
$modulePotentials = Vtiger_Module::getInstance('Potentials');

$blockOpportunities302 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $moduleOpportunities);
if ($blockOpportunities302) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_LEADDETAILS block already exists in Opportunities <br>";
} else {
    $blockOpportunities302 = new Vtiger_Block();
    $blockOpportunities302->label = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS';
    $moduleOpportunities->addBlock($blockOpportunities302);
}

$blockPotentials302 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $modulePotentials);
if ($blockPotentials302) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_LEADDETAILS block already exists in Potentials <br>";
} else {
    $blockPotentials302 = new Vtiger_Block();
    $blockPotentials302->label = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS';
    $modulePotentials->addBlock($blockPotentials302);
}

$blockOpportunities303 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_HIDEDELETE', $moduleOpportunities);
if ($blockOpportunities303) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_HIDEDELETE block already exists in Opportunities <br>";
} else {
    $blockOpportunities303 = new Vtiger_Block();
    $blockOpportunities303->label = 'LBL_OPPORTUNITIES_BLOCK_HIDEDELETE';
    $moduleOpportunities->addBlock($blockOpportunities303);
}

$blockPotentials303 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_HIDEDELETE', $modulePotentials);
if ($blockPotentials303) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_HIDEDELETE block already exists in Potentials <br>";
} else {
    $blockPotentials303 = new Vtiger_Block();
    $blockPotentials303->label = 'LBL_OPPORTUNITIES_BLOCK_HIDEDELETE';
    $modulePotentials->addBlock($blockPotentials303);
}

/*Language Strings

    'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS' => 'Lead Details',
    'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS' => 'Lead Details',
    'LBL_OPPORTUNITIES_BLOCK_HIDEDELETE' => 'Hide Delete',
    'LBL_OPPORTUNITIES_BLOCK_HIDEDELETE' => 'Hide Delete',
*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";