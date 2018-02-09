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

$module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_EMPLOYEES_SAFETYDETAILS';
$module->addBlock($block3);

 $module = Vtiger_Module::getInstance('Employees'); // The module your blocks and fields will be in.
//$module = Vtiger_Module::getInstance('VanlineContacts'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_EMPLOYEES_AVAILABILITY';
$module->addBlock($block4);



$field2 = new Vtiger_Field();
$field2->label = 'LBL_EMPLOYEES_LPHYSICAL';
$field2->name = 'employees_lphysical';
$field2->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'employees_lphysical';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'DATE';
$field2->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_EMPLOYEES_NPHYSICAL';
$field3->name = 'employees_nphysical';
$field3->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'employees_nphysical';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field3);


$field5 = new Vtiger_Field();
$field5->label = 'LBL_EMPLOYEES_LBACKGROUND';
$field5->name = 'employees_lbackground';
$field5->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'employees_lbackground';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_EMPLOYEES_NBACKGROUND';
$field6->name = 'employees_nbackground';
$field6->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'employees_nbackground';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field6);

$block3->save($module);


$field10 = new Vtiger_Field();
$field10->label = 'LBL_EMPLOYEES_SUNDAY';
$field10->name = 'employees_sunday';
$field10->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'employees_sunday';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(3)';
$field10->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_EMPLOYEES_SUNDAYALL';
$field11->name = 'employees_sundayall';
$field11->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'employees_sundayall';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(3)';
$field11->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_EMPLOYEES_SUNDAYSTART';
$field12->name = 'employees_sundaystart';
$field12->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'employees_sundaystart';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'TIME';
$field12->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field12);
 
$field16 = new Vtiger_Field();
$field16->label = 'LBL_EMPLOYEES_SUNDAYEND';
$field16->name = 'employees_sundayend';
$field16->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'employees_sundayend';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'TIME';
$field16->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field16);
 
$field17 = new Vtiger_Field();
$field17->label = 'LBL_EMPLOYEES_MONDAY';
$field17->name = 'employees_monday';
$field17->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'employees_monday';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(3)';
$field17->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_EMPLOYEES_MONDAYALL';
$field18->name = 'employees_mondayall';
$field18->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'employees_mondayall';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(3)';
$field18->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_EMPLOYEES_MONDAYSTART';
$field19->name = 'employees_mondaystart';
$field19->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field19->column = 'employees_mondaystart';   //  This will be the columnname in your database for the new field.
$field19->columntype = 'TIME';
$field19->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_EMPLOYEES_MONDAYEND';
$field20->name = 'employees_mondayend';
$field20->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field20->column = 'employees_mondayend';   //  This will be the columnname in your database for the new field.
$field20->columntype = 'TIME';
$field20->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field20);

$field21 = new Vtiger_Field();
$field21->label = 'LBL_EMPLOYEES_TUESDAY';
$field21->name = 'employees_tuesday';
$field21->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field21->column = 'employees_tuesday';   //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(3)';
$field21->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_EMPLOYEES_TUESDAYALL';
$field22->name = 'employees_tuesdayall';
$field22->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field22->column = 'employees_tuesdayall';   //  This will be the columnname in your database for the new field.
$field22->columntype = 'VARCHAR(3)';
$field22->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_EMPLOYEES_TUESDAYSTART';
$field23->name = 'employees_tuesdaystart';
$field23->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field23->column = 'employees_tuesdaystart';   //  This will be the columnname in your database for the new field.
$field23->columntype = 'TIME';
$field23->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field23->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field23);

 
$field24 = new Vtiger_Field();
$field24->label = 'LBL_EMPLOYEES_TUESDAYEND';
$field24->name = 'employees_tuesdayend';
$field24->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field24->column = 'employees_tuesdayend';   //  This will be the columnname in your database for the new field.
$field24->columntype = 'TIME';
$field24->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field24);
 
$field25 = new Vtiger_Field();
$field25->label = 'LBL_EMPLOYEES_WEDNESDAY';
$field25->name = 'employees_wednesday';
$field25->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field25->column = 'employees_wednesday';   //  This will be the columnname in your database for the new field.
$field25->columntype = 'VARCHAR(3)';
$field25->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field25->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field25);

$field26 = new Vtiger_Field();
$field26->label = 'LBL_EMPLOYEES_WEDNESDAYALL';
$field26->name = 'employees_wednesdayall';
$field26->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field26->column = 'employees_wednesdayall';   //  This will be the columnname in your database for the new field.
$field26->columntype = 'VARCHAR(3)';
$field26->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field26->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field26);

