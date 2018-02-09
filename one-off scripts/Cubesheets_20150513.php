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

$isNew = false;  //flag for filters at the end

$module = Vtiger_Module::getInstance('Cubesheets');
if ($module) {
    echo "<h2>Updating Cubesheets Fields</h2><br>";
} else {
    $module = new Vtiger_Module();
    $module->name = 'Cubesheets';
    $module->save();
    echo "<h2>Creating Module Cubesheets and Updating Fields</h2><br>";
    $module->initTables();
}

//start block1 : LBL_CUBESHEETS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $module);
if ($block1) {
    echo "<h3>The LBL_CUBESHEETS_INFORMATION block1 already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_CUBESHEETS_INFORMATION';
    $module->addBlock($block1);
    $isNew = true;
}
echo "<ul>";

//start block1 fields
$field0 = Vtiger_Field::getInstance('cubesheet_name', $module);
if ($field0) {
    echo "<li>The cubesheet_name field already exists</li><br> \n";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_CUBESHEETS_NAME';
    $field0->name = 'cubesheet_name';
    $field0->table = 'vtiger_cubesheets';
    $field0->column = 'cubesheet_name';
    $field0->columntype = 'VARCHAR(255)';
    $field0->uitype = 2;
    $field0->typeofdata = 'V~M';
    $field0->summaryfield = 1;

    $block1->addField($field0);

    $module->setEntityIdentifier($field0);
}

$field1 = Vtiger_Field::getInstance('contact_id', $module);
if ($field1) {
    echo "<li>The contact_id field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CUBESHEETS_CONTACT';
    $field1->name = 'contact_id';
    $field1->table = 'vtiger_cubesheets';
    $field1->column = 'contact_id';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $block1->addField($field1);

    $field1->setRelatedModules(array('Contacts'));
}

$field2 = Vtiger_Field::getInstance('potential_id', $module);
if ($field2) {
    echo "<li>The potential_id field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CUBESHEETS_OPPORTUNITY';
    $field2->name = 'potential_id';
    $field2->table = 'vtiger_cubesheets';
    $field2->column = 'potential_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;

    $block1->addField($field2);

    $field2->setRelatedModules(array('Potentials'));
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CUBESHEETS_ASSIGNEDTO';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';
    $field3->summaryfield = 1;

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('CreatedTime', $module);
if ($field4) {
    echo "<li>The CreatedTime field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CUBESHEETS_CREATEDTIME';
    $field4->name = 'CreatedTime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('ModifiedTime', $module);
if ($field5) {
    echo "<li>The ModifiedTime field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CUBESHEETS_MODIFIEDTIME';
    $field5->name = 'ModifiedTime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;

    $block1->addField($field5);
}
//from Add_IsPrimary_Field.php
$field6 = Vtiger_Field::getInstance('is_primary', $module);
if ($field6) {
    echo "<li>The is_primary field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CUBESHEETS_ISPRIMARY';
    $field6->name = 'is_primary';
    $field6->table = 'vtiger_cubesheets';
    $field6->column = 'is_primary';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'C~O';
    $field6->summaryfield = 1;

    $block1->addField($field6);
}
echo "</ul>";
$block1->save($module);
//end block1 : LBL_CUBESHEETS_INFORMATION

//start block2 : LBL_CUSTOM_INFORMATION
$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);
if ($block2) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block1 already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CUSTOM_INFORMATION';
    $module->addBlock($block2);
}
$block2->save($module);
//end block2 : LBL_CUSTOM_INFORMATION

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module->addFilter($filter1);

    $filter1->addField($field0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3);

    $module->setDefaultSharing();

    $module->initWebservice();

    $related1 = Vtiger_Module::getInstance('Potentials');
    $relationLabel1 = 'Surveys';
    $related1->setRelatedList($module, $relationLabel1, array('Add'));

    $related2 = Vtiger_Module::getInstance('Contacts');
    $relationLabel2 = 'Surveys';
    $related2->setRelatedList($module, $relationLabel2, array('Add'));

    $related3 = Vtiger_Module::getInstance('Accounts');
    $relationLabel3 = 'Surveys';
    $related3->setRelatedList($module, $relationLabel3);
}
