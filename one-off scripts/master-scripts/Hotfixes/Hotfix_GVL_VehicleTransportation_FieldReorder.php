<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//OT 1812 - Reordering fields in VehicleTransportation to make hiding some fields when it is a guest block in orders easier.

echo "<br> begin Vehicle Transportation update";

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('VehicleTransportation');
if (!$module) {
    return;
}
$db = &PearDatabase::getInstance();

$newField = Vtiger_Field::getInstance('vehicletrans_ratingtype', $module);
$block = Vtiger_Block::getInstance('LBL_VEHICLETRANSPORTATION_INFORMATION', $module);
if ($block) {
    if (!$newField) {
        $newField             = new Vtiger_Field();
        $newField->label      = 'LBL_VEHICLETRANSPORTATION_RATINGTYPE';
        $newField->name       = 'vehicletrans_ratingtype';
        $newField->table      = 'vtiger_vehicletransportation';
        $newField->column     = 'vehicletrans_ratingtype';
        $newField->columntype = 'VARCHAR(20)';
        $newField->uitype     = 16;
        $newField->typeofdata = 'V~M';
        $block->addField($newField);
        $newField->setPicklistValues(['Bulky', 'Flat Rate']);
    } else {
        $db->pquery('TRUNCATE TABLE vtiger_vehicletrans_ratingtype');
        $newField->setPicklistValues(['Bulky', 'Flat Rate']);
    }
}

$hideFields = [
    'vehicletrans_miles',
    'vehicletrans_diversions',    'vehicletrans_valamount'
];

echo "<p>Reordering fields in Vehicle Transportation module</p>\n";
$fieldOrder = [
    'vehicletrans_ratingtype',
    'vehicletrans_description',  'vehicletrans_make',
    'vehicletrans_modelyear',    'vehicletrans_model',
    'vehicletrans_type',        'vehicletrans_inoperable',
    'vehicletrans_groundclearance',     'vehicletrans_oversized',
    'vehicletrans_weight',      'vehicletrans_cube',
    'vehicletrans_sitdays',     'vehicletrans_sitmiles',
    'vehicletrans_ot',          'vehicletrans_miles',
    'vehicletrans_diversions',    'vehicletrans_valamount'

];

foreach ($fieldOrder as $key => $field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $module);

    $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
    $db->pquery($sql, [$key+1, $fieldInstance->id]);
}
echo "<p>Done reordering fields in Vehicle Transportation module</p>\n";

hideFields_VTFR($hideFields, $module);

function hideFields_VTFR($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";