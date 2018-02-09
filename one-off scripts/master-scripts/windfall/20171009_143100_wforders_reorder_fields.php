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


$db = PearDatabase::getInstance();


$moduleInstance = Vtiger_Module::getInstance('WFOrders');

if(!$moduleInstance){
    return;
}

$fieldSeq = [
    'LBL_WFORDER_INFORMATION' => [
        'wforder_number',
        'business_line',
        'commodities',
        'wforder_download',
        'warehouse_status',
        'wforder_weight',
        'weight_date',
        'wforder_account',
        'wforder_contact',
        'agentid',
        'assigned_user_id'
    ],
    'LBL_WFORDER_DETAILS' => [
        'load_date',
        'wforder_firstday',
        'wforder_days_authorized',
        'wforder_overage_days',
        'wforder_control_number'
    ],
    'LBL_WFORDER_VALUATION' => [
        'wforder_valuation_type',
        'wforder_discount',
        'wforder_amount',
        'wforder_unit'
    ],
    'LBL_WFORDER_NOTES' => [
        'wforder_comment'
    ]
];


foreach($fieldSeq as $blockLabel=>$fields) {
    $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
    if(!$blockInstance){
        $blockInstance        = new Vtiger_Block();
        $blockInstance->label = $blockLabel;
        $moduleInstance->addBlock($blockInstance);
    }
    foreach ($fields as $fieldName) {
        $field = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
        if ($field) {
            $seq++;
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockInstance->id, `sequence` = $seq WHERE `fieldid` = $field->id");
        }
    }
}
