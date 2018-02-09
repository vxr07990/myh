<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/21/2016
 * Time: 10:08 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Actuals');
if (!$module) {
    return;
}

$db = PearDatabase::getInstance();
// Hide estimates stage
$field1 = Vtiger_Field::getInstance('quotestage', $module);
if ($field1) {
    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
    $db->pquery($stmt, ['1', $field1->id]);
}

$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
if ($block1) {
    // Add new picklick for actuals
    $field2 = Vtiger_Field::getInstance('actuals_stage', $module);
    if ($field2) {
        echo "<br> Field 'actuals_stage' is already present, updating picklist <br>";
        $db = PearDatabase::getInstance();
        $tableName = 'vtiger_actuals_stage';
        $sql = "TRUNCATE TABLE `$tableName`";
        $db->pquery($sql, array());
        $field2->setPicklistValues(['Created', 'Delivered', 'Reviewed', 'Accepted', 'Rejected', 'Non-Current', 'Ready to Invoice', 'Ready to Distribute', 'Completed - Invoiced', 'Completed - Distributed', 'Completed - Invoiced/Distributed']);
    } else {
        $field2 = new Vtiger_Field();
        // reuse label
        $field2->label        = 'LBL_QUOTES_QUOTESTAGE';
        $field2->name         = 'actuals_stage';
        $field2->table        = 'vtiger_quotes';
        $field2->column       = 'actuals_stage';
        $field2->columntype   = 'varchar(200)';
        $field2->uitype       = 16;
        $field2->typeofdata   = 'V~M';
        $field2->displaytype  = 1;
        $field2->presence     = 2;
        $field2->defaultvalue = 'Created';
        $field2->quickcreate  = 0;
        $field2->summaryfield = 0;
        if ($field1) {
            $field2->sequence = 4;
        }
        $block1->addField($field2);
        $field2->setPicklistValues(['Created', 'Delivered', 'Reviewed', 'Accepted', 'Rejected', 'Non-Current', 'Ready to Invoice', 'Ready to Distribute', 'Completed - Invoiced', 'Completed - Distributed', 'Completed - Invoiced/Distributed']);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";