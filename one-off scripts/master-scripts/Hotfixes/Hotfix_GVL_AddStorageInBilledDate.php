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
 * Date: 8/24/2016
 * Time: 11:12 AM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleStorage = Vtiger_Module::getInstance('Storage');
if ($moduleStorage) {
    $block1 = Vtiger_Block::getInstance('LBL_STORAGE_SITDETAILS', $moduleStorage);
    if ($block1) {
        $field1 = Vtiger_Field::getInstance('storage_dateinbilled', $moduleStorage);
        if ($field1) {
            echo "<li>The storage_dateinbilled field already exists</li><br>";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_STORAGE_SITDATEINBILLED';
            $field1->name       = 'storage_dateinbilled';
            $field1->table      = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
            $field1->column     = 'storage_dateinbilled';   //  This will be the columnname in your database for the new field.
            $field1->columntype = 'DATE';
            $field1->uitype     = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
            $field1->typeofdata = 'D~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
            $block1->addField($field1);
            $seq      = 3;
            $fieldSDO = Vtiger_Field::getInstance('storage_dateout', $moduleStorage);
            if ($fieldSDO) {
                $seq = $fieldSDO->sequence + 1;
            }
            setFieldSequenceASIBD($moduleStorage, $field1, $seq);
        }
    }
}

function setFieldSequenceASIBD($module, $field, $seqNum)
{
    $db              = PearDatabase::getInstance();
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