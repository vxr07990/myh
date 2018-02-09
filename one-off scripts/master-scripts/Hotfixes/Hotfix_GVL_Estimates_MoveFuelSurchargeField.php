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


// OT 16796 - Relocating the Fuel Surcharge field from Accessorials to Interstate Move Details


$fieldName = 'accesorial_fuel_surcharge';
$moduleNames = ['Estimates', 'Actuals'];


$newBlockName = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';

$fieldSequence = [
    'weight',                       'tweight',
    'gweight',                      'pickup_date',
    'full_pack',                    'full_unpack',
    'overtime_pack',                'overtime_unpack',
    'storage_inspection_fee',       'bottom_line_discount',
    'interstate_mileage',           'guaranteed_price',
    'linehaul_disc',                'accessorial_disc',
    'packing_disc',                 'sit_disc',
    'interstate_effective_date',    'pricing_type',
    'bottom_line_distribution_discount', 'estimate_cube',
    'estimate_piece_count',         'estimate_pack_count',
    'sit_distribution_discount',    'accesorial_fuel_surcharge',
    'irr_charge',
    'storage_inspection_fee',       'crating_disc',
    'overtime_unpack',              'overtime_pack',
    'effective_tariff'
];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";

        return;
    } else {
        $block = Vtiger_Block::getInstance($newBlockName, $module);
        //no harm in making sure.
        if ($block) {
            //move these fields
            $moveFields = [
                $fieldName
            ];
            moveFields_EMFSF($moveFields, $module, $block->id);
            echo "<br>Reordering fields in $newBlockName for $moduleName<br/>\n";
            reorderFieldsByBlock_EMFSF($fieldSequence, $newBlockName, $moduleName);
        }
    }
    echo "<br>Completed moving Fuel Surcharge Fields in $moduleName<br/>\n";
}


function moveFields_EMFSF($fields, $module, $newBlockID)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->block != $newBlockID) {
                    echo "Updating $field_name to be a have blockID = $newBlockID <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `block` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$newBlockID, $field0->id]);
                }
            }
        }
    }
    return false;
}

function reorderFieldsByBlock_EMFSF($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end = [];
            $seq = 1;
            foreach ($fieldSeq as $name) {
                if ($name && $field = Vtiger_Field::getInstance($name, $module)) {
                    $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                    $result = $db->pquery($sql, [$seq, $block->id]);
                    if ($result) {
                        while ($row = $result->fetchRow()) {
                            $push_to_end[] = $row['fieldname'];
                        }
                    }
                    $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                    $db->pquery($updateStmt, [$seq++, $field->id, $block->id]);
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!in_array($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                        $db->pquery($updateStmt, [$max++, $field->id, $block->id]);
                        $max++;
                    }
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";