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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
echo "<br>Begin LeadType Hotfix...<br>";

$leadsModule = Vtiger_Module::getInstance('Leads');

$leadsBlock = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
if ($leadsBlock) {
    echo "<br> block 'LBL_LEADS_INFORMATION' exists, attempting to add Lead Type field<br>";
    $leadType = Vtiger_Field::getInstance('lead_type', $leadsModule);
    if ($leadType) {
        echo "<br> Lead Type field already exists.<br>";
    } else {
        echo "<br> Lead Type field doesn't exist, adding it now.<br>";
        $leadType = new Vtiger_Field();
        $leadType->label = 'LBL_LEADS_TYPE';
        $leadType->name = 'lead_type';
        $leadType->table = 'vtiger_leaddetails';
        $leadType->column = 'lead_type';
        $leadType->columntype = 'VARCHAR(200)';
        $leadType->uitype = 16;
        $leadType->typeofdata = 'V~O';
        $leadType->displaytype = 1;
        $leadType->quickcreate = 0;
        
        $leadsBlock->addField($leadType);
        $leadType->setPicklistValues(['Consumer', 'National Account', 'OA Survey']);
        echo "<br> Lead Type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_LEADS_INFORMATION' doesn't exist, no action taken.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";