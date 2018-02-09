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


// OT 2774 - Adding comment field for reason to shuttle.

echo "<h3>Starting AddReasonFieldtoShuttles</h3>\n";

$moduleName = 'Estimates';
$blockName = 'LBL_QUOTES_ACCESSORIALDETAILS';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block already exists</p>\n";
    addShuttleReasonFields($module, $block);
} else {
    echo "<p> The $blockName block wasn't found </p>\n";
}

function addShuttleReasonFields($module, $block)
{
    //**************** Shuttle Origin Reason *******************//
    $fieldName = 'acc_shuttle_origin_reason';
    $field     = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_ESTIMATES_'.strtoupper($fieldName);
        $field->name       = $fieldName;
        $field->table      = 'vtiger_quotes';
        $field->column     = $fieldName;
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = '1';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
    //**************** Shuttle Destination Reason *******************//
    $fieldName = 'acc_shuttle_destination_reason';
    $field     = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_ESTIMATES_'.strtoupper($fieldName);
        $field->name       = $fieldName;
        $field->table      = 'vtiger_quotes';
        $field->column     = $fieldName;
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = '1';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
    echo "<p>Reordering fields in the accessorial block</p>\n";
    $fieldOrder = [
        'acc_shuttle_origin_weight',
        'acc_shuttle_dest_weight',
        'acc_shuttle_origin_applied',
        'acc_shuttle_dest_applied',
        'acc_shuttle_origin_ot',
        'acc_shuttle_dest_ot',
        'acc_shuttle_origin_over25',
        'acc_shuttle_dest_over25',
        'acc_shuttle_origin_reason',
        'acc_shuttle_destination_reason',
        'acc_shuttle_origin_miles',
        'acc_shuttle_dest_miles',
        'acc_ot_origin_weight',
        'acc_ot_dest_weight',
        'acc_ot_origin_applied',
        'acc_ot_dest_applied',
        'acc_selfstg_origin_weight',
        'acc_selfstg_dest_weight',
        'acc_selfstg_origin_applied',
        'acc_selfstg_dest_applied',
        'acc_selfstg_origin_ot',
        'acc_selfstg_dest_ot',
        'acc_exlabor_origin_hours',
        'acc_exlabor_dest_hours',
        'apply_exlabor_rate_origin',
        'apply_exlabor_rate_dest',
        'exlabor_rate_origin',
        'exlabor_rate_dest',
        'exlabor_flat_origin',
        'exlabor_flat_dest',
        'acc_exlabor_ot_origin_hours',
        'acc_exlabor_ot_dest_hours',
        'apply_exlabor_ot_rate_origin',
        'apply_exlabor_ot_rate_dest',
        'exlabor_ot_flat_origin',
        'exlabor_ot_flat_dest',
        'exlabor_ot_rate_origin',
        'exlabor_ot_rate_dest',
        'acc_wait_origin_hours',
        'acc_wait_dest_hours',
        'acc_wait_ot_origin_hours',
        'acc_wait_ot_dest_hours',
        'bulky_article_changes',
        'rush_shipment_fee',
        'accesorial_ot_loading',
        'accesorial_ot_unloading',
        'accesorial_fuel_surcharge',
        'accesorial_expedited_service',
        'irr_charge',
    ];
    $db    = PearDatabase::getInstance();
    foreach ($fieldOrder as $key=>$field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, [$key, $fieldInstance->id]);
    }
    echo "<p>Done reordering fields in the accessorial block</p>\n";
}
echo "<h3>Finished AddReasonFieldtoShuttles</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";