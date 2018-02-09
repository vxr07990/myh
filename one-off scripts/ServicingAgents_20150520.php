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


// Turn on debugging level
$Vtiger_Utils_Log = true;
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$servicingAgentsIsNew = false;


// To use a pre-existing block
$module = Vtiger_Module::getInstance('ServicingAgents'); // The module your blocks and fields will be in.
if ($module) {
    echo "<h2>Updating ServicingAgents</h2><br>";
} else {
    $module = new Vtiger_Module();
    $module->name = 'ServicingAgents';
    $module->save();
    echo "<h2>Creating Module ServicingAgents and Updating Fields</h2><br>";
    $module->initTables();
}

$block1 = Vtiger_Block::getInstance('LBL_SERVICINGAGENTS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_SERVICINGAGENTS_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_SERVICINGAGENTS_INFORMATION';
    $module->addBlock($block1);
    $servicingAgentsIsNew = true;
}

// START Add new field
$field1 = Vtiger_Field::getInstance('name', $module);
if ($field1) {
    echo "<li>The name field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Name';
    $field1->name = 'name';
    $field1->table = 'vtiger_servicingagents';
    $field1->column = 'name';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Salesperson', 'Surveyor', 'Customer Service Cordinator', 'O/A Coordinator', 'D/A Coordinator', 'Packing', 'Contractor', 'Claims Rep', 'Billing Clerk'));
        
    $module->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field2) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Assigned To';
    $field2->name = 'assigned_user_id';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'smownerid';
    $field2->uitype = 53;
    $field2->typeofdata = 'V~M';

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('CreatedTime', $module);
if ($field3) {
    echo "<li>The CreatedTime field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Created Time';
    $field3->name = 'createdtime';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'createdtime';
    $field3->uitype = 70;
    $field3->typeofdata = 'T~O';
    $field3->displaytype = 2;

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('ModifiedTime', $module);
if ($field4) {
    echo "<li>The ModifiedTime field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'Modified Time';
    $field4->name = 'modifiedtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'modifiedtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('sagent_type', $module);
if ($field5) {
    echo "<li>The sagent_type field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_SERVICINGAGENTS_AGENTTYPE';
    $field5->name = 'sagent_type';
    $field5->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
    $field5->column = 'sagent_type';   //  This will be the columnname in your database for the new field.
    $field5->columntype = 'VARCHAR(100)';
    $field5->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
    $field5->setPicklistValues(array('Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'));
}
$field6 = Vtiger_Field::getInstance('sagent_agent', $module);
if ($field6) {
    echo "<li>The sagent_agent field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_SERVICINGAGENTS_AGENT';
    $field6->name = 'sagent_agent';
    $field6->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'sagent_agent';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(100)';
    $field6->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
    $field6->setRelatedModules(array('Agents'));
}

$field7 = Vtiger_Field::getInstance('sagent_order', $module);
if ($field7) {
    echo "<li>The sagent_order field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_SERVICINGAGENTS_ORDER';
    $field7->name = 'sagent_order';
    $field7->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'sagent_order';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'VARCHAR(100)';
    $field7->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


    $block1->addField($field7);
    $field7->setRelatedModules(array('Orders'));
}
$field8 = Vtiger_Field::getInstance('sagent_contact', $module);
if ($field8) {
    echo "<li>The sagent_contact field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_SERVICINGAGENTS_CONTACT';
    $field8->name = 'sagent_contact';
    $field8->table = 'vtiger_servicingagents';  // This is the tablename from your database that the new field will be added to.
    $field8->column = 'sagent_contact';   //  This will be the columnname in your database for the new field.
    $field8->columntype = 'VARCHAR(100)';
    $field8->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field8);
    $field8->setRelatedModules(array('Contacts'));
}
$block1->save($module);
if ($servicingAgentsIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module->addFilter($filter1);

    $filter1->addField($field1);
    
    $module->setDefaultSharing();
    $module->initWebservice();
}

    $ordersInstance = Vtiger_Module::getInstance('Orders');
    $ordersInstance->setRelatedList(Vtiger_Module::getInstance('ServicingAgents'), 'Servicing Agents', array('ADD'), 'get_related_list');
//END Add navigation link in module
;
