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
$VanlinesIsNew = false;  //flag for filters at the end

//Start Vanlines Module
$module1 = Vtiger_Module::getInstance('Vanlines');
if ($module1) {
    echo "<h2>Updating Vanlines Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Vanlines';
    $module1->save();
    echo "<h2>Creating Module Vanlines and Updating Fields</h2><br>";
    $module1->initTables();
}

//start block1 : LBL_VANLINES_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_VANLINES_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_VANLINES_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VANLINES_INFORMATION';
    $module1->addBlock($block1);
    $ModNameIsNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('vanline_address1', $module1);
if ($field1) {
    echo "<li>The vanline_address1 field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VANLINES_ADDRESS1';
    $field1->name = 'vanline_address1';
    $field1->table = 'vtiger_vanlines';
    $field1->column = 'vanline_address1';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
}
$field2 = Vtiger_Field::getInstance('vanline_address2', $module1);
if ($field2) {
    echo "<li>The vanline_address2 field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_VANLINES_ADDRESS2';
    $field2->name = 'vanline_address2';
    $field2->table = 'vtiger_vanlines';
    $field2->column = 'vanline_address2';
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('vanline_city', $module1);
if ($field3) {
    echo "<li>The vanline_city field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VANLINES_CITY';
    $field3->name = 'vanline_city';
    $field3->table = 'vtiger_vanlines';
    $field3->column = 'vanline_city';
    $field3->columntype = 'VARCHAR(50)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('vanline_state', $module1);
if ($field4) {
    echo "<li>The vanline_state field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VANLINES_STATE';
    $field4->name = 'vanline_state';
    $field4->table = 'vtiger_vanlines';
    $field4->column = 'vanline_state';
    $field4->columntype = 'VARCHAR(50)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('vanline_zip', $module1);
if ($field5) {
    echo "<li>The vanline_zip field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_VANLINES_ZIP';
    $field5->name = 'vanline_zip';
    $field5->table = 'vtiger_vanlines';
    $field5->column = 'vanline_zip';
    $field5->columntype = 'INT(10)';
    $field5->uitype = 7;
    $field5->typeofdata = 'V~O';

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('vanline_country', $module1);
if ($field6) {
    echo "<li>The vanline_country field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_VANLINES_COUNTRY';
    $field6->name = 'vanline_country';
    $field6->table = 'vtiger_vanlines';
    $field6->column = 'vanline_country';
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('vanline_phone', $module1);
if ($field7) {
    echo "<li>The vanline_phone field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_VANLINES_PHONE';
    $field7->name = 'vanline_phone';
    $field7->table = 'vtiger_vanlines';
    $field7->column = 'vanline_phone';
    $field7->columntype = 'VARCHAR(50)';
    $field7->uitype = 11;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('vanline_fax', $module1);
if ($field8) {
    echo "<li>The vanline_fax field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VANLINES_FAX';
    $field8->name = 'vanline_fax';
    $field8->table = 'vtiger_vanlines';
    $field8->column = 'vanline_fax';
    $field8->columntype = 'VARCHAR(50)';
    $field8->uitype = 11;
    $field8->typeofdata = 'V~O';

    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('vanline_email', $module1);
if ($field9) {
    echo "<li>The vanline_email field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_VANLINES_EMAIL';
    $field9->name = 'vanline_email';
    $field9->table = 'vtiger_vanlines';
    $field9->column = 'vanline_email';
    $field9->columntype = 'VARCHAR(50)';
    $field9->uitype = 13;
    $field9->typeofdata = 'V~O';

    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('vanline_contact', $module1);
if ($field10) {
    echo "<li>The vanline_contact field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_VANLINES_CONTACT';
    $field10->name = 'vanline_contact';
    $field10->table = 'vtiger_vanlines';
    $field10->column = 'vanline_contact';
    $field10->columntype = 'VARCHAR(50)';
    $field10->uitype = 10;
    $field10->typeofdata = 'V~O';

    $block1->addField($field10);
    $field10->setRelatedModules(array('Contacts'));
}
$field14 = Vtiger_Field::getInstance('name', $module1);
if ($field14) {
    echo "<li>The name field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_VANLINES_NAME';
    $field14->name = 'name';
    $field14->table = 'vtiger_vanlines';
    $field14->column = 'name';
    $field14->columntype = 'VARCHAR(50)';
    $field14->uitype = 2;
    $field14->typeofdata = 'V~O';

    $block1->addField($field14);
    $module1->setEntityIdentifier($field14);
}
//end block1 fields
echo "</ul>";
$block1->save($module1);

//end block1 : LBL_VANLINES_INFORMATION

//start block2 : LBL_VANLINES_RECORDUPDATE
$block2 = Vtiger_Block::getInstance('LBL_VANLINES_RECORDUPDATE', $module1);
if ($block2) {
    echo "<h3>The LBL_VANLINES_RECORDUPDATE block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_VANLINES_RECORDUPDATE';
    $module->addBlock($block2);
}
$block2->save($module1);
//end block1 : LBL_VANLINES_RECORDUPDATE

$field15 = Vtiger_Field::getInstance('createdtime', $module1);
if ($field15) {
    echo "<li>The createdtime field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_VANLINES_CREATEDTIME';
    $field15->name = 'createdtime';
    $field15->table = 'vtiger_crmentity';
    $field15->column = 'createdtime';
    $field15->uitype = 70;
    $field15->typeofdata = 'T~O';

    $block2->addField($field15);
}

$field16 = Vtiger_Field::getInstance('modifiedtime', $module1);
if ($field16) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_VANLINES_MODIFIEDTIME';
    $field16->name = 'modifiedtime';
    $field16->table = 'vtiger_crmentity';
    $field16->column = 'modifiedtime';
    $field16->uitype = 70;
    $field16->typeofdata = 'T~O';

    $block2->addField($field16);
}

//end block1 fields
echo "</ul>";
$block2->save($module1);

//START Add navigation link in module opportunities to orders
$ordersInstance = Vtiger_Module::getInstance('Agents');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('Vanlines'), 'Van Line Agents', array('ADD'), 'get_dependents_list');
//END Add navigation link in module

if ($VanlinesIsNew) {
    $module1->setDefaultSharing();
    $module1->initWebservice();
}
//End Vanelines Module
;
