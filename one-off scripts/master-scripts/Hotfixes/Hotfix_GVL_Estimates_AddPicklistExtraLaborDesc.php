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


// OT 2902 - Adding service description picklist

echo "Starting Add Picklist Extra Labor Desc<br/>";

$module = Vtiger_Module::getInstance('Estimates');
if ($module) {
    echo "Module exists";
    $block = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
    if ($block) {
        echo "<li>The LBL_QUOTES_ACCESSORIALDETAILS block exists</li><br>";

        $serviceDescPicklist =[
            'Assemble/Disassemble Bookcase',
            'Assemble/Disassemble Computer',
            'Assemble/Disassemble Pool Table',
            'Assemble/Disassemble Stereo',
            'Assemble/Disassemble Swingset/Play',
            'Assemble/Disassemble Wall Unit',
            'Assemble/Disassemble Waterbed',
            'Assemble/Disassemble Weight Bench',
            'Attic Placement',
            'Attic Removal',
            'Labor Services',
            'Other Service',
            'PBO Unpack Only - Full Service Pack',
            'PBO Unpack/Repack - Custom Serivce Pack',
            'Repack/Check PBOs',
            'Service GF/GM Clock',
            'Unwrap Fine Finish Wrapped Items',
            'Unwrap Upholstery Wrapped Items',
        ];

        $field0 = Vtiger_Field::getInstance('acc_exlabor_origin_desc', $module);
        if ($field0) {
            echo '<p>acc_exlabor_orign_desc field exists</p>';
        } else {
            $db  = PearDatabase::getInstance();
            $sql = "TRUNCATE TABLE `acc_exlabor_origin_desc`";
            $db->pquery($sql, []);
            $field0             = new Vtiger_Field();
            $field0->label      = 'LBL_QUOTES_ACCEXLABORORIGINDESC';
            $field0->name       = 'acc_exlabor_origin_desc';
            $field0->table      = 'vtiger_quotes';
            $field0->column     = 'acc_exlabor_origin_desc';
            $field0->columntype = 'VARCHAR(200)';
            $field0->uitype     = '16';
            $field0->typeofdata = 'V~O';
            $block->addField($field0);
            $field0->setPicklistValues($serviceDescPicklist);
            echo '<p>Added acc_exlabor_origin_desc picklist</p>';
        }
        $field1 = Vtiger_Field::getInstance('acc_exlabor_dest_desc', $module);
        if ($field1) {
            echo '<p>acc_exlabor_dest_desc field exists</p>';
        } else {
            $db  = PearDatabase::getInstance();
            $sql = "TRUNCATE TABLE `acc_exlabor_dest_desc`";
            $db->pquery($sql, []);
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_QUOTES_ACCEXLABORDESTDESC';
            $field1->name       = 'acc_exlabor_dest_desc';
            $field1->table      = 'vtiger_quotes';
            $field1->column     = 'acc_exlabor_dest_desc';
            $field1->columntype = 'VARCHAR(200)';
            $field1->uitype     = '16';
            $field1->typeofdata = 'V~O';
            $block->addField($field1);
            $field1->setPicklistValues($serviceDescPicklist);
            echo '<p>Added acc_exlabor_dest_desc picklist</p>';
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
            'acc_exlabor_origin_desc',
            'acc_exlabor_dest_desc',
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
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";