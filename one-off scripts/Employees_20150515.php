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
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once 'modules/ModComments/ModComments.php';
$employeesIsNew = false;

$module1 = Vtiger_Module::getInstance('Employees'); // The module1 your blocks and fields will be in.
if ($module1) {
    echo "<h2>Updating Employees Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Employees';
    $module1->save();
    echo "<h2>Creating Employees Module and Updating Fields</h2>";
    $module1->initTables();
}
//start block0 : LBL_CUSTOM_INFORMATION
$block0 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module1);
if ($block0) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br>";
} else {
    $block0 = new Vtiger_Block();
    $block0->label = 'LBL_CUSTOM_INFORMATION';
    $module1->addBlock($block0);
}
//start block0 fields
echo "<ul>";
$field1 = Vtiger_Field::getInstance('employee_type', $module1);
if ($field1) {
    echo "<li>The employee_type field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_EMPLOYEES_TYPE';
    $field1->name = 'employee_type';
    $field1->table = 'vtiger_employees';
    $field1->column = 'employee_type';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';
    
    $block0->addField($field1);
    $field1->setPicklistValues(array('Office Employee', 'Crew Employee', 'Contractor'));
}
//end block0 fields
echo "</ul>";
$block0->save($module1);
//end block0 : LBL_CUSTOM_INFORMATION


