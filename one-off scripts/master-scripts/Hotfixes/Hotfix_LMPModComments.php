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


//include this stuff to run independent of master script
// $Vtiger_Utils_Log = true;
// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

echo "<br><h1> Adding LMP fields to ModComments</h1><br>";
$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('ModComments');

//blocks in MODCOMMENTS
//LBL_MODCOMMENTS_INFORMATION
//LBL_OTHER_INFORMATION
//LBL_CUSTOM_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);

//Provider
$field1 = Vtiger_Field::getInstance('provider', $module);
if ($field1) {
    echo "<br> The provider field already exists";
} else {
    echo "<br> The provider field doesn't exist creating it now.";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_MODCOMMENTS_PROVIDER';
    $field1->name = 'provider';
    $field1->table = 'vtiger_modcomments';
    $field1->column = 'provider';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
}
//NoteSource
$field2 = Vtiger_Field::getInstance('note_source', $module);
if ($field2) {
    echo "<br> The note_source field already exists";
} else {
    echo "<br> The note_source field doesn't exist creating it now.";
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_MODCOMMENTS_NOTESOURCE';
    $field2->name = 'note_source';
    $field2->table = 'vtiger_modcomments';
    $field2->column = 'note_source';
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";