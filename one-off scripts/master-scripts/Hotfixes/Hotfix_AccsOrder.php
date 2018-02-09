<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

$fieldSeq = [
    'acc_shuttle_origin_weight'=> 1,
    'acc_shuttle_dest_weight'=> 2,
    'acc_shuttle_origin_applied'=> 3,
    'acc_shuttle_dest_applied'=> 4,
    'acc_shuttle_origin_ot'=> 5,
    'acc_shuttle_dest_ot'=> 6,
    'acc_shuttle_origin_over25'=> 7,
    'acc_shuttle_dest_over25'=> 8,
    'acc_shuttle_origin_miles'=> 9,
    'acc_shuttle_dest_miles'=> 10,
    'acc_ot_origin_weight'=> 11,
    'acc_ot_dest_weight'=> 12,
    'acc_ot_origin_applied'=> 13,
    'acc_ot_dest_applied'=> 14,
    'acc_selfstg_origin_weight'=> 15,
    'acc_selfstg_dest_weight'=> 16,
    'acc_selfstg_origin_applied'=> 17,
    'acc_selfstg_dest_applied'=> 18,
    'acc_selfstg_origin_ot'=> 19,
    'acc_selfstg_dest_ot'=> 20,
    'acc_exlabor_origin_hours'=> 21,
    'acc_exlabor_dest_hours'=> 22,
    'apply_exlabor_rate_origin'=> 23,
    'apply_exlabor_rate_dest'=> 24,
    'exlabor_rate_origin'=> 25,
    'exlabor_rate_dest'=> 26,
    'exlabor_flat_origin'=> 27,
    'exlabor_flat_dest'=> 28,
    'acc_exlabor_ot_origin_hours'=> 29,
    'acc_exlabor_ot_dest_hours'=> 30,
    'apply_exlabor_ot_rate_origin'=> 31,
    'apply_exlabor_ot_rate_dest'=> 32,
    'exlabor_ot_flat_origin'=> 33,
    'exlabor_ot_flat_dest'=> 34,
    'exlabor_ot_rate_origin'=> 35,
    'exlabor_ot_rate_dest'=> 36,
    'acc_wait_origin_hours'=> 37,
    'acc_wait_dest_hours'=> 38,
    'acc_wait_ot_origin_hours'=> 39,
    'acc_wait_ot_dest_hours'=> 40,
    'bulky_article_changes'=> 41,
    'rush_shipment_fee'=> 42,
    'accesorial_ot_loading'=> 43,
    'accesorial_ot_unloading'=> 44,
    'accesorial_fuel_surcharge'=> 45,
    'accesorial_expedited_service'=> 46,
    'irr_charge'=> 47,
    //if you need to add new fields add them to this and set the sequence values appropriately
    //set up as 'fieldname'=> sequence,
];
$est = Vtiger_Module::getInstance('Estimates');
$block = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $est);
$db = PearDatabase::getInstance();
$push_to_end = [];
foreach ($fieldSeq as $name=>$seq) {
    $field = Vtiger_Field::getInstance($name, $est);
    if ($field) {
        $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
        $result = $db->pquery($sql, [$seq, $block->id]);
        if ($result) {
            while ($row = $result->fetchRow()) {
                $push_to_end[] = $row[0];
            }
        }
        Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
    }
    unset($field);
}
//push anything that might have gotten added and isn't on the list to the end of the block
$max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0]+1;
foreach ($push_to_end as $name) {
    //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
    if (!array_key_exists($name, $fieldSeq)) {
        $field = Vtiger_Field::getInstance($name, $est);
        if ($field) {
            Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
            $max++;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";