//start block1 : LBL_EMPLOYEES_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $module1);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3> LBL_EMPLOYEES_INFORMATION block already exists </h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_EMPLOYEES_INFORMATION';
    $module1->addBlock($block1);
    $employeesIsNew = true;
}
echo "<ul>";
//start block1 fields
$field2 = Vtiger_Field::getInstance('name', $module1);
if ($field2) {
    echo "<li>The name field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_EMPLOYEES_NAME';
    $field2->name = 'name';                                // Must be the same as column.
    $field2->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field2->column = 'name';                            //  This will be the columnname in your database for the new field.
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field2->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('employee_lastname', $module1);
if ($field3) {
    echo "<li>The employee_lastname field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_EMPLOYEES_LASTNAME';
    $field3->name = 'employee_lastname';                                // Must be the same as column.
    $field3->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field3->column = 'employee_lastname';                            //  This will be the columnname in your database for the new field.
    $field3->columntype = 'VARCHAR(50)';
    $field3->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field3->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('employee_email', $module1);
if ($field4) {
    echo "<li>The employee_email field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_EMPLOYEES_EMAIL';
    $field4->name = 'employee_email';                                // Must be the same as column.
    $field4->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field4->column = 'employee_email';                            //  This will be the columnname in your database for the new field.
    $field4->columntype = 'VARCHAR(50)';
    $field4->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field4->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('employee_mphone', $module1);
if ($field5) {
    echo "<li>The employee_mphone field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_EMPLOYEES_MPHONE';
    $field5->name = 'employee_mphone';                                // Must be the same as column.
    $field5->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field5->column = 'employee_mphone';                            //  This will be the columnname in your database for the new field.
    $field5->columntype = 'INT(20)';
    $field5->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('employee_hphone', $module1);
if ($field6) {
    echo "<li>The employee_hphone field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EMPLOYEES_HPHONE';
    $field6->name = 'employee_hphone';                                // Must be the same as column.
    $field6->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field6->column = 'employee_hphone';                            //  This will be the columnname in your database for the new field.
    $field6->columntype = 'INT(20)';
    $field6->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('address1', $module1);
if ($field7) {
    echo "<li>The address1 field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_EMPLOYEES_ADDRESS1';
    $field7->name = 'address1';                                // Must be the same as column.
    $field7->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field7->column = 'address1';                            //  This will be the columnname in your database for the new field.
    $field7->columntype = 'VARCHAR(50)';
    $field7->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('address2', $module1);
if ($field8) {
    echo "<li>The address2 field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_EMPLOYEES_ADDRESS2';
    $field8->name = 'address2';
    $field8->table = 'vtiger_employees';
    $field8->column = 'address2';
    $field8->columntype = 'VARCHAR(50)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    
    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('city', $module1);
if ($field9) {
    echo "<li>The city field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_EMPLOYEES_CITY';
    $field9->name = 'city';                                // Must be the same as column.
    $field9->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field9->column = 'city';                            //  This will be the columnname in your database for the new field.
    $field9->columntype = 'VARCHAR(50)';
    $field9->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('state', $module1);
if ($field10) {
    echo "<li>The state field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_EMPLOYEES_STATE';
    $field10->name = 'state';                                // Must be the same as column.
    $field10->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field10->column = 'state';                            //  This will be the columnname in your database for the new field.
    $field10->columntype = 'VARCHAR(50)';
    $field10->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field10);
}
$field11 = Vtiger_Field::getInstance('zip', $module1);
if ($field11) {
    echo "<li>The zip field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_EMPLOYEES_ZIP';
    $field11->name = 'zip';                                // Must be the same as column.
    $field11->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field11->column = 'zip';                            //  This will be the columnname in your database for the new field.
    $field11->columntype = 'INT(10)';
    $field11->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field11);
}
$field12 = Vtiger_Field::getInstance('country', $module1);
if ($field12) {
    echo "<li>The country field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_EMPLOYEES_COUNTRY';
    $field12->name = 'country';                                // Must be the same as column.
    $field12->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field12->column = 'country';                            //  This will be the columnname in your database for the new field.
    $field12->columntype = 'VARCHAR(50)';
    $field12->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field12);
}
$field13 = Vtiger_Field::getInstance('employee_bdate', $module1);
if ($field13) {
    echo "<li>The employee_bdate field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_EMPLOYEES_BDATE';
    $field13->name = 'employee_bdate';                                // Must be the same as column.
    $field13->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field13->column = 'employee_bdate';                            //  This will be the columnname in your database for the new field.
    $field13->columntype = 'DATE';
    $field13->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field13);
}
//end block1 fields
echo "</ul>";
$block1->save($module1);

//start block2 : LBL_EMPLOYEES_DETAILINFO
$block2 = Vtiger_Block::getInstance('LBL_EMPLOYEES_DETAILINFO', $module1);
if ($block2) {
    echo "<h3>The LBL_EMPLOYEES_DETAILINFO block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_EMPLOYEES_DETAILINFO';
    $module1->addBlock($block2);
}
echo "<ul>";
//start block2 fields
$field14 = Vtiger_Field::getInstance('employee_no', $module1);
if ($field14) {
    echo "<li>The employee_no field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_EMPLOYEES_NUMBER';
    $field14->name = 'employee_no';
    $field14->table = 'vtiger_employees';
    $field14->column = 'employee_no';
    $field14->columntype = 'VARCHAR(50)';
    $field14->uitype = 1;
    $field14->typeofdata = 'V~O';
    
    $block2->addField($field14);
}
$field15 = Vtiger_Field::getInstance('employee_prole', $module1);
if ($field15) {
    echo "<li>The employee_prole field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_EMPLOYEES_PROLE';
    $field15->name = 'employee_prole';                                // Must be the same as column.
    $field15->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field15->column = 'employee_prole';                            //  This will be the columnname in your database for the new field.
    $field15->columntype = 'VARCHAR(100)';
    $field15->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field15);
    $field15->setPicklistValues(array('Driver', 'Helper', 'Office', 'Packer', 'Sales', 'Warehouse'));
}
$field16 = Vtiger_Field::getInstance('employee_srole', $module1);
if ($field16) {
    echo "<li>The employee_srole field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_EMPLOYEES_SROLE';
    $field16->name = 'employee_srole';                                // Must be the same as column.
    $field16->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field16->column = 'employee_srole';                            //  This will be the columnname in your database for the new field.
    $field16->columntype = 'VARCHAR(100)';
    $field16->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field16->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field16);
    $field16->setPicklistValues(array('Driver', 'Helper', 'Office', 'Packer', 'Sales', 'Warehouse'));
}
$field17 = Vtiger_Field::getInstance('employee_hdate', $module1);
if ($field17) {
    echo "<li>The employee_hdate field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_EMPLOYEES_HDATE';
    $field17->name = 'employee_hdate';                                // Must be the same as column.
    $field17->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field17->column = 'employee_hdate';                            //  This will be the columnname in your database for the new field.
    $field17->columntype = 'DATE';
    $field17->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field17->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field17);
}
$field18 = Vtiger_Field::getInstance('employee_tdate', $module1);
if ($field18) {
    echo "<li>The employee_tdate field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_EMPLOYEES_TDATE';
    $field18->name = 'employee_tdate';                                // Must be the same as column.
    $field18->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field18->column = 'employee_tdate';                            //  This will be the columnname in your database for the new field.
    $field18->columntype = 'DATE';
    $field18->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field18->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field18);
}
$field19 = Vtiger_Field::getInstance('employee_status', $module1);
if ($field19) {
    echo "<li>The employee_status field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_EMPLOYEES_STATUS';
    $field19->name = 'employee_status';                                // Must be the same as column.
    $field19->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field19->column = 'employee_status';                            //  This will be the columnname in your database for the new field.
    $field19->columntype = 'VARCHAR(100)';
    $field19->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field19->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field19);
    $field19->setPicklistValues(array('Active', 'Suspended', 'Terminated'));
}
$field20 = Vtiger_Field::getInstance('employee_rdate', $module1);
if ($field20) {
    echo "<li>The employee_rdate field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_EMPLOYEES_RDATE';
    $field20->name = 'employee_rdate';                                // Must be the same as column.
    $field20->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field20->column = 'employee_rdate';                            //  This will be the columnname in your database for the new field.
    $field20->columntype = 'DATE';
    $field20->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field20->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field20);
}
//end block2 fields
echo "</ul>";
$block2->save($module1);
//end block2 : LBL_EMPLOYEES_DETAILINFO

