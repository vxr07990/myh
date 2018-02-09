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
 * Updates to Documents to hide: invoice_pkg_format
 * To Add new field invoice_packet_include  "check box" they asked for yes/no but checkbox makes most sense.
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$blockName  = 'LBL_FILE_INFORMATION';
$moduleName = 'Documents';
$module     = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if ($block) {
        $field = Vtiger_Field::getInstance('invoice_packet_include', $module);
        if ($field) {
            echo "The invoice_packet_include field already exists<br>\n";
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_INVOICE_PACKET_INCLUDE';
            $field->name       = 'invoice_packet_include';
            $field->table      = 'vtiger_notes';
            $field->column     = 'invoice_packet_include';
            $field->columntype = 'VARCHAR(3)';
            $field->uitype     = 56;
            $field->typeofdata = 'C~O';
            $block->addField($field);
        }

        $field = Vtiger_Field::getInstance('invoice_pkg_format', $module);
        if ($field) {
            echo "Updating invoice_pkg_format to be hidden.<br />\n";
            if ($field->presence != 1) {
                $db = &PearDatabase::getInstance();
                $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ? LIMIT 1';
                echo "Running: $stmt<br />\n";
                $db->pquery($stmt, ['1', $field->id]);
            }
        }
    }
    print "<h2>finished add fields to $moduleName module. </h2>\n";

    //THIS CAN'T BE DONE, picklist is non-unique name.
//        $field = Vtiger_Field::getInstance('invoice_pkg_format', $module);
//        if ($field) {
//            $db = &PearDatabase::getInstance();
//            echo "Updating invoice_pkg_format Exists so making default value = no.<br />\n";
//            if ($field->defaultvalue != 'no') {
//                $stmt = 'UPDATE `vtiger_field` SET `defaultvalue` = ? WHERE `fieldid` = ? LIMIT 1';
//                echo "Running: $stmt<br />\n";
//                $db->pquery($stmt, ['no', $field->id]);
//            }
//            if (true) {
//                //update picklist, was going to check first but bygones be bygones.
//                echo "Updating invoice_pkg_format Exists so fixing the picklist.<br />\n";
//                $tableName = 'vtiger_invoice_pkg_format';
//                $sql = "TRUNCATE TABLE `$tableName`";
//                $db->pquery($sql, array());
//                $field->setPicklistValues(['yes', 'no']);
//            }
//        }
//    }
//    print "<h2>finished correcting fields in $moduleName module. </h2>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";