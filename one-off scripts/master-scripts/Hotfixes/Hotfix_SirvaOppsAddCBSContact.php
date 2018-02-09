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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin hotfix sirva opps add cbs contact field<br>";

$oppsModel = Vtiger_Module::getInstance('Opportunities');
$STSBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_REGISTERSTS', $oppsModel);

$contactField = Vtiger_Field::getInstance('cbs_contact', $oppsModel);
if ($contactField) {
    echo "<br>The cbs_contact field already exists<br>\n";
} else {
    $contactField             = new Vtiger_Field();
    $contactField->label      = 'LBL_OPPORTUNITIES_CBSCONTACT';
    $contactField->name       = 'cbs_contact';
    $contactField->table      = 'vtiger_potential';
    $contactField->column     = 'cbs_contact';
    $contactField->columntype = 'VARCHAR(75)';
    $contactField->uitype     = '1';
    $contactField->typeofdata = 'V~O';
    $STSBlock->addField($contactField);
    //hacky bullshit courtesy of alex
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 200  WHERE fieldname = 'cbs_ind' AND tablename = 'vtiger_potential'");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 300  WHERE fieldname = 'cbs_contact' AND tablename = 'vtiger_potential'");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 400  WHERE fieldname = 'express_shipment' AND tablename = 'vtiger_potential'");
}

echo "<br>begin hotfix sirva opps add cbs contact field<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";