//start of block3 : LBL_CONTRACTORS_DETAILINFO
$block3 = Vtiger_Block::getInstance('LBL_CONTRACTORS_DETAILINFO', $module1);
if ($block3) {
    echo "<h3>The LBL_CONTRACTORS_DETAILINFO block already exists</h3><br>";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_CONTRACTORS_DETAILINFO';
    $module1->addBlock($block3);
}
//start of block3 fields
$field21 = Vtiger_Field::getInstance('contractor_enum', $module1);
if ($field21) {
    echo "<li>The contractor_enum field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_CONTRACTORS_ENUM';
    $field21->name = 'contractor_enum';
    $field21->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field21->column = 'contractor_enum';   //  This will be the columnname in your database for the new field.
    $field21->columntype = 'INT(50)';
    $field21->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field21);
}
$field22 = Vtiger_Field::getInstance('contractor_prole', $module1);
if ($field22) {
    echo "<li>The contractor_prole field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_CONTRACTORS_PROLE';
    $field22->name = 'contractor_prole';
    $field22->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field22->column = 'contractor_prole';   //  This will be the columnname in your database for the new field.
    $field22->columntype = 'VARCHAR(100)';
    $field22->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field22->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field22);
    $field22->setPicklistValues(array('Driver', 'Packer', 'Warehouse'));
}
$field23 = Vtiger_Field::getInstance('contractor_hdate', $module1);
if ($field23) {
    echo "<li>The contractor_hdate field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_CONTRACTORS_HDATE';
    $field23->name = 'contractor_hdate';
    $field23->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field23->column = 'contractor_hdate';   //  This will be the columnname in your database for the new field.
    $field23->columntype = 'DATE';
    $field23->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field23->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field23);
}
$field24 = Vtiger_Field::getInstance('contractor_tdate', $module1);
if ($field24) {
    echo "<li>The contractor_tdate field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_CONTRACTORS_TDATE';
    $field24->name = 'contractor_tdate';
    $field24->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field24->column = 'contractor_tdate';   //  This will be the columnname in your database for the new field.
    $field24->columntype = 'DATE';
    $field24->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field24->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field24);
}
$field25 = Vtiger_Field::getInstance('contractor_status', $module1);
if ($field25) {
    echo "<li>The contractor_status field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CONTRACTORS_STATUS';
    $field25->name = 'contractor_status';
    $field25->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field25->column = 'contractor_status';   //  This will be the columnname in your database for the new field.
    $field25->columntype = 'VARCHAR(100)';
    $field25->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field25->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field25);
    $field25->setPicklistValues(array('Active', 'Terminated', 'Suspended'));
}
$field26 = Vtiger_Field::getInstance('contractor_rdate', $module1);
if ($field26) {
    echo "<li>The contractor_rdate field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CONTRACTORS_RDATE';
    $field26->name = 'contractor_rdate';
    $field26->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field26->column = 'contractor_rdate';   //  This will be the columnname in your database for the new field.
    $field26->columntype = 'DATE';
    $field26->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field26->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field26);
}
$field27 = Vtiger_Field::getInstance('contractor_cedate', $module1);
if ($field27) {
    echo "<li>The contractor_cedate field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_CONTRACTORS_CEDATE';
    $field27->name = 'contractor_cedate';
    $field27->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field27->column = 'contractor_cedate';   //  This will be the columnname in your database for the new field.
    $field27->columntype = 'DATE';
    $field27->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field27->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field27);
}
$field28 = Vtiger_Field::getInstance('contractor_trucknumber', $module1);
if ($field28) {
    echo "<li>The contractor_trucknumber field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CONTRACTORS_TRUCKNUMBER';
    $field28->name = 'contractor_trucknumber';
    $field28->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field28->column = 'contractor_trucknumber';   //  This will be the columnname in your database for the new field.
    $field28->columntype = 'VARCHAR(100)';
    $field28->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field28->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field28);
    //$field14->setRelatedModules(Array('Project'));
}
$field29 = Vtiger_Field::getInstance('contractor_trailernumber', $module1);
if ($field29) {
    echo "<li>The contractor_trailernumber field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_CONTRACTORS_TRAILERNUMBER';
    $field29->name = 'contractor_trailernumber';
    $field29->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field29->column = 'contractor_trailernumber';   //  This will be the columnname in your database for the new field.
    $field29->columntype = 'VARCHAR(100)';
    $field29->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field29->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field29);
}

//end of block3 fields
echo "</ul>";
$block3->save($module1);
//end of block3 : LBL_CONTRACTORS_DETAILINFO

//start block4 : LBL_EMPLOYEES_EMERGINFO
$block4 = Vtiger_Block::getInstance('LBL_EMPLOYEES_EMERGINFO', $module1);
if ($block4) {
    echo "<h3>The LBL_EMPLOYEES_EMERGINFO block already exists</h3><br> \n";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_EMPLOYEES_EMERGINFO';
    $module1->addBlock($block4);
}
echo "<ul>";
//start block4 fields
$field30 = Vtiger_Field::getInstance('employee_emgname', $module1);
if ($field30) {
    echo "<li>The employee_emgname field already exists</li><br> \n";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_EMPLOYEES_EMGNAME';
    $field30->name = 'employee_emgname';                                // Must be the same as column.
    $field30->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field30->column = 'employee_emgname';                            //  This will be the columnname in your database for the new field.
    $field30->columntype = 'VARCHAR(50)';
    $field30->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field30->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field30);
}
$field31 = Vtiger_Field::getInstance('employee_emglast', $module1);
if ($field31) {
    echo "<li>The employee_emglast field already exists</li><br> \n";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_EMPLOYEES_EMGLAST';
    $field31->name = 'employee_emglast';                                // Must be the same as column.
    $field31->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field31->column = 'employee_emglast';                            //  This will be the columnname in your database for the new field.
    $field31->columntype = 'VARCHAR(50)';
    $field31->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field31->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field31);
}
$field32 = Vtiger_Field::getInstance('employee_relation', $module1);
if ($field32) {
    echo "<li>The employee_relation field already exists</li><br> \n";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_EMPLOYEES_RELATION';
    $field32->name = 'employee_relation';                                // Must be the same as column.
    $field32->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field32->column = 'employee_relation';                            //  This will be the columnname in your database for the new field.
    $field32->columntype = 'VARCHAR(100)';
    $field32->uitype = 15;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field32->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field32);
    $field32->setPicklistValues(array('Family', '', 'Friend', 'Spouse'));
}
$field33 = Vtiger_Field::getInstance('employee_emgemail', $module1);
if ($field33) {
    echo "<li>The employee_emgemail field already exists</li><br> \n";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_EMPLOYEES_EMGEMAIL';
    $field33->name = 'employee_emgemail';                                // Must be the same as column.
    $field33->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field33->column = 'employee_emgemail';                            //  This will be the columnname in your database for the new field.
    $field33->columntype = 'VARCHAR(100)';
    $field33->uitype = 13;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field33->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field33);
}
$field34 = Vtiger_Field::getInstance('employee_emphone', $module1);
if ($field34) {
    echo "<li>The employee_emphone field already exists</li><br> \n";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_EMPLOYEES_EMPHONE';
    $field34->name = 'employee_emphone';                                // Must be the same as column.
    $field34->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field34->column = 'employee_emphone';                            //  This will be the columnname in your database for the new field.
    $field34->columntype = 'INT(20)';
    $field34->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field34->typeofdata = 'I~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field34);
}
$field35 = Vtiger_Field::getInstance('employee_ehphone', $module1);
if ($field35) {
    echo "<li>The employee_ehphone field already exists</li><br> \n";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_EMPLOYEES_EHPHONE';
    $field35->name = 'employee_ehphone';                                // Must be the same as column.
    $field35->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field35->column = 'employee_ehphone';                            //  This will be the columnname in your database for the new field.
    $field35->columntype = 'INT(20)';
    $field35->uitype = 7;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field35->typeofdata = 'I~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field35);
}
//end block4 fields
echo "</ul>";
$block4->save($module1);
//end block4 : LBL_EMPLOYEES_EMERGINFO

