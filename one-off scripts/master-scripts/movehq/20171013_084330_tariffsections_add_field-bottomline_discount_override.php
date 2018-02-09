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

$moduleName = 'TariffSections';
$fieldName = 'bottomline_discount_override';
$blockName = 'LBL_TARIFFSECTIONS_INFORMATION';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($blockName, $module);
$field = Vtiger_Field::getInstance($fieldName, $module);

if ($field) {
    echo "<li>The $fieldName field already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_'.strtoupper($moduleName).'_'.strtoupper($fieldName);
    $field->name = $fieldName;
    $field->table = 'vtiger_'.strtolower($moduleName);
    $field->column = $fieldName;
    $field->columntype = 'varchar(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;

    $block->addField($field);
}

if ($module) {
    $db = &PearDatabase::getInstance();
    //Update sort order
    $fieldSeq = [
        'section_name',
        'related_tariff',
        'agentid',
        'tariffsection_sortorder',
        'is_discountable',
        'bottomline_discount_override'
    ];
    foreach ($fieldSeq as $key => $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);
        if ($fieldInstance) {
            $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, [$key + 1, $fieldInstance->id]);
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
