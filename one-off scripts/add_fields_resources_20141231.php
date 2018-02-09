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


// for Employees
$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_EMPLOYEES_DATEOUT';
$field1->name = 'date_out';                                // Must be the same as column.
$field1->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_out';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EMPLOYEES_TIMEOUT';
$field1->name = 'time_out';
$field1->table = 'vtiger_employees';
$field1->column = 'time_out';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EMPLOYEES_DATEIN';
$field1->name = 'date_in';                                // Must be the same as column.
$field1->table = 'vtiger_employees';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_in';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EMPLOYEES_TIMEIN';
$field1->name = 'time_in';
$field1->table = 'vtiger_employees';
$field1->column = 'time_in';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$block1->save($module);
// END Add new field




// for Vehicles
$module = Vtiger_Module::getInstance('Vehicles'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_VEHICLES_DATEOUT';
$field1->name = 'date_out';                                // Must be the same as column.
$field1->table = 'vtiger_vehicles';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_out';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_VEHICLES_TIMEOUT';
$field1->name = 'time_out';
$field1->table = 'vtiger_vehicles';
$field1->column = 'time_out';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_VEHICLES_DATEIN';
$field1->name = 'date_in';                                // Must be the same as column.
$field1->table = 'vtiger_vehicles';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_in';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_VEHICLES_TIMEIN';
$field1->name = 'time_in';
$field1->table = 'vtiger_vehicles';
$field1->column = 'time_in';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$block1->save($module);
// END Add new field






// for Equipment
$module = Vtiger_Module::getInstance('Equipment'); // The module your blocks and fields will be in.
$block1 = Vtiger_Block::getInstance('LBL_EQUIPMENT_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.

// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_EQUIPMENT_DATEOUT';
$field1->name = 'date_out';                                // Must be the same as column.
$field1->table = 'vtiger_equipment';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_out';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EQUIPMENT_TIMEOUT';
$field1->name = 'time_out';
$field1->table = 'vtiger_equipment';
$field1->column = 'time_out';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EQUIPMENT_DATEIN';
$field1->name = 'date_in';                                // Must be the same as column.
$field1->table = 'vtiger_equipment';                        // This is the tablename from your database that the new field will be added to.
$field1->column = 'date_in';                            //  This will be the columnname in your database for the new field.
$field1->columntype = 'DATE';
$field1->uitype = 5;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'D~O';                                // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_EQUIPMENT_TIMEIN';
$field1->name = 'time_in';
$field1->table = 'vtiger_equipment';
$field1->column = 'time_in';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';
$block1->addField($field1);

$block1->save($module);
// END Add new field
;