//start block5 : LBL_EMPLOYEES_LICENSEINFO
$block5 = Vtiger_Block::getInstance('LBL_EMPLOYEES_LICENSEINFO', $module1);
if ($block5) {
    echo "<h3>The LBL_EMPLOYEES_LICENSEINFO block already exists</h3><br> \n";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_EMPLOYEES_LICENSEINFO';
    $module1->addBlock($block5);
}
echo "<ul>";
//start block5 fields
$field36 = Vtiger_Field::getInstance('employee_dlnumber', $module1);
if ($field36) {
    echo "<li>The employee_dlnumber field already exists</li><br> \n";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_EMPLOYEES_DLNUMBER';
    $field36->name = 'employee_dlnumber';                                // Must be the same as column.
    $field36->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field36->column = 'employee_dlnumber';                            //  This will be the columnname in your database for the new field.
    $field36->columntype = 'VARCHAR(50)';
    $field36->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field36->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field36);
}
$field37 = Vtiger_Field::getInstance('employee_dlstate', $module1);
if ($field37) {
    echo "<li>The employee_dlstate field already exists</li><br> \n";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_EMPLOYEES_DLSTATE';
    $field37->name = 'employee_dlstate';                                // Must be the same as column.
    $field37->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field37->column = 'employee_dlstate';                            //  This will be the columnname in your database for the new field.
    $field37->columntype = 'VARCHAR(50)';
    $field37->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field37->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field37);
}
$field38 = Vtiger_Field::getInstance('employee_dlexpy', $module1);
if ($field38) {
    echo "<li>The employee_dlexpy field already exists</li><br> \n";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_EMPLOYEES_DLEXPY';
    $field38->name = 'employee_dlexpy';                                // Must be the same as column.
    $field38->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field38->column = 'employee_dlexpy';                            //  This will be the columnname in your database for the new field.
    $field38->columntype = 'DATE';
    $field38->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field38->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field38);
}
$field39 = Vtiger_Field::getInstance('employee_dlclass', $module1);
if ($field39) {
    echo "<li>The employee_dlclass field already exists</li><br> \n";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_EMPLOYEES_DLCLASS';
    $field39->name = 'employee_dlclass';                                // Must be the same as column.
    $field39->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
    $field39->column = 'employee_dlclass';                            //  This will be the columnname in your database for the new field.
    $field39->columntype = 'VARCHAR(20)';
    $field39->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field39->typeofdata = 'V~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field39);
}
//end block5 fields
echo "</ul>";
$block5->save($module1);
//end block5 : LBL_EMPLOYEES_LICENSEINFO

//start block6 : LBL_EMPLOYEES_SAFETYDETAILS
$block6 = Vtiger_Block::getInstance('LBL_EMPLOYEES_SAFETYDETAILS', $module1);
if ($block6) {
    echo "<h3>The LBL_EMPLOYEES_SAFETYDETAILS block already exists</h3><br> \n";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_EMPLOYEES_SAFETYDETAILS';
    $module1->addBlock($block6);
}
echo "<ul>";
//start block6 fields
$field40 = Vtiger_Field::getInstance('employees_lphysical', $module1);
if ($field40) {
    echo "<li>The employees_lphysical field already exists</li><br> \n";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_EMPLOYEES_LPHYSICAL';
    $field40->name = 'employees_lphysical';
    $field40->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field40->column = 'employees_lphysical';   //  This will be the columnname in your database for the new field.
    $field40->columntype = 'DATE';
    $field40->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field40->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field40);
}
$field41 = Vtiger_Field::getInstance('employees_nphysical', $module1);
if ($field41) {
    echo "<li>The employees_nphysical field already exists</li><br> \n";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_EMPLOYEES_NPHYSICAL';
    $field41->name = 'employees_nphysical';
    $field41->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field41->column = 'employees_nphysical';   //  This will be the columnname in your database for the new field.
    $field41->columntype = 'DATE';
    $field41->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field41->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field41);
}
$field42 = Vtiger_Field::getInstance('employees_lbackground', $module1);
if ($field42) {
    echo "<li>The employees_lbackground field already exists</li><br> \n";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_EMPLOYEES_LBACKGROUND';
    $field42->name = 'employees_lbackground';
    $field42->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field42->column = 'employees_lbackground';   //  This will be the columnname in your database for the new field.
    $field42->columntype = 'DATE';
    $field42->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field42->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field42);
}
$field43 = Vtiger_Field::getInstance('employees_lbackground', $module1);
if ($field43) {
    echo "<li>The employees_lbackground field already exists</li><br> \n";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_EMPLOYEES_NBACKGROUND';
    $field43->name = 'employees_nbackground';
    $field43->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field43->column = 'employees_nbackground';   //  This will be the columnname in your database for the new field.
    $field43->columntype = 'DATE';
    $field43->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field43->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block6->addField($field43);
}
//end block6 fields
echo "</ul>";
$block6->save($module1);
//end block6 : LBL_EMPLOYEES_SAFETYDETAILS

