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


$leadsModule = Vtiger_Module::getInstance('Leads');

if(!$leadsModule){
    echo "Unable to access Leads module. Exiting <br/>\n";
    return;
}

$leadsInfoBlock = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);

if(!$leadsInfoBlock){
    echo "Unable to access Leads information Block. Exiting <br/>\n";
    return;
}
$fieldMoreInformation = Vtiger_Field::getInstance('more_information', $leadsModule);
if ($fieldMoreInformation) {
    echo "The more_information field already exists<br>\n";
} else {
    $fieldMoreInformation             = new Vtiger_Field();
    $fieldMoreInformation->label      = 'LBL_MORE_INFORMATION';
    $fieldMoreInformation->name       = 'more_information';
    $fieldMoreInformation->table      = 'vtiger_leaddetails';
    $fieldMoreInformation->column     = 'more_information';
    $fieldMoreInformation->columntype = 'TEXT';
    $fieldMoreInformation->uitype     = 19;
    $fieldMoreInformation->typeofdata = 'V~O';
    $leadsInfoBlock->addField($fieldMoreInformation);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
