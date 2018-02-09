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


// OT 16055, updating maximum RVP field to allow 9 total digits (values up to 9,999,999.99)

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_CONTRACTS_VALUATION';
foreach (['Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);


        //no harm in making sure.
        if ($block) {
            $field_name = 'maximum_rvp';
            $field0 = Vtiger_Field::getInstance($field_name, $module);


            if ($field0) {
                echo "<h3>The $field_name field exists</h3><br>";
                //update the presence
                $db          = PearDatabase::getInstance();
                    //hell you have to fix the created table!  ... sigh.
                    $stmt = 'EXPLAIN `' . $field0->table . '` `' . $field_name . '`';
                if ($res = $db->pquery($stmt)) {
                    while ($value = $res->fetchRow()) {
                        if ($value['Field'] == $field_name) {
                            if (strtolower($value['Type']) != strtolower('DECIMAL(9,2)')) {
                                echo "Updating $field_name to be a " . strtolower('DECIMAL(9,2)') . " type.<br />\n";
                                $stmt = 'ALTER TABLE `' . $field0->table . '` MODIFY COLUMN `' . $field_name . '` ' . strtolower('DECIMAL(9,2)') . ' DEFAULT NULL';
                                $db->pquery($stmt);
                            }
                                //we're only affecting the $field_name so if we find it just break
                                break;
                        }
                    }
                }
            } else {
                echo "<h3> Field $field_name not present.</h3><br>";
            }
        } else {
            echo "<h3>The $blockName block wasn't found.</h3><br>";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";