//start block7 : LBL_EMPLOYEES_AVAILABILITY
$block7 = Vtiger_Block::getInstance('LBL_EMPLOYEES_AVAILABILITY', $module1);
if ($block7) {
    echo "<h3>The LBL_EMPLOYEES_AVAILABILITY block already exists</h3><br> \n";
} else {
    $block7 = new Vtiger_Block();
    $block7->label = 'LBL_EMPLOYEES_AVAILABILITY';
    $module1->addBlock($block7);
}
echo "<ul>";
//start block7 fields
$field44 = Vtiger_Field::getInstance('employees_sunday', $module1);
if ($field44) {
    echo "<li>The employees_sunday field already exists</li><br> \n";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_EMPLOYEES_SUNDAY';
    $field44->name = 'employees_sunday';
    $field44->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field44->column = 'employees_sunday';   //  This will be the columnname in your database for the new field.
    $field44->columntype = 'VARCHAR(3)';
    $field44->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field44->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field44);
}
$field45 = Vtiger_Field::getInstance('employees_sundayall', $module1);
if ($field45) {
    echo "<li>The employees_sundayall field already exists</li><br> \n";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_EMPLOYEES_SUNDAYALL';
    $field45->name = 'employees_sundayall';
    $field45->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field45->column = 'employees_sundayall';   //  This will be the columnname in your database for the new field.
    $field45->columntype = 'VARCHAR(3)';
    $field45->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field45->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field45);
}
$field46 = Vtiger_Field::getInstance('employees_sundaystart', $module1);
if ($field46) {
    echo "<li>The employees_sundaystart field already exists</li><br> \n";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_EMPLOYEES_SUNDAYSTART';
    $field46->name = 'employees_sundaystart';
    $field46->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field46->column = 'employees_sundaystart';   //  This will be the columnname in your database for the new field.
    $field46->columntype = 'TIME';
    $field46->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field46->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field46);
}
$field47 = Vtiger_Field::getInstance('employees_sundayend', $module1);
if ($field47) {
    echo "<li>The employees_sundayend field already exists</li><br> \n";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_EMPLOYEES_SUNDAYEND';
    $field47->name = 'employees_sundayend';
    $field47->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field47->column = 'employees_sundayend';   //  This will be the columnname in your database for the new field.
    $field47->columntype = 'TIME';
    $field47->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field47->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field47);
}
$field48 = Vtiger_Field::getInstance('employees_monday', $module1);
if ($field48) {
    echo "<li>The employees_monday field already exists</li><br> \n";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_EMPLOYEES_MONDAY';
    $field48->name = 'employees_monday';
    $field48->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field48->column = 'employees_monday';   //  This will be the columnname in your database for the new field.
    $field48->columntype = 'VARCHAR(3)';
    $field48->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field48->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field48);
}
$field49 = Vtiger_Field::getInstance('employees_mondayall', $module1);
if ($field49) {
    echo "<li>The employees_mondayall field already exists</li><br> \n";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_EMPLOYEES_MONDAYALL';
    $field49->name = 'employees_mondayall';
    $field49->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field49->column = 'employees_mondayall';   //  This will be the columnname in your database for the new field.
    $field49->columntype = 'VARCHAR(3)';
    $field49->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field49->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field49);
}
$field50 = Vtiger_Field::getInstance('employees_mondaystart', $module1);
if ($field50) {
    echo "<li>The employees_mondaystart field already exists</li><br> \n";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_EMPLOYEES_MONDAYSTART';
    $field50->name = 'employees_mondaystart';
    $field50->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field50->column = 'employees_mondaystart';   //  This will be the columnname in your database for the new field.
    $field50->columntype = 'TIME';
    $field50->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field50->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field50);
}
$field51 = Vtiger_Field::getInstance('employees_mondayend', $module1);
if ($field51) {
    echo "<li>The employees_mondayend field already exists</li><br> \n";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_EMPLOYEES_MONDAYEND';
    $field51->name = 'employees_mondayend';
    $field51->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field51->column = 'employees_mondayend';   //  This will be the columnname in your database for the new field.
    $field51->columntype = 'TIME';
    $field51->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field51->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field51);
}
$field52 = Vtiger_Field::getInstance('employees_tuesday', $module1);
if ($field52) {
    echo "<li>The employees_tuesday field already exists</li><br> \n";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_EMPLOYEES_TUESDAY';
    $field52->name = 'employees_tuesday';
    $field52->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field52->column = 'employees_tuesday';   //  This will be the columnname in your database for the new field.
    $field52->columntype = 'VARCHAR(3)';
    $field52->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field52->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field52);
}
$field53 = Vtiger_Field::getInstance('employees_tuesdayall', $module1);
if ($field53) {
    echo "<li>The employees_tuesdayall field already exists</li><br> \n";
} else {
    $field53 = new Vtiger_Field();
    $field53->label = 'LBL_EMPLOYEES_TUESDAYALL';
    $field53->name = 'employees_tuesdayall';
    $field53->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field53->column = 'employees_tuesdayall';   //  This will be the columnname in your database for the new field.
    $field53->columntype = 'VARCHAR(3)';
    $field53->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field53->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field53);
}
$field54 = Vtiger_Field::getInstance('employees_tuesdaystart', $module1);
if ($field54) {
    echo "<li>The employees_tuesdaystart field already exists</li><br> \n";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_EMPLOYEES_TUESDAYSTART';
    $field54->name = 'employees_tuesdaystart';
    $field54->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field54->column = 'employees_tuesdaystart';   //  This will be the columnname in your database for the new field.
    $field54->columntype = 'TIME';
    $field54->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field54->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field54);
}
$field55 = Vtiger_Field::getInstance('employees_tuesdayend', $module1);
if ($field55) {
    echo "<li>The employees_tuesdayend field already exists</li><br> \n";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'LBL_EMPLOYEES_TUESDAYEND';
    $field55->name = 'employees_tuesdayend';
    $field55->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field55->column = 'employees_tuesdayend';   //  This will be the columnname in your database for the new field.
    $field55->columntype = 'TIME';
    $field55->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field55->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field55);
}
$field56 = Vtiger_Field::getInstance('employees_wednesday', $module1);
if ($field56) {
    echo "<li>The employees_wednesday field already exists</li><br> \n";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_EMPLOYEES_WEDNESDAY';
    $field56->name = 'employees_wednesday';
    $field56->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field56->column = 'employees_wednesday';   //  This will be the columnname in your database for the new field.
    $field56->columntype = 'VARCHAR(3)';
    $field56->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field56->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field56);
}
$field57 = Vtiger_Field::getInstance('employees_wednesdayall', $module1);
if ($field57) {
    echo "<li>The employees_wednesdayall field already exists</li><br> \n";
} else {
    $field57 = new Vtiger_Field();
    $field57->label = 'LBL_EMPLOYEES_WEDNESDAYALL';
    $field57->name = 'employees_wednesdayall';
    $field57->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field57->column = 'employees_wednesdayall';   //  This will be the columnname in your database for the new field.
    $field57->columntype = 'VARCHAR(3)';
    $field57->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field57->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field57);
}
$field58 = Vtiger_Field::getInstance('employees_wednesdaystart', $module1);
if ($field58) {
    echo "<li>The employees_wednesdaystart field already exists</li><br> \n";
} else {
    $field58 = new Vtiger_Field();
    $field58->label = 'LBL_EMPLOYEES_WEDNESDAYSTART';
    $field58->name = 'employees_wednesdaystart';
    $field58->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field58->column = 'employees_wednesdaystart';   //  This will be the columnname in your database for the new field.
    $field58->columntype = 'TIME';
    $field58->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field58->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field58);
}
$field59 = Vtiger_Field::getInstance('employees_wednesdayend', $module1);
if ($field59) {
    echo "<li>The employees_wednesdayend field already exists</li><br> \n";
} else {
    $field59 = new Vtiger_Field();
    $field59->label = 'LBL_EMPLOYEES_WEDNESDAYEND';
    $field59->name = 'employees_wednesdayend';
    $field59->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field59->column = 'employees_wednesdayend';   //  This will be the columnname in your database for the new field.
    $field59->columntype = 'TIME';
    $field59->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field59->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field59);
}
$field60 = Vtiger_Field::getInstance('employees_thursday', $module1);
if ($field60) {
    echo "<li>The employees_thursday field already exists</li><br> \n";
} else {
    $field60 = new Vtiger_Field();
    $field60->label = 'LBL_EMPLOYEES_THURSDAY';
    $field60->name = 'employees_thursday';
    $field60->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field60->column = 'employees_thursday';   //  This will be the columnname in your database for the new field.
    $field60->columntype = 'VARCHAR(3)';
    $field60->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field60->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field60);
}
$field61 = Vtiger_Field::getInstance('employees_thursdayall', $module1);
if ($field61) {
    echo "<li>The employees_thursdayall field already exists</li><br> \n";
} else {
    $field61 = new Vtiger_Field();
    $field61->label = 'LBL_EMPLOYEES_THURSDAYALL';
    $field61->name = 'employees_thursdayall';
    $field61->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field61->column = 'employees_thursdayall';   //  This will be the columnname in your database for the new field.
    $field61->columntype = 'VARCHAR(3)';
    $field61->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field61->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field61);
}
$field62 = Vtiger_Field::getInstance('employees_thursdaystart', $module1);
if ($field62) {
    echo "<li>The employees_thursdaystart field already exists</li><br> \n";
} else {
    $field62 = new Vtiger_Field();
    $field62->label = 'LBL_EMPLOYEES_THURSDAYSTART';
    $field62->name = 'employees_thursdaystart';
    $field62->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field62->column = 'employees_thursdaystart';   //  This will be the columnname in your database for the new field.
    $field62->columntype = 'TIME';
    $field62->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field62->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field62);
}
$field63 = Vtiger_Field::getInstance('employees_thursdayend', $module1);
if ($field63) {
    echo "<li>The employees_thursdayend field already exists</li><br> \n";
} else {
    $field63 = new Vtiger_Field();
    $field63->label = 'LBL_EMPLOYEES_THURSDAYEND';
    $field63->name = 'employees_thursdayend';
    $field63->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field63->column = 'employees_thursdayend';   //  This will be the columnname in your database for the new field.
    $field63->columntype = 'TIME';
    $field63->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field63->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field63);
}
$field64 = Vtiger_Field::getInstance('employees_friday', $module1);
if ($field64) {
    echo "<li>The employees_friday field already exists</li><br> \n";
} else {
    $field64 = new Vtiger_Field();
    $field64->label = 'LBL_EMPLOYEES_FRIDAY';
    $field64->name = 'employees_friday';
    $field64->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field64->column = 'employees_friday';   //  This will be the columnname in your database for the new field.
    $field64->columntype = 'VARCHAR(3)';
    $field64->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field64->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field64);
}
$field65 = Vtiger_Field::getInstance('employees_fridayall', $module1);
if ($field65) {
    echo "<li>The employees_fridayall field already exists</li><br> \n";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_EMPLOYEES_FRIDAYALL';
    $field65->name = 'employees_fridayall';
    $field65->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field65->column = 'employees_fridayall';   //  This will be the columnname in your database for the new field.
    $field65->columntype = 'VARCHAR(3)';
    $field65->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field65->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field65);
}
$field66 = Vtiger_Field::getInstance('employees_fridaystart', $module1);
if ($field66) {
    echo "<li>The employees_fridaystart field already exists</li><br> \n";
} else {
    $field66 = new Vtiger_Field();
    $field66->label = 'LBL_EMPLOYEES_FRIDAYSTART';
    $field66->name = 'employees_fridaystart';
    $field66->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field66->column = 'employees_fridaystart';   //  This will be the columnname in your database for the new field.
    $field66->columntype = 'TIME';
    $field66->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field66->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field66);
}
$field67 = Vtiger_Field::getInstance('employees_fridayend', $module1);
if ($field67) {
    echo "<li>The employees_fridayend field already exists</li><br> \n";
} else {
    $field67 = new Vtiger_Field();
    $field67->label = 'LBL_EMPLOYEES_FRIDAYEND';
    $field67->name = 'employees_fridayend';
    $field67->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field67->column = 'employees_fridayend';   //  This will be the columnname in your database for the new field.
    $field67->columntype = 'TIME';
    $field67->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field67->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field67);
}
$field68 = Vtiger_Field::getInstance('employees_saturday', $module1);
if ($field68) {
    echo "<li>The employees_saturday field already exists</li><br> \n";
} else {
    $field68 = new Vtiger_Field();
    $field68->label = 'LBL_EMPLOYEES_SATURDAY';
    $field68->name = 'employees_saturday';
    $field68->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field68->column = 'employees_saturday';   //  This will be the columnname in your database for the new field.
    $field68->columntype = 'VARCHAR(3)';
    $field68->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field68->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field68);
}
$field69 = Vtiger_Field::getInstance('employees_saturdayall', $module1);
if ($field69) {
    echo "<li>The employees_saturdayall field already exists</li><br> \n";
} else {
    $field69 = new Vtiger_Field();
    $field69->label = 'LBL_EMPLOYEES_SATURDAYALL';
    $field69->name = 'employees_saturdayall';
    $field69->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field69->column = 'employees_saturdayall';   //  This will be the columnname in your database for the new field.
    $field69->columntype = 'VARCHAR(3)';
    $field69->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field69->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field69);
}
$field70 = Vtiger_Field::getInstance('employees_saturdaystart', $module1);
if ($field70) {
    echo "<li>The employees_saturdaystart field already exists</li><br> \n";
} else {
    $field70 = new Vtiger_Field();
    $field70->label = 'LBL_EMPLOYEES_SATURDAYSTART';
    $field70->name = 'employees_saturdaystart';
    $field70->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field70->column = 'employees_saturdaystart';   //  This will be the columnname in your database for the new field.
    $field70->columntype = 'TIME';
    $field70->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field70->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field70);
}
$field71 = Vtiger_Field::getInstance('employees_saturdayend', $module1);
if ($field71) {
    echo "<li>The employees_saturdayend field already exists</li><br> \n";
} else {
    $field71 = new Vtiger_Field();
    $field71->label = 'LBL_EMPLOYEES_SATURDAYEND';
    $field71->name = 'employees_saturdayend';
    $field71->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
    $field71->column = 'employees_saturdayend';   //  This will be the columnname in your database for the new field.
    $field71->columntype = 'TIME';
    $field71->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field71->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block7->addField($field71);
}
//end block7 fields
echo "</ul>";
$block7->save($module1);
//end block7 : LBL_EMPLOYEES_AVAILABILITY

