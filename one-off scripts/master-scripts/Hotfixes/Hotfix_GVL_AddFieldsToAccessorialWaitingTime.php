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
 * Date: 9/1/2016
 * Time: 4:51 PM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$modules = ['Actuals', 'Estimates'];
$blockName = 'LBL_QUOTES_ACCESSORIALDETAILS';
$fields = ['hours_per_van', 'hours_first_man', 'additional_men', 'hours_per_additional_man'];
$failed = false;

foreach ($modules as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo 'Module ' . $moduleName . ' does not exist'.PHP_EOL;
        $failed = true;
        break;
    }
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo 'Block ' . $blockName . ' does not exist'.PHP_EOL;
        $failed = true;
        break;
    }
    foreach ($fields as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            echo 'Field ' . $fieldName . ' already exists'.PHP_EOL;
            $failed = true;
            break 2;
        }
    }
}

if (!$failed) {
    foreach ($modules as $moduleName) {
        $module = Vtiger_Module::getInstance($moduleName);
        $block = Vtiger_Block::getInstance($blockName, $module);
        $fieldOvertime = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $module);
        if (!$fieldOvertime) {
            $failed = true;
            echo 'Failed to find over time field'.PHP_EOL;
            break;
        }
        $seq = $fieldOvertime->sequence + 1;

        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_ACCESSORIAL_HOURS_PER_VAN';
        $field->name       = 'hours_per_van';
        $field->table      = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
        $field->column     = 'hours_per_van';   //  This will be the columnname in your database for the new field.
        $field->columntype = 'DECIMAL(10,2)';
        $field->uitype     = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
        $field->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
        $block->addField($field);
        setFieldSequenceASIBD($module, $field, $seq++);

        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_ACCESSORIAL_HOURS_FIRST_MAN';
        $field->name       = 'hours_first_man';
        $field->table      = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
        $field->column     = 'hours_first_man';   //  This will be the columnname in your database for the new field.
        $field->columntype = 'DECIMAL(10,2)';
        $field->uitype     = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
        $field->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
        $block->addField($field);
        setFieldSequenceASIBD($module, $field, $seq++);

        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_ACCESSORIAL_ADDITIONAL_MEN';
        $field->name       = 'additional_men';
        $field->table      = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
        $field->column     = 'additional_men';   //  This will be the columnname in your database for the new field.
        $field->columntype = 'INT(10)';
        $field->uitype     = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
        $field->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
        $block->addField($field);
        setFieldSequenceASIBD($module, $field, $seq++);

        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_ACCESSORIAL_HOURS_PER_ADDITIONAL_MAN';
        $field->name       = 'hours_per_additional_man';
        $field->table      = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
        $field->column     = 'hours_per_additional_man';   //  This will be the columnname in your database for the new field.
        $field->columntype = 'DECIMAL(10,2)';
        $field->uitype     = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
        $field->typeofdata = 'N~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
        $block->addField($field);
        setFieldSequenceASIBD($module, $field, $seq++);
    }
}

function setFieldSequenceGAFTAWT($module, $field, $seqNum)
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