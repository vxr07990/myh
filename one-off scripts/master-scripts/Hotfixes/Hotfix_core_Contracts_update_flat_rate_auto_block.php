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


/*
 *
 *Goals:
 * Creates the table to store the flat rate auto information for contracts.
 *
 * create Block: Additional Flat Rate Auto Charges.
 *
 * add labels for this in: languages/en_us/Contracts.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_CONTRACTS_FLAT_RATE_AUTO';
foreach (['Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        //just jamming this in here
        if (!$block) {
            //create new block.
            $block        = new Vtiger_Block();
            $block->label = $blockName;
            $module->addBlock($block);
        }
        
        //no harm in making sure.
        if ($block) {
            if (!Vtiger_Utils::CheckTable('vtiger_contract_flat_rate_auto')) {
                echo "<li>creating vtiger_contract_flat_rate_auto </li><br>";
                Vtiger_Utils::CreateTable('vtiger_contract_flat_rate_auto',
                                          '(
								contractid INT(11),
								from_mileage int(11),
								to_mileage int(11),
								rate DECIMAL(10,2),
								discount varchar(3),
								line_item_id INT(11) AUTO_INCREMENT,
								PRIMARY KEY (line_item_id)
							  )', true);
                echo "<li>Table vtiger_contract_flat_rate_auto created</li><br>";
            } else {
                echo "<li>Table vtiger_contract_flat_rate_auto already exists</li><br>";
            }
        }
        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";