//start of block8 : LBL_EMPLOYEES_RECORDUPDATE
$block8 = Vtiger_Block::getInstance('LBL_EMPLOYEES_RECORDUPDATE', $module1);
if ($block8) {
    echo "<h3>The LBL_EMPLOYEES_RECORDUPDATE block already exists</h3><br>";
} else {
    $block8 = new Vtiger_Block();
    $block8->label = 'LBL_EMPLOYEES_RECORDUPDATE';
    $module1->addBlock($block8);
}
//start block8 fields
echo "<ul>";
$field72 = Vtiger_Field::getInstance('createdtime', $module1);
if ($field72) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field72 = new Vtiger_Field();
    $field72->label = 'LBL_EMPLOYEES_CREATEDTIME';
    $field72->name = 'createdtime';
    $field72->table = 'vtiger_crmentity';
    $field72->column = 'createdtime';
    $field72->columntype = 'datetime';
    $field72->uitype = 70;
    $field72->typeofdata = 'T~O';
    
    $block8->addField($field72);
}
$field73 = Vtiger_Field::getInstance('modifiedtime', $module1);
if ($field73) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field73 = new Vtiger_Field();
    $field73->label = 'LBL_EMPLOYEES_MODIFIEDTIME';
    $field73->name = 'modifiedtime';
    $field73->table = 'vtiger_crmentity';
    $field73->column = 'modifiedtime';
    $field73->columntype = 'datetime';
    $field73->uitype = 70;
    $field73->typeofdata = 'T~O';
    
    $block8->addField($field73);
}
//end block8 fields
echo "</ul>";
$block8->save($module1);
//end of block8 : LBL_EMPLOYEES_RECORDUPDATE