$field27 = new Vtiger_Field();
$field27->label = 'LBL_EMPLOYEES_WEDNESDAYSTART';
$field27->name = 'employees_wednesdaystart';
$field27->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field27->column = 'employees_wednesdaystart';   //  This will be the columnname in your database for the new field.
$field27->columntype = 'TIME';
$field27->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field27->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field27);

$field28 = new Vtiger_Field();
$field28->label = 'LBL_EMPLOYEES_WEDNESDAYEND';
$field28->name = 'employees_wednesdayend';
$field28->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field28->column = 'employees_wednesdayend';   //  This will be the columnname in your database for the new field.
$field28->columntype = 'TIME';
$field28->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field28->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'LBL_EMPLOYEES_THURSDAY';
$field29->name = 'employees_thursday';
$field29->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field29->column = 'employees_thursday';   //  This will be the columnname in your database for the new field.
$field29->columntype = 'VARCHAR(3)';
$field29->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field29->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'LBL_EMPLOYEES_THURSDAYALL';
$field30->name = 'employees_thursdayall';
$field30->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field30->column = 'employees_thursdayall';   //  This will be the columnname in your database for the new field.
$field30->columntype = 'VARCHAR(3)';
$field30->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field30->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field30);

$field31 = new Vtiger_Field();
$field31->label = 'LBL_EMPLOYEES_THURSDAYSTART';
$field31->name = 'employees_thursdaystart';
$field31->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field31->column = 'employees_thursdaystart';   //  This will be the columnname in your database for the new field.
$field31->columntype = 'TIME';
$field31->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field31->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field31);

$field32 = new Vtiger_Field();
$field32->label = 'LBL_EMPLOYEES_THURSDAYEND';
$field32->name = 'employees_thursdayend';
$field32->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field32->column = 'employees_thursdayend';   //  This will be the columnname in your database for the new field.
$field32->columntype = 'TIME';
$field32->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field32->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field32);

$field33 = new Vtiger_Field();
$field33->label = 'LBL_EMPLOYEES_FRIDAY';
$field33->name = 'employees_friday';
$field33->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field33->column = 'employees_friday';   //  This will be the columnname in your database for the new field.
$field33->columntype = 'VARCHAR(3)';
$field33->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field33->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field33);

$field34 = new Vtiger_Field();
$field34->label = 'LBL_EMPLOYEES_FRIDAYALL';
$field34->name = 'employees_fridayall';
$field34->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field34->column = 'employees_fridayall';   //  This will be the columnname in your database for the new field.
$field34->columntype = 'VARCHAR(3)';
$field34->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field34->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field34);

$field35 = new Vtiger_Field();
$field35->label = 'LBL_EMPLOYEES_FRIDAYSTART';
$field35->name = 'employees_fridaystart';
$field35->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field35->column = 'employees_fridaystart';   //  This will be the columnname in your database for the new field.
$field35->columntype = 'TIME';
$field35->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field35->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field35);

$field36 = new Vtiger_Field();
$field36->label = 'LBL_EMPLOYEES_FRIDAYEND';
$field36->name = 'employees_fridayend';
$field36->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field36->column = 'employees_fridayend';   //  This will be the columnname in your database for the new field.
$field36->columntype = 'TIME';
$field36->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field36->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field36);

$field37 = new Vtiger_Field();
$field37->label = 'LBL_EMPLOYEES_SATURDAY';
$field37->name = 'employees_saturday';
$field37->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field37->column = 'employees_saturday';   //  This will be the columnname in your database for the new field.
$field37->columntype = 'VARCHAR(3)';
$field37->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field37->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field37);

$field38 = new Vtiger_Field();
$field38->label = 'LBL_EMPLOYEES_SATURDAYALL';
$field38->name = 'employees_saturdayall';
$field38->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field38->column = 'employees_saturdayall';   //  This will be the columnname in your database for the new field.
$field38->columntype = 'VARCHAR(3)';
$field38->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field38->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field38);

$field39 = new Vtiger_Field();
$field39->label = 'LBL_EMPLOYEES_SATURDAYSTART';
$field39->name = 'employees_saturdaystart';
$field39->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field39->column = 'employees_saturdaystart';   //  This will be the columnname in your database for the new field.
$field39->columntype = 'TIME';
$field39->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field39->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field39);

$field40 = new Vtiger_Field();
$field40->label = 'LBL_EMPLOYEES_SATURDAYEND';
$field40->name = 'employees_saturdayend';
$field40->table = 'vtiger_employees';  // This is the tablename from your database that the new field will be added to.
$field40->column = 'employees_saturdayend';   //  This will be the columnname in your database for the new field.
$field40->columntype = 'TIME';
$field40->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field40->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field40);


$block4->save($module);
