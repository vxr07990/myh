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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//Start Opportunities Module
$module1 = Vtiger_Module::getInstance('Opportunities');
if ($module1) {
    echo "<h2>Updating Opportunities Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Opportunities';
    $module1->save();
    echo "<h2>Creating Module Opportunities and Updating Fields</h2><br>";
    $module1->initTables();
}

//start block1 : LBL_POTENTIALS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_POTENTIALS_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_POTENTIALS_INFORMATION';
    $module1->addBlock($block1);
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('participating_agents_full', $module1);
if ($field1) {
    echo "<li>The participating_agents_full field already exists</li><br> \n";
} else {
    echo "<li>Setting field</li><br> \n";
    $field1 = new Vtiger_Field();
    echo "<li>Setting Label</li><br> \n";
    $field1->label = 'LBL_PARTICIPATING_AGENTS_FULL';
    echo "<li>Setting Name</li><br> \n";
    $field1->name = 'participating_agents_full';
    echo "<li>Setting Table</li><br> \n";
    $field1->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
    echo "<li>Setting Column</li><br> \n";
    $field1->column = 'participating_agents_full';   //  This will be the columnname in your database for the new field.
    echo "<li>Setting ColumnType</li><br> \n";
    $field1->columntype = 'VARCHAR(100)';
    echo "<li>Setting UiType</li><br> \n";
    $field1->uitype = 200; // CUSTOM MULTIAGENT UITYPE
    echo "<li>Setting typeOfData</li><br> \n";
    $field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    echo "<li>Adding Field to Block</li><br> \n";
    $block1->addField($field1);
    echo "<li>Field Creation Complete</li><br> \n";
}

$field2 = Vtiger_Field::getInstance('participating_agents_no_rates', $module1);
if ($field2) {
    echo "<li>The participating_agents_no_rates field already exists</li><br> \n";
} else {
    echo "<li>Setting field</li><br> \n";
    $field2 = new Vtiger_Field();
    echo "<li>Setting Label</li><br> \n";
    $field2->label = 'LBL_PARTICIPATING_AGENTS_NO_RATES';
    echo "<li>Setting Name</li><br> \n";
    $field2->name = 'participating_agents_no_rates';
    echo "<li>Setting Table</li><br> \n";
    $field2->table = 'vtiger_potential';  // This is the tablename from your database that the new field will be added to.
    echo "<li>Setting Column</li><br> \n";
    $field2->column = 'participating_agents_no_rates';   //  This will be the columnname in your database for the new field.
    echo "<li>Setting ColumnType</li><br> \n";
    $field2->columntype = 'VARCHAR(100)';
    echo "<li>Setting UiType</li><br> \n";
    $field2->uitype = 200; // CUSTOM MULTIAGENT UITYPE
    echo "<li>Setting typeOfData</li><br> \n";
    $field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    echo "<li>Adding Field to Block</li><br> \n";
    $block1->addField($field2);
    echo "<li>Field Creation Complete</li><br> \n";
}

echo "<br> <h1> SCRIPT COMPLETED </h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";