//start of block9 photo testing block : LBL_EMPLOYEES_PHOTO
$block9 = Vtiger_Block::getInstance('LBL_EMPLOYEES_PHOTO', $module1);
if ($block9) {
    echo "<h3>The LBL_EMPLOYEES_PHOTO block already exists</h3><br>";
} else {
    $block9 = new Vtiger_Block();
    $block9->label = 'LBL_EMPLOYEES_PHOTO';
    $module1->addBlock($block9);
}
//start of block9 fields
echo "<ul>";
$field74 = Vtiger_Field::getInstance('imagename', $module1);
if ($field74) {
    echo "<li>The imagename field already exists</li><br>";
} else {
    $field74 = new Vtiger_Field();
    $field74->label = 'LBL_EMPLOYEES_IMAGENAME';
    $field74->name = 'imagename';
    $field74->table = 'vtiger_employees';
    $field74->column = 'imagename';
    $field74->columntype = 'VARCHAR(255)';
    $field74->uitype = 69;
    $field74->typeofdata = 'V~O';
    
    $block9->addField($field74);
}

//end of block9 fields
echo "</ul>";
$block9->save($module1);
//end of block9

//add comments module
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Employees'));
ModComments::removeWidgetFrom('Employees'); //remove it before adding it because we can't tell if it already exists
ModComments::addWidgetTo('Employees');

