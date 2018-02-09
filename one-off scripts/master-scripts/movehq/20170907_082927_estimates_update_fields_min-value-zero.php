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

// OT5139 - Don't allow for negatives in the pricing module

$fields = [
    //extra stop block
    'extrastops_weight',
     //local fields
     'local_weight',
     'local_billed_weight',
     'local_bl_discount',
     'local_mileage',
     'local_cubes',
     'local_piece_count',
     'local_pack_count',
    //move details block
    'weight',
    'billed_weight',
    'bottom_line_discount',
    'interstate_mileage',
    'estimate_cube',
    'estimate_piece_count',
    'estimate_pack_count',
    //sit details block
    'sit_origin_weight',
    'sit_dest_weight',
    'sit_origin_miles',
    'sit_dest_miles',
    'sit_origin_number_days',
    'sit_dest_number_days',
    'sit_origin_fuel_percent',
    'sit_dest_fuel_percent',
    //accessorial details block
    'acc_shuttle_origin_weight',
    'acc_shuttle_dest_weight',
    'acc_shuttle_origin_miles',
    'acc_shuttle_dest_miles',
    'acc_ot_origin_weight',
    'acc_ot_dest_weight',
    'acc_selfstg_origin_weight',
    'acc_selfstg_dest_weight',
    'acc_exlabor_origin_hours',
    'acc_exlabor_dest_hours',
    'acc_exlabor_ot_origin_hours',
    'acc_exlabor_ot_dest_hours',
    'acc_wait_origin_hours',
    'acc_wait_dest_hours',
    'acc_wait_ot_origin_hours',
    'acc_wait_ot_dest_hours'
];

$module = Vtiger_Module::getInstance('Estimates');
if(!$module){
    echo 'Module '.$module->name.' not present.';
    return;
}
$db = PearDatabase::getInstance();

$db->pquery("update `vtiger_field` set typeofdata=CONCAT(typeofdata,'~MIN=0') where tabid = ? AND fieldname IN (". generateQuestionMarks($fields).") AND typeofdata NOT LIKE '%~MIN=0%'",
        array($module->id,$fields));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";