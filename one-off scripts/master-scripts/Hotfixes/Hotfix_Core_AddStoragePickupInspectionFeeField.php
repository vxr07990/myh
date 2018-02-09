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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/29/2016
 * Time: 10:44 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleNames = ['Estimates', 'Actuals'];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
    $field = Vtiger_Field::getInstance('storage_inspection_fee', $module);
    if ($field) {
        echo "The storage_inspection_fee field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_STORAGE_INSPECTION_FEE';
        $field->name       = 'storage_inspection_fee';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'storage_inspection_fee';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype     = 56;
        $field->typeofdata = 'V~O';
        $block->addField($field);
        setFieldSequenceCASPIFF($module, $field, 'full_unpack');
    }
}

function setFieldSequenceCASPIFF($module, $field, $afterFieldName)
{
    $db              = PearDatabase::getInstance();
    $afterField = Vtiger_Field::getInstance($afterFieldName, $module);
    if ($afterField) {
        $seqNum = $afterField->sequence + 1;
    } else {
        // Don't do anything, just leave the field where it is
        return;
    }
    $fieldID         = $field->id;
    $currentSequence = $field->sequence;
    $stmt            = 'SELECT tabid FROM `vtiger_field` WHERE fieldid='.$fieldID;
    $res             = $db->pquery($stmt);
    if ($res->fetchInto($row)) {
        $tabID = $row['tabid'];
        $stmt  = 'UPDATE `vtiger_field` SET sequence = sequence - 1 WHERE sequence > '.$currentSequence.' AND tabid = '.$tabID;
        $db->pquery($stmt);
        $stmt = 'UPDATE `vtiger_field` SET sequence = sequence + 1 WHERE sequence >= '.$seqNum.' AND tabid = '.$tabID;
        $db->pquery($stmt);
        $stmt = 'UPDATE `vtiger_field` SET sequence = '.$seqNum.' WHERE fieldid = '.$fieldID;
        $db->pquery($stmt);
    } else {
        echo "<li>Failed to find table id for field</li><br>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";