if (!$employeesIsNew) { //if this is a pre-existing module lets fix the orders of things
    //hide the random irrelevant fields
    echo "<h2>Hiding all pre-existing fields</h2><br>";
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE tabid = '.$module1->id);
    
    //Reorder the blocks
    echo "<h2>Reordering Blocks</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 1 WHERE blockid  = ' . $block0->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 2 WHERE blockid  = ' . $block1->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid  = ' . $block2->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid  = ' . $block3->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 5 WHERE blockid  = ' . $block4->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 6 WHERE blockid  = ' . $block5->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 7 WHERE blockid  = ' . $block6->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 8 WHERE blockid  = ' . $block7->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 9 WHERE blockid  = ' . $block8->id);
    
    //Reorder the fields
    echo "<h2>Reordering Fields</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block0->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field1->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field2->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field3->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field4->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field5->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field6->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field7->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 7, presence = 0'. ' WHERE fieldid = ' . $field8->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 8, presence = 0'. ' WHERE fieldid = ' . $field9->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 9, presence = 0'. ' WHERE fieldid = ' . $field10->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 10, presence = 0'. ' WHERE fieldid = ' . $field11->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 11, presence = 0'. ' WHERE fieldid = ' . $field12->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 12, presence = 0'. ' WHERE fieldid = ' . $field13->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field14->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field15->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field16->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field17->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field18->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field19->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 7, presence = 0'. ' WHERE fieldid = ' . $field20->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field21->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field22->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field23->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field24->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field25->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field26->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 7, presence = 0'. ' WHERE fieldid = ' . $field27->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 8, presence = 0'. ' WHERE fieldid = ' . $field28->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 9, presence = 0'. ' WHERE fieldid = ' . $field29->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field30->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field31->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field32->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field33->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field34->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field35->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field36->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field37->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field38->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field39->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field40->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field41->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field42->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field43->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field44->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field45->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field46->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field47->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field48->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field49->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 7, presence = 0'. ' WHERE fieldid = ' . $field50->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 8, presence = 0'. ' WHERE fieldid = ' . $field51->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 9, presence = 0'. ' WHERE fieldid = ' . $field52->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 10, presence = 0'. ' WHERE fieldid = ' . $field53->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 11, presence = 0'. ' WHERE fieldid = ' . $field54->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 12, presence = 0'. ' WHERE fieldid = ' . $field55->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 13, presence = 0'. ' WHERE fieldid = ' . $field56->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 14, presence = 0'. ' WHERE fieldid = ' . $field57->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 15, presence = 0'. ' WHERE fieldid = ' . $field58->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 16, presence = 0'. ' WHERE fieldid = ' . $field59->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 17, presence = 0'. ' WHERE fieldid = ' . $field60->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 18, presence = 0'. ' WHERE fieldid = ' . $field61->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 19, presence = 0'. ' WHERE fieldid = ' . $field62->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 20, presence = 0'. ' WHERE fieldid = ' . $field63->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 21, presence = 0'. ' WHERE fieldid = ' . $field64->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 22, presence = 0'. ' WHERE fieldid = ' . $field65->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 23, presence = 0'. ' WHERE fieldid = ' . $field66->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 24, presence = 0'. ' WHERE fieldid = ' . $field67->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 25, presence = 0'. ' WHERE fieldid = ' . $field68->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 26, presence = 0'. ' WHERE fieldid = ' . $field69->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 27, presence = 0'. ' WHERE fieldid = ' . $field70->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block7->id . ', sequence = 28, presence = 0'. ' WHERE fieldid = ' . $field71->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block8->id . ', sequence = 1, presence = 2'. ' WHERE fieldid = ' . $field72->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block8->id . ', sequence = 2, presence = 2'. ' WHERE fieldid = ' . $field73->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block9->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field74->id);
    echo "<h2>Done</h2><br>";
}
