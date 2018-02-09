<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/2/2016
 * Time: 8:59 AM
 */
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
include_once('Hotfix_CoreUtil_FixFieldSequencePerBlock.php');

$moduleNames = ['Actuals', 'Estimates'];
$blockName = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';

$db = &PearDatabase::getInstance();


foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $block = Vtiger_Block::getInstance($blockName, $module);

    $order = [];
    $field = Vtiger_Field::getInstance('space_reservation_cuft', $module);
    if($field)
    {
        $order[] = $field;
    }

    $field = Vtiger_Field::getInstance('gvl_vehicle_only', $module);
    if ($field) {
        echo "The gvl_vehicle_only field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_GVL_VEHICLEONLY';
        $field->name       = 'gvl_vehicle_only';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gvl_vehicle_only';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype     = 56;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
    $order[] = $field;
    ms_SetFieldSequence($order, $db);
}





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";