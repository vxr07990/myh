<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$module = Vtiger_Module_Model::getInstance('Opportunities');

if(!$module){
    print "Unable to find Opportunities module. Exiting<br />\n";
    return;
}

$block = Vtiger_Block_Model::getInstance('LBL_POTENTIALS_INFORMATION', $module);
if($block) {
    $blockID = $block->id;
    //Moving amount field to new block
    $amountField = Vtiger_Field_Model::getInstance('amount', $module);
    if ($amountField) {
        $amountFieldCurrentBlock = $amountField->get('block')->id;
        if ($amountFieldCurrentBlock != $blockID){
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block = $blockID WHERE fieldid = $amountField->id");
        }
    }
} else {
    print "Unable to find Information block. Exiting<br />\n";
}

//Reordering fields.

$fieldOrder = [
    'contact_id', 'opportunitystatus',
    'potentialname', 'opportunityreason',
    'business_line2', 'billing_type',
    'amount', 'leadsource',
    'closingdate', 'related_to',
    'oppotunitiescontract', 'is_competitive',
    'agentid'
];

$db = PearDatabase::getInstance();
foreach ($fieldOrder as $key => $field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $module);

    $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
    $db->pquery($sql, [$key+1, $fieldInstance->id]);
}

//Clearing summary fields
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield = '0' WHERE tabid = $module->getId()");

$summaryFieldLIst = [
    'contact_id', 'potentialname',
    'business_line2', 'closingdate',
    'related_to', 'assigned_user_id',
    'origin_city', 'origin_state',
    'destination_city', 'destination_state',
    'load_date', 'amount'
];

foreach($summaryFieldLIst as $fieldName){
    $field = Vtiger_Field_Model::getInstance($fieldName, $module);
    if(!$field){
        print "$fieldName field not present. Skipping. <br />\n";
        continue;
    }
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield = '1' WHERE fieldid = ".$field->id);
}

print "\e[32mFINISHED: " . __FILE__ . "<br />\n\e[0m";
