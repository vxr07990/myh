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
 *
 * Reorder the blocks to match the mockup.
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$miscBlockName = 'LBL_CONTRACTS_MISC_ITEMS';
$fuelSurcName = 'LBL_CONTRACTS_FUEL_SURCHARGE_TABLE';

foreach (['Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        
        //create blocks for the two tables that aren't blocked. so we can use the db to order them.
        foreach ([$miscBlockName, $fuelSurcName] as $blockName) {
            $block = Vtiger_Block::getInstance($blockName, $module);
            if (!$block) {
                //create new block.
                $block        = new Vtiger_Block();
                $block->label = $blockName;
                $module->addBlock($block);
            }
        }
        
        $blockSeq = [
            'LBL_CONTRACTS_INFORMATION',
            'LBL_CONTRACTS_BILLING',
            'LBL_CONTRACTS_TARIFF',
            'LBL_CONTRACTS_SIT_INFORMATION',
            'LBL_CONTRACTS_INTRA_TARIFF_INFORMATION',
            'LBL_CONTRACTS_VALUATION',
            'LBL_CONTRACTS_ADDITIONAL_SERVICES',
            'LBL_CONTRACTS_INTERNATIONAL_INFORMATION',
            'LBL_CONTRACTS_ANNUALRATE',
            $miscBlockName,
            $fuelSurcName,
            'LBL_CONTRACTS_FLAT_RATE_AUTO',
            'LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO',
            'LBL_CONTRACTS_ADMINISTRATIVE',
            'LBL_CUSTOM_INFORMATION',
            'LBL_CONTRACTS_ADMIN',
        ];

        print "<li>Reordering blocks for $moduleName. </li>\n";
        reorderBlocks_CCRB($blockSeq, $module);

        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}

function reorderBlocks_CCRB($blockSeq, $module)
{
    $db = PearDatabase::getInstance();
    if ($module && is_array($blockSeq)) {
        $push_to_end = [];
        $sequence = 1;
        foreach ($blockSeq as $blockLabel) {
            if ($blockLabel && $block = Vtiger_Block::getInstance($blockLabel, $module)) {
                //block exists so we are good to move it.
                $sql    = 'SELECT blocklabel FROM `vtiger_blocks` WHERE sequence = ? AND blockid = ?';
                $result = $db->pquery($sql, [$sequence, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row['blocklabel'];
                    }
                }
                $updateStmt = 'UPDATE `vtiger_blocks` SET `sequence` = ? WHERE `blockid` = ? AND `tabid` = ?';
                $db->pquery($updateStmt, [$sequence++, $block->id, $module->getId()]);
            } else {
                print "Didn't find: $blockLabel in " . $module->getName() . " to reorder<br/>\n";
            }
        }
        
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_blocks` WHERE `tabid` = ? AND `blockid` = ?', [$module->getId(), $block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!in_array($name, $blockSeq)) {
                if ($block = Vtiger_Block::getInstance($blockLabel, $module)) {
                    $updateStmt = 'UPDATE `vtiger_blocks` SET `sequence` = ? WHERE `blockid` = ? AND `tabid` = ?';
                    $db->pquery($updateStmt, [$max++, $block->id, $module->getId()]);
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";