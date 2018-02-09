<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/16/2016
 * Time: 11:50 AM
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
$blockName = 'LBL_QUOTES_ACCESSORIALDETAILS';

$db = &PearDatabase::getInstance();

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }

    $block = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
    $field = Vtiger_Field::getInstance('gsa500_extra_driver_hours', $module);
    if ($field) {
        echo "The gsa500_extra_driver_hours field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_EXTRA_DRIVER_HOURS';
        $field->name       = 'gsa500_extra_driver_hours';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_extra_driver_hours';
        $field->columntype = 'DECIMAL(7,2)';
        $field->uitype     = 7;
        $field->typeofdata = 'N~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_supervisory_hours_origin_regular', $module);
    if ($field) {
        echo "The gsa500_supervisory_hours_origin_regular field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SUPERVISORY_HOURS_REGULAR';
        $field->name       = 'gsa500_supervisory_hours_origin_regular';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_supervisory_hours_origin_regular';
        $field->columntype = 'DECIMAL(7,2)';
        $field->uitype     = 7;
        $field->typeofdata = 'N~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_supervisory_hours_dest_regular', $module);
    if ($field) {
        echo "The gsa500_supervisory_hours_dest_regular field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SUPERVISORY_HOURS_REGULAR';
        $field->name       = 'gsa500_supervisory_hours_dest_regular';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_supervisory_hours_dest_regular';
        $field->columntype = 'DECIMAL(7,2)';
        $field->uitype     = 7;
        $field->typeofdata = 'N~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_supervisory_hours_origin_ot', $module);
    if ($field) {
        echo "The gsa500_supervisory_hours_ot field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SUPERVISORY_HOURS_OVERTIME';
        $field->name       = 'gsa500_supervisory_hours_origin_ot';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_supervisory_hours_origin_ot';
        $field->columntype = 'DECIMAL(7,2)';
        $field->uitype     = 7;
        $field->typeofdata = 'N~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_supervisory_hours_dest_ot', $module);
    if ($field) {
        echo "The gsa500_supervisory_hours_dest_ot field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SUPERVISORY_HOURS_OVERTIME';
        $field->name       = 'gsa500_supervisory_hours_dest_ot';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_supervisory_hours_dest_ot';
        $field->columntype = 'DECIMAL(7,2)';
        $field->uitype     = 7;
        $field->typeofdata = 'N~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_washing_machine_employee', $module);
    if ($field) {
        echo "The gsa500_washing_machine_employee field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_WASHING_MACHINE_EMPLOYEE';
        $field->name       = 'gsa500_washing_machine_employee';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_washing_machine_employee';
        $field->columntype = 'INT(9)';
        $field->uitype     = 7;
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_washing_machine_tsp', $module);
    if ($field) {
        echo "The gsa500_washing_machine_tsp field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_WASHING_MACHINE_TSP';
        $field->name       = 'gsa500_washing_machine_tsp';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_washing_machine_tsp';
        $field->columntype = 'INT(9)';
        $field->uitype     = 7;
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('gsa500_washing_machine_pedestal', $module);
    if ($field) {
        echo "The gsa500_washing_machine_pedestal field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_WASHING_MACHINE_PEDESTAL';
        $field->name       = 'gsa500_washing_machine_pedestal';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'gsa500_washing_machine_pedestal';
        $field->columntype = 'INT(9)';
        $field->uitype     = 7;
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }

    $order = [];
    $block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
    $exclusiveUse = Vtiger_Field::getInstance('exclusive_use', $module);
    if ($exclusiveUse) {
        $order[] = $exclusiveUse;
    }

    $field = Vtiger_Field::getInstance('exclusive_use_cuft', $module);
    if ($field) {
        echo "The exclusive_use_cuft field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_EXCLUSIVE_USE_CUFT';
        $field->name       = 'exclusive_use_cuft';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'exclusive_use_cuft';
        $field->columntype = 'INT(10)';
        $field->uitype     = 7;
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }
    $order[] = $field;
    $field = Vtiger_Field::getInstance('space_reservation', $module);
    if ($field) {
        echo "The space_reservation field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SPACE_RESERVATION';
        $field->name       = 'space_reservation';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'space_reservation';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype     = 56;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
    $order[] = $field;
    $field = Vtiger_Field::getInstance('space_reservation_cuft', $module);
    if ($field) {
        echo "The space_reservation_cuft field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_SPACE_RESERVATION_CUFT';
        $field->name       = 'space_reservation_cuft';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'space_reservation_cuft';
        $field->columntype = 'INT(10)';
        $field->uitype     = 7;
        $field->typeofdata = 'I~O';
        $block->addField($field);
    }
    $order[] = $field;

    ms_SetFieldSequence($order, $db);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";