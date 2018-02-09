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



// $Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('quotess/ModTracker/ModTracker.php');
// include_once('quotess/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'quotess/Users/Users.php';

echo "<br>Begin LeadType Hotfix...<br>";

$quotes = Vtiger_Module::getInstance('Quotes');

$block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quotes);
if ($block) {
    echo "<br> block 'LBL_QUOTE_INFORMATION' exists, attempting to add Lead Type field<br>";
    $field = Vtiger_Field::getInstance('lead_type', $quotes);
    if ($field) {
        echo "<br> Lead Type field already exists.<br>";
    } else {
        echo "<br> Lead Type field doesn't exist, adding it now.<br>";
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_LEAD_TYPE';
        $field->name = 'lead_type';
        $field->table = 'vtiger_quotes';
        $field->column = 'lead_type';
        $field->columntype = 'VARCHAR(200)';
        $field->uitype = 16;
        $field->typeofdata = 'V~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        
        $block->addField($field);
        $field->setPicklistValues(['Consumer', 'National Account', 'OA Survey']);
        echo "<br> Lead Type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_QUOTE_INFORMATION' doesn't exist, no action taken.<br>";
}

$est = Vtiger_Module::getInstance('Estimates');

$block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $est);
if ($block) {
    echo "<br> block 'LBL_QUOTE_INFORMATION' exists, attempting to add Lead Type field<br>";
    $field = Vtiger_Field::getInstance('lead_type', $est);
    if ($field) {
        echo "<br> Lead Type field already exists.<br>";
    } else {
        echo "<br> Lead Type field doesn't exist, adding it now.<br>";
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_LEAD_TYPE';
        $field->name = 'lead_type';
        $field->table = 'vtiger_quotes';
        $field->column = 'lead_type';
        $field->columntype = 'VARCHAR(200)';
        $field->uitype = 16;
        $field->typeofdata = 'V~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        
        $block->addField($field);
        $field->setPicklistValues(['Consumer', 'National Account', 'OA Survey']);
        echo "<br> Lead Type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_QUOTE_INFORMATION' doesn't exist, no action taken.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";