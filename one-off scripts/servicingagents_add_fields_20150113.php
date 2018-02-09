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



// Make sure to give your file a descriptive name and place in the root of your installation.  Then access the appropriate URL in a browser.

// Turn on debugging level
$Vtiger_Utils_Log = true;
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


// To use a pre-existing block
 $module = Vtiger_Module::getInstance('ServicingAgents'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_SERVICINGAGENTS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.



// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_SERVICINGAGENTS_AGENTTYPE';
$field1->name = 'sagent_type';
$field1->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'sagent_type';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
$field1->setPicklistValues(array('Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'));


//start adding
$field2 = new Vtiger_Field();
$field2->label = 'LBL_SERVICINGAGENTS_AGENT';
$field2->name = 'sagent_agent';
$field2->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'sagent_agent';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);
$field2->setRelatedModules(array('Agents'));


$field3 = new Vtiger_Field();
$field3->label = 'LBL_SERVICINGAGENTS_ORDER';
$field3->name = 'sagent_order';
$field3->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'sagent_order';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'VARCHAR(100)';
$field3->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block1->addField($field3);
$field3->setRelatedModules(array('Project'));


$field4 = new Vtiger_Field();
$field4->label = 'LBL_SERVICINGAGENTS_CONTACT';
$field4->name = 'sagent_contact';
$field4->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'sagent_contact';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'VARCHAR(100)';
$field4->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);
$field4->setRelatedModules(array('Contacts'));



$block1->save($module);
// END Add new field
;
