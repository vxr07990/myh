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

// OT4801 - Estimate Module - Add picklist "Pricing Type" to Estimate Details block - resequence fields
// OT4804 - Estimate Module - Add checkbox field to Estimate Details block

if (!function_exists("reorderFieldsByBlock_Pricing")) {
    function reorderFieldsByBlock_Pricing($fieldSeq, $blockLabel, $moduleName)
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
}

$moduleName = 'Estimates';
$module = Vtiger_Module::getInstance($moduleName);

$blockName = 'LBL_QUOTE_INFORMATION';
$block1 = Vtiger_Block::getInstance($blockName, $module);

$fieldName0 = 'pricing_type2';
$field0 = Vtiger_Field::getInstance($fieldName0, $module);
if ($field0) {
    $field0->delete();
    echo "<li>The $fieldName0 field was removed</li><br>";
}

$fieldName1 = 'pricing_mode';
$field1 = Vtiger_Field::getInstance($fieldName1, $module);
if ($field1) {
    echo "<li>The $fieldName1 field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_'. strtoupper($fieldName1);
    $field1->name = $fieldName1;
    $field1->table = 'vtiger_quotes';
    $field1->column = $fieldName1;
    $field1->columntype = 'varchar(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->defaultvalue = 'Estimate';

    $block1->addField($field1);
    
    $field1->setPicklistValues(array('Estimate','Actual Rating'));
}

$fieldName2 = 'is_multi_day';
$field2 = Vtiger_Field::getInstance($fieldName2, $module);
if ($field2) {
    echo "<li>The $fieldName2 field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_'. strtoupper($fieldName2);
    $field2->name = $fieldName2;
    $field2->table = 'vtiger_quotes';
    $field2->column = $fieldName2;
    $field2->columntype = 'varchar(3)';
    $field2->uitype = 56;
    $field2->typeofdata = 'C~O';
    $field2->displaytype = 1;

    $block1->addField($field2);
}

//reorder fields
$orderFieldSeq = [
    'subject',
    'pricing_mode',
    'load_date',
    'validtill',
    'business_line_est',
    'commodities',
    'billing_type',
    'authority',
    'quotestage',
    'is_primary',
    'estimate_type',
    'contact_id',
    'potential_id',
    'orders_id',
    'account_id',
    'contract',
    'is_multi_day',
    'effective_tariff',
    'agentid',
    'assigned_user_id'
];

reorderFieldsByBlock_Pricing($orderFieldSeq, $blockName, $moduleName);
