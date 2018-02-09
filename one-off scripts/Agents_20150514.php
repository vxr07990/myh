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
$AgentsIsNew = false;  //flag for filters at the end


//Start ModName Module
$module1 = Vtiger_Module::getInstance('Agents');
if ($module1) {
    echo "<h2>Updating Agents Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Agents';
    $module1->save();
    echo "<h2>Creating Module Agents and Updating Fields</h2><br>";
    $module1->initTables();
}

//start block1 : LBL_AGENTS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_AGENTS_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_AGENTS_INFORMATION';
    $module1->addBlock($block1);
    $AgentsIsNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('agent_contacts', $module1);
if ($field1) {
    echo "<li>The agent_contacts field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_AGENTS_CONTACTS';
    $field1->name = 'agent_contacts';    // Must be the same as column.
    $field1->table = 'vtiger_agents';    // This is the tablename from your database that the new field will be added to.
    $field1->column = 'agent_contacts'; //  This will be the columnname in your database for the new field.
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 10;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field1->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field1);        // Use only if this field is being added to relate to another module.
    $field1->setRelatedModules(array('Contacts'));
}
$field2 = Vtiger_Field::getInstance('agent_number', $module1);
if ($field2) {
    echo "<li>The agent_number field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_AGENTS_NUMBER';
    $field2->name = 'agent_number';                                // Must be the same as column.
    $field2->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field2->column = 'agent_number';                            //  This will be the columnname in your database for the new field.
    $field2->columntype = 'INT(15)';
    $field2->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field2->typeofdata = 'V~M';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('agent_address1', $module1);
if ($field3) {
    echo "<li>The agent_address1 field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_AGENTS_ADDRESS1';
    $field3->name = 'agent_address1';                                // Must be the same as column.
    $field3->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field3->column = 'agent_address1';                            //  This will be the columnname in your database for the new field.
    $field3->columntype = 'VARCHAR(50)';
    $field3->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field3->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('agent_address2', $module1);
if ($field4) {
    echo "<li>The agent_address2 field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_AGENTS_ADDRESS2';
    $field4->name = 'agent_address2';                                // Must be the same as column.
    $field4->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field4->column = 'agent_address2';                            //  This will be the columnname in your database for the new field.
    $field4->columntype = 'VARCHAR(50)';
    $field4->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field4->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field4);
    // Use only if this field is being added to relate to another module.
    //$field1->setRelatedModules(Array('Potentials'));  			// Make sure to change to the name of the module your blocks and fields will be in.
}
$field5 = Vtiger_Field::getInstance('agent_city', $module1);
if ($field5) {
    echo "<li>The agent_city field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_AGENTS_CITY';
    $field5->name = 'agent_city';                                // Must be the same as column.
    $field5->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field5->column = 'agent_city';                            //  This will be the columnname in your database for the new field.
    $field5->columntype = 'VARCHAR(50)';
    $field5->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('agent_state', $module1);
if ($field6) {
    echo "<li>The agent_state field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_AGENTS_STATE';
    $field6->name = 'agent_state';                                // Must be the same as column.
    $field6->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field6->column = 'agent_state';                            //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('agent_zip', $module1);
if ($field7) {
    echo "<li>The agent_zip field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_AGENTS_ZIP';
    $field7->name = 'agent_zip';                                // Must be the same as column.
    $field7->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field7->column = 'agent_zip';                            //  This will be the columnname in your database for the new field.
    $field7->columntype = 'INT(10)';
    $field7->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('agent_country', $module1);
if ($field8) {
    echo "<li>The agent_country field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_AGENTS_COUNTRY';
    $field8->name = 'agent_country';                                // Must be the same as column.
    $field8->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field8->column = 'agent_country';                            //  This will be the columnname in your database for the new field.
    $field8->columntype = 'VARCHAR(50)';
    $field8->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('agent_phone', $module1);
if ($field9) {
    echo "<li>The agent_phone field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_AGENTS_PHONE';
    $field9->name = 'agent_phone';                                // Must be the same as column.
    $field9->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field9->column = 'agent_phone';                            //  This will be the columnname in your database for the new field.
    $field9->columntype = 'INT(10)';
    $field9->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('agent_fax', $module1);
if ($field10) {
    echo "<li>The agent_fax field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_AGENTS_FAX';
    $field10->name = 'agent_fax';                                // Must be the same as column.
    $field10->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field10->column = 'agent_fax';                            //  This will be the columnname in your database for the new field.
    $field10->columntype = 'INT(10)';
    $field10->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field10);
}
$field11 = Vtiger_Field::getInstance('agent_email', $module1);
if ($field11) {
    echo "<li>The agent_email field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_AGENTS_EMAIL';
    $field11->name = 'agent_email';                                // Must be the same as column.
    $field11->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field11->column = 'agent_email';                            //  This will be the columnname in your database for the new field.
    $field11->columntype = 'VARCHAR(50)';
    $field11->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field11);
}
$field12 = Vtiger_Field::getInstance('agent_puc', $module1);
if ($field12) {
    echo "<li>The agent_puc field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_AGENTS_PUC';
    $field12->name = 'agent_puc';                                // Must be the same as column.
    $field12->table = 'vtiger_agents';                        // This is the tablename from your database that the new field will be added to.
    $field12->column = 'agent_puc';                            //  This will be the columnname in your database for the new field.
    $field12->columntype = 'INT(15)';
    $field12->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field12);
}
$field13 = Vtiger_Field::getInstance('agent_vanline', $module1);
if ($field13) {
    echo "<li>The agent_vanline field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_AGENTS_VANLINES';
    $field13->name = 'agent_vanline';    // Must be the same as column.
    $field13->table = 'vtiger_agents';    // This is the tablename from your database that the new field will be added to.
    $field13->column = 'agent_vanline'; //  This will be the columnname in your database for the new field.
    $field13->columntype = 'VARCHAR(50)';
    $field13->uitype = 10;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field13);        // Use only if this field is being added to relate to another module.
    $field13->setRelatedModules(array('Vanlines'));
}
$field14 = Vtiger_Field::getInstance('name', $module1);
if ($field14) {
    echo "<li>The name field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_AGENTS_NAME';
    $field14->name = 'name';
    $field14->table = 'vtiger_agents';
    $field14->column = 'name';
    $field14->columntype = 'VARCHAR(50)';
    $field14->uitype = 2;
    $field14->typeofdata = 'V~O';
}

//end block1 fields

echo "</ul>";
$block1->save($module1);
//end block1 : LBL_AGENTS_INFORMATION

if ($AgentsIsNew) {
    $module2 = Vtiger_Module::getInstance('Contacts');
    if ($module2) {
        $module1->setRelatedList(Vtiger_Module::getInstance('Contacts'), 'Contacts', array('ADD', 'SELECT'), 'get_related_list');
        echo "<br><h3>Set Related List Agents -> Contacts :: Add, Select</h3><br>";
    } else {
        echo "<br><h3>Could not set related list Agents -> Contacts, Contacts does not exist.</h3><br>";
    }
    
    $module3 = Vtiger_Module::getInstance('Vanlines');
    if ($module3) {
        $module1->setRelatedList(Vtiger_Module::getInstance('Vanlines'), 'Vanlines', array('ADD', 'SELECT'), 'get_related_list');
        echo "<br><h3>Set Related List Agents -> Vanlines :: Add, Select</h3><br>";
    } else {
        echo "<br><h3>Could not set related list Agents -> Vanlines, Vanlines does not exist.</h3><br>";
    }
    $module1->setDefaultSharing();

    $module1->initWebservice();
}
//End Agents Module
