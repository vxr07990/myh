<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/20/2017
 * Time: 4:30 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Users');
if(!$module)
{
    return;
}
$block = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $module);
if(!$block)
{
    return;
}

$field = Vtiger_Field::getInstance('vehicles_edit_permission', $module);
if ($field) {
    echo "The vehicles_edit_permission field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_USERS_VEHICLE_EDIT_PERMISSION';
    $field->name       = 'vehicles_edit_permission';
    $field->table      = 'vtiger_users';
    $field->column     = 'vehicles_edit_permission';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}

$field = Vtiger_Field::getInstance('drivers_edit_permission', $module);
if ($field) {
    echo "The vehicles_edit_permission field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_USERS_DRIVERS_EDIT_PERMISSION';
    $field->name       = 'drivers_edit_permission';
    $field->table      = 'vtiger_users';
    $field->column     = 'drivers_edit_permission';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}


$db = &PearDatabase::getInstance();

$db->pquery('UPDATE vtiger_users SET vehicles_edit_permission=? WHERE user_name IN (' .
            implode(',',
                ['\'Deshawn.Bryant@graebelmoving.com\'',
                 '\'Steve.Cox@graebelmoving.com\'',
                '\'Shelly.Gomez@graebelmoving.com\'',
                '\'Noemi.GarciaRomero@graebelmoving.com\'',
                '\'Carol.Daughenbaugh@graebelmoving.com\'',
                '\'James.Spikes@graebelmoving.com\'',
                '\'Terry.yon@graebelmoving.com\'',
                '\'Jennifer.Chandler@graebelmoving.com\'',
                '\'Lynn.Thompson@graebelmoving.com\'',
                '\'Gloria.Gritzmacher@graebelmoving.com\'',
                ]
            ) . ')', [1]);

$db->pquery('UPDATE vtiger_users SET drivers_edit_permission=? WHERE user_name IN (' .
            implode(',',
                    ['\'Deshawn.Bryant@graebelmoving.com\'',
                     '\'Steve.Cox@graebelmoving.com\'',
                     '\'Shelly.Gomez@graebelmoving.com\'',
                     '\'Noemi.GarciaRomero@graebelmoving.com\'',
                     '\'Carol.Daughenbaugh@graebelmoving.com\'',
                     '\'James.Spikes@graebelmoving.com\'',
                     '\'Terry.yon@graebelmoving.com\'',
                     '\'Jennifer.Chandler@graebelmoving.com\'',
                     '\'Lynn.Thompson@graebelmoving.com\'',
                    ]
            ) . ')', [1]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";