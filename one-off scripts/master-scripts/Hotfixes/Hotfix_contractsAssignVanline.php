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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$contracts = Vtiger_Module::getInstance('Contracts');

if ($contracts) {
    echo "<br> contacts exists, attempting to add administrative block";
    $admin_block = Vtiger_Block::getInstance('LBL_CONTRACTS_ADMINISTRATIVE', $contracts);
    if ($admin_block) {
        echo "<br>The LBL_CONTRACTS_ADMINISTRATIVE block already exists<br>";
    } else {
        $admin_block = new Vtiger_Block();
        $admin_block->label = 'LBL_CONTRACTS_ADMINISTRATIVE';
        $admin_block->sequence = 2;
        $contracts->addBlock($admin_block);
        echo "<br>The LBL_CONTRACTS_ADMINISTRATIVE created!<br>";
    }
} else {
    echo "<br> contracts doesn't exist, no action taken";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";