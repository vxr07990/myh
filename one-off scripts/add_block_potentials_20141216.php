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



// Make sure to give your file a descriptive name and place in the root of your installation.  Then access the appropriate URL in a browser.

// Turn on debugging level
$Vtiger_Utils_Log = true;
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// Or to create a new block
$module = Vtiger_Module::getInstance('Potentials'); // The module your blocks and fields will be in.
$block1 = new Vtiger_Block();
$block1->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDETAILS';
$module->addBlock($block1);


$block1->save($module);
// END Add new field
;
