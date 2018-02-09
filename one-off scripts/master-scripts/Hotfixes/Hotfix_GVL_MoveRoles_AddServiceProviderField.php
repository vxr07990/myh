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


//

if (!$db) {
    $db = PearDatabase::getInstance();
}

echo '<br />Starting add service_provider field to MoveRoles<br />';

$module = Vtiger_Module::getInstance('MoveRoles');

$block = Vtiger_Block::getInstance('LBL_MOVEROLES_INFORMATION', $module);
$field = Vtiger_Field::getInstance('service_provider', $module);
if ($field) {
    echo "The service_provider field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_SERVICE_PROVIDER';
    $field->name       = 'service_provider';
    $field->table      = 'vtiger_moveroles';
    $field->column     = 'service_provider';
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 10;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $field->SetRelatedModules(array('Vendors'));
}

echo '<br />Finished add service_provider field to MoveRoles<br />';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";