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

// OT4798 - Estimates - Remove / Hide the picklist "Quotation Type" within Estimate Details block
// OT4799 - Estimate Module - Remove / Hide fields from "Move Details" block

$module = Vtiger_Module::getInstance('Estimates');

$fieldsToHide = [
    'quotation_type',
    'bottom_line_distribution_discount',
    'sit_distribution_discount'
];
$db    = PearDatabase::getInstance();
foreach ($fieldsToHide as $fieldName) {
    $field1 = Vtiger_Field::getInstance($fieldName, $module);
    if ($field1) {
        $sql    = "UPDATE `vtiger_field`
            SET   `presence` = '1'
            WHERE  `fieldid` = ?";
        $query = $db->pquery($sql, [$field1->id]);
        echo '<li> '.$db->getAffectedRowCount($query).' Rows where updated in vtiger_field where label = '.$field1->label;
    }
}

