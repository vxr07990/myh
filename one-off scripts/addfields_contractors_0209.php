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

$module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_CONTRACTORS_SAFETYDETAILS';
$module->addBlock($block3);

 $module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_CONTRACTORS_AVAILABILITY';
$module->addBlock($block4);

 $module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
$block5 = new Vtiger_Block();
$block5->label = 'LBL_CONTRACTORS_LICENSEINFORMATION';
$module->addBlock($block5);

// To use a pre-existing block
 $module = Vtiger_Module::getInstance('Contractors'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_CONTRACTORS_DETAILINFO', $module);  // Must be the actual instance name, not just what appears in the browser.

$field1 = new Vtiger_Field();
$field1->label = 'LBL_CONTRACTORS_TRUCKNUMBER';
$field1->name = 'contractor_trucknumber';
$field1->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'contractor_trucknumber';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);


$field2 = new Vtiger_Field();
$field2->label = 'LBL_CONTRACTORS_TRAILERNUMBER';
$field2->name = 'contractor_trailernumber';
$field2->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'contractor_trailernumber';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_CONTRACTORS_NPHYSICAL';
$field3->name = 'contractor_nphysical';
$field3->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'contractor_nphysical';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'DATE';
$field3->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_CONTRACTORS_LPHYSICAL';
$field4->name = 'contractor_lphysical';
$field4->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'contractor_lphysical';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'DATE';
$field4->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field4);


$field5 = new Vtiger_Field();
$field5->label = 'LBL_CONTRACTORS_LBACKGROUND';
$field5->name = 'contractor_lbackground';
$field5->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'contractor_lbackground';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'DATE';
$field5->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_CONTRACTORS_NBACKGROUND';
$field6->name = 'contractor_nbackground';
$field6->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'contractor_nbackground';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field6);

$block3->save($module);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_CONTRACTORS_SUNDAY';
$field10->name = 'contractor_sunday';
$field10->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'contractor_sunday';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(3)';
$field10->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_CONTRACTORS_SUNDAYALL';
$field11->name = 'contractor_sundayall';
$field11->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'contractor_sundayall';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(3)';
$field11->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_CONTRACTORS_SUNDAYSTART';
$field12->name = 'contractor_sundaystart';
$field12->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'contractor_sundaystart';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'TIME';
$field12->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field12);
 
$field16 = new Vtiger_Field();
$field16->label = 'LBL_CONTRACTORS_SUNDAYEND';
$field16->name = 'contractor_sundayend';
$field16->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'contractor_sundayend';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'TIME';
$field16->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field16);
 
$field17 = new Vtiger_Field();
$field17->label = 'LBL_CONTRACTORS_MONDAY';
$field17->name = 'contractor_monday';
$field17->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'contractor_monday';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(3)';
$field17->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_CONTRACTORS_MONDAYALL';
$field18->name = 'contractor_mondayall';
$field18->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'contractor_mondayall';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(3)';
$field18->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_CONTRACTORS_MONDAYSTART';
$field19->name = 'contractor_mondaystart';
$field19->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field19->column = 'contractor_mondaystart';   //  This will be the columnname in your database for the new field.
$field19->columntype = 'TIME';
$field19->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_CONTRACTORS_MONDAYEND';
$field20->name = 'contractor_mondayend';
$field20->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field20->column = 'contractor_mondayend';   //  This will be the columnname in your database for the new field.
$field20->columntype = 'TIME';
$field20->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field20);

$field21 = new Vtiger_Field();
$field21->label = 'LBL_CONTRACTORS_TUESDAY';
$field21->name = 'contractor_tuesday';
$field21->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field21->column = 'contractor_tuesday';   //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(3)';
$field21->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_CONTRACTORS_TUESDAYALL';
$field22->name = 'contractor_tuesdayall';
$field22->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field22->column = 'contractor_tuesdayall';   //  This will be the columnname in your database for the new field.
$field22->columntype = 'VARCHAR(3)';
$field22->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_CONTRACTORS_TUESDAYSTART';
$field23->name = 'contractor_tuesdaystart';
$field23->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field23->column = 'contractor_tuesdaystart';   //  This will be the columnname in your database for the new field.
$field23->columntype = 'TIME';
$field23->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field23->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field23);

 
$field24 = new Vtiger_Field();
$field24->label = 'LBL_CONTRACTORS_TUESDAYEND';
$field24->name = 'contractor_tuesdayend';
$field24->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field24->column = 'contractor_tuesdayend';   //  This will be the columnname in your database for the new field.
$field24->columntype = 'TIME';
$field24->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field24);
 
$field25 = new Vtiger_Field();
$field25->label = 'LBL_CONTRACTORS_WEDNESDAY';
$field25->name = 'contractor_wednesday';
$field25->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field25->column = 'contractor_wednesday';   //  This will be the columnname in your database for the new field.
$field25->columntype = 'VARCHAR(3)';
$field25->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field25->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field25);

$field26 = new Vtiger_Field();
$field26->label = 'LBL_CONTRACTORS_WEDNESDAYALL';
$field26->name = 'contractor_wednesdayall';
$field26->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field26->column = 'contractor_wednesdayall';   //  This will be the columnname in your database for the new field.
$field26->columntype = 'VARCHAR(3)';
$field26->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field26->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field26);

$field27 = new Vtiger_Field();
$field27->label = 'LBL_CONTRACTORS_WEDNESDAYSTART';
$field27->name = 'contractor_wednesdaystart';
$field27->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field27->column = 'contractor_wednesdaystart';   //  This will be the columnname in your database for the new field.
$field27->columntype = 'TIME';
$field27->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field27->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field27);

