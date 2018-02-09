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


include_once('vtlib/Vtiger/Module.php');

//Set up the tab/module
unset($moduleInstance);
$moduleInstance = Vtiger_Module::getInstance('AutoSpotQuote');

//Set up the block
unset($blockInstance);
$blockInstance = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleInstance);

$field = Vtiger_Field::getInstance('auto_sts_response', $moduleInstance);
if ($field) {
    echo "<br> The auto_sts_response field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_STSRESPONSE';
    $field->name = 'auto_sts_response';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_sts_response';
    $field->columntype = 'varchar(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockInstance->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";