$field28 = new Vtiger_Field();
$field28->label = 'LBL_CONTRACTORS_WEDNESDAYEND';
$field28->name = 'contractor_wednesdayend';
$field28->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field28->column = 'contractor_wednesdayend';   //  This will be the columnname in your database for the new field.
$field28->columntype = 'TIME';
$field28->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field28->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'LBL_CONTRACTORS_THURSDAY';
$field29->name = 'contractor_thursday';
$field29->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field29->column = 'contractor_thursday';   //  This will be the columnname in your database for the new field.
$field29->columntype = 'VARCHAR(3)';
$field29->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field29->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'LBL_CONTRACTORS_THURSDAYALL';
$field30->name = 'contractor_thursdayall';
$field30->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field30->column = 'contractor_thursdayall';   //  This will be the columnname in your database for the new field.
$field30->columntype = 'VARCHAR(3)';
$field30->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field30->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field30);

$field31 = new Vtiger_Field();
$field31->label = 'LBL_CONTRACTORS_THURSDAYSTART';
$field31->name = 'contractor_thursdaystart';
$field31->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field31->column = 'contractor_thursdaystart';   //  This will be the columnname in your database for the new field.
$field31->columntype = 'TIME';
$field31->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field31->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field31);

$field32 = new Vtiger_Field();
$field32->label = 'LBL_CONTRACTORS_THURSDAYEND';
$field32->name = 'contractor_thursdayend';
$field32->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field32->column = 'contractor_thursdayend';   //  This will be the columnname in your database for the new field.
$field32->columntype = 'TIME';
$field32->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field32->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field32);

$field33 = new Vtiger_Field();
$field33->label = 'LBL_CONTRACTORS_FRIDAY';
$field33->name = 'contractor_friday';
$field33->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field33->column = 'contractor_friday';   //  This will be the columnname in your database for the new field.
$field33->columntype = 'VARCHAR(3)';
$field33->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field33->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field33);

$field34 = new Vtiger_Field();
$field34->label = 'LBL_CONTRACTORS_FRIDAYALL';
$field34->name = 'contractor_fridayall';
$field34->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field34->column = 'contractor_fridayall';   //  This will be the columnname in your database for the new field.
$field34->columntype = 'VARCHAR(3)';
$field34->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field34->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field34);

$field35 = new Vtiger_Field();
$field35->label = 'LBL_CONTRACTORS_FRIDAYSTART';
$field35->name = 'contractor_fridaystart';
$field35->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field35->column = 'contractor_fridaystart';   //  This will be the columnname in your database for the new field.
$field35->columntype = 'TIME';
$field35->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field35->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field35);

$field36 = new Vtiger_Field();
$field36->label = 'LBL_CONTRACTORS_FRIDAYEND';
$field36->name = 'contractor_fridayend';
$field36->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field36->column = 'contractor_fridayend';   //  This will be the columnname in your database for the new field.
$field36->columntype = 'TIME';
$field36->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field36->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field36);

$field37 = new Vtiger_Field();
$field37->label = 'LBL_CONTRACTORS_SATURDAY';
$field37->name = 'contractor_saturday';
$field37->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field37->column = 'contractor_saturday';   //  This will be the columnname in your database for the new field.
$field37->columntype = 'VARCHAR(3)';
$field37->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field37->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field37);

$field38 = new Vtiger_Field();
$field38->label = 'LBL_CONTRACTORS_SATURDAYALL';
$field38->name = 'contractor_saturdayall';
$field38->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field38->column = 'contractor_saturdayall';   //  This will be the columnname in your database for the new field.
$field38->columntype = 'VARCHAR(3)';
$field38->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field38->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field38);

$field39 = new Vtiger_Field();
$field39->label = 'LBL_CONTRACTORS_SATURDAYSTART';
$field39->name = 'contractor_saturdaystart';
$field39->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field39->column = 'contractor_saturdaystart';   //  This will be the columnname in your database for the new field.
$field39->columntype = 'TIME';
$field39->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field39->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field39);

$field40 = new Vtiger_Field();
$field40->label = 'LBL_CONTRACTORS_SATURDAYEND';
$field40->name = 'contractor_saturdayend';
$field40->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field40->column = 'contractor_saturdayend';   //  This will be the columnname in your database for the new field.
$field40->columntype = 'TIME';
$field40->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field40->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field40);

$block4->save($module);

$field41 = new Vtiger_Field();
$field41->label = 'LBL_CONTRACTORS_DLNUMBER';
$field41->name = 'contractor_dlnumber';
$field41->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field41->column = 'contractor_dlnumber';   //  This will be the columnname in your database for the new field.
$field41->columntype = 'VARCHAR(100)';
$field41->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field41->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block5->addField($field41);

$field42 = new Vtiger_Field();
$field42->label = 'LBL_CONTRACTORS_DLSTATE';
$field42->name = 'contractor_dlstate';
$field42->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field42->column = 'contractor_dlstate';   //  This will be the columnname in your database for the new field.
$field42->columntype = 'VARCHAR(2)';
$field42->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field42->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block5->addField($field42);

$field43 = new Vtiger_Field();
$field43->label = 'LBL_CONTRACTORS_DLEDATE';
$field43->name = 'contractor_dledate';
$field43->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field43->column = 'contractor_dledate';   //  This will be the columnname in your database for the new field.
$field43->columntype = 'DATE';
$field43->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field43->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block5->addField($field43);

$field44 = new Vtiger_Field();
$field44->label = 'LBL_CONTRACTORS_DLCLASS';
$field44->name = 'contractor_dlclass';
$field44->table = 'vtiger_contractors';  // This is the tablename from your database that the new field will be added to.
$field44->column = 'contractor_dlclass';   //  This will be the columnname in your database for the new field.
$field44->columntype = 'VARCHAR(100)';
$field44->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field44->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block5->addField($field44);

$block5->save($module);
