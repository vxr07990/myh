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
$stdIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('ServicesToDispatch');
if ($moduleInstance) {
    echo "<h2>Updating ServicesToDispatch Fields</h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ServicesToDispatch';
    $moduleInstance->save();
    echo "<h2>Creating Module ServicesToDispatch and Updating Fields</h2><br>";
    $moduleInstance->initTables();
}
$blockInstance = Vtiger_Block::getInstance('LBL_SERVICESTODISPATCH_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_SERVICESTODISPATCH_INFORMATION block already exists</h3><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_SERVICESTODISPATCH_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
    $stdIsNew = true;
}
echo "<ul>";
$field1 = Vtiger_Field::getInstance('stdispatch_service', $moduleInstance);
if ($field1) {
    echo "<li>The stdispatch_service field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_STDISPATCH_SERVICE';
    $field1->name = 'stdispatch_service';
    $field1->table = 'vtiger_servicestodispatch';
    $field1->column = 'stdispatch_service';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
    $field1->setPicklistValues(array('Pack', 'Load', 'Deliver', 'Pack/Load', 'Pack/Load/Deliver', 'Unpack', 'Extra Pickup', 'Extra Delivery', 'Carton Delivery', 'Debris Pickup', 'Storage Delivery', 'Storage Access'));
}
$field3 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Assigned To';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';

    $blockInstance->addField($field3);
}
    /*
    $field4 = new Vtiger_Field();
    $field4->label = 'Created Time';
    $field4->name = 'CreatedTime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $blockInstance->addField($field4);

    $field5 = new Vtiger_Field();
    $field5->label = 'Modified Time';
    $field5->name = 'ModifiedTime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;

    $blockInstance->addField($field5);
    */
$field10 = Vtiger_Field::getInstance('stdispatch_numcrew', $moduleInstance);
if ($field10) {
    echo "<li>The stdispatch_numcrew field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_STDISPATCH_NUMCREW';
    $field10->name = 'stdispatch_numcrew';
    $field10->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'stdispatch_numcrew';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'INT(100)';
    $field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field10);
}
$field6 = Vtiger_Field::getInstance('stdispatch_ehours', $moduleInstance);
if ($field6) {
    echo "<li>The stdispatch_ehours field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_STDISPATCH_EHOURS';
    $field6->name = 'stdispatch_ehours';
    $field6->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'stdispatch_ehours';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'INT(100)';
    $field6->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field6);
}
$field7 = Vtiger_Field::getInstance('stdispatch_stop', $moduleInstance);
if ($field7) {
    echo "<li>The stdispatch_stop field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_STDISPATCH_STOP';
    $field7->name = 'stdispatch_stop';
    $field7->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'stdispatch_stop';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field7);
    $field7->setRelatedModules(array('Stops'));
}
$field11 = Vtiger_Field::getInstance('stdispatch_agent', $moduleInstance);
if ($field11) {
    echo "<li>The stdispatch_agent field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_STDISPATCH_AGENT';
    $field11->name = 'stdispatch_agent';
    $field11->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'stdispatch_agent';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'VARCHAR(255)';
    $field11->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field11);
    $field11->setRelatedModules(array('Agents'));
}
$field12 = Vtiger_Field::getInstance('stdispatch_datefrom', $moduleInstance);
if ($field12) {
    echo "<li>The stdispatch_datefrom field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_STDISPATCH_DATEFROM';
    $field12->name = 'stdispatch_datefrom';
    $field12->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field12->column = 'stdispatch_datefrom';   //  This will be the columnname in your database for the new field.
    $field12->columntype = 'DATE';
    $field12->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field12);
}
$field13 = Vtiger_Field::getInstance('stdispatch_dateto', $moduleInstance);
if ($field13) {
    echo "<li>The stdispatch_dateto field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_STDISPATCH_DATETO';
    $field13->name = 'stdispatch_dateto';
    $field13->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field13->column = 'stdispatch_dateto';   //  This will be the columnname in your database for the new field.
    $field13->columntype = 'DATE';
    $field13->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field13);
}
$field14 = Vtiger_Field::getInstance('stdispatch_pdate', $moduleInstance);
if ($field14) {
    echo "<li>The stdispatch_pdate field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_STDISPATCH_PDATE';
    $field14->name = 'stdispatch_pdate';
    $field14->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field14->column = 'stdispatch_pdate';   //  This will be the columnname in your database for the new field.
    $field14->columntype = 'DATE';
    $field14->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field14->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field14);
}
$field15 = Vtiger_Field::getInstance('stdispatch_name', $moduleInstance);
if ($field15) {
    echo "<li>The stdispatch_name field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_STDISPATCH_NAME';
    $field15->name = 'stdispatch_name';
    $field15->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field15->column = 'stdispatch_name';   //  This will be the columnname in your database for the new field.
    $field15->columntype = 'VARCHAR(255)';
    $field15->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field15);
    $moduleInstance->setEntityIdentifier($field15);
}
$field16 = Vtiger_Field::getInstance('description', $moduleInstance);
if ($field16) {
    echo "<li>The description field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_STDISPATCH_DESC';
    $field16->name = 'description';
    $field16->table = 'vtiger_crmentity';  // This is the tablename from your database that the new field will be added to.
    $field16->column = 'description';   //  This will be the columnname in your database for the new field.
    $field16->columntype = 'VARCHAR(100)';
    $field16->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field16->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance->addField($field16);
}
echo "</ul>";
$blockInstance->save($moduleInstance);

$blockInstance1 = Vtiger_Block::getInstance('LBL_SERVICESTODISPATCH_PATCHUPDATES', $moduleInstance);
if ($blockInstance1) {
    echo "<h3>The LBL_SERVICESTODISPATCH_PATCHUPDATES block already exists</h3><br>";
} else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_SERVICESTODISPATCH_PATCHUPDATES';
    $moduleInstance->addBlock($blockInstance1);
}
echo "<ul>";
$field17 = Vtiger_Field::getInstance('stdispatch_dstatus', $moduleInstance);
if ($field17) {
    echo "<li>The stdispatch_dstatus field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_STDISPATCH_DSTATUS';
    $field17->name = 'stdispatch_dstatus';
    $field17->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field17->column = 'stdispatch_dstatus';   //  This will be the columnname in your database for the new field.
    $field17->columntype = 'VARCHAR(225)';
    $field17->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field17->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance1->addField($field17);
}
$field18 = Vtiger_Field::getInstance('stdispatch_assdate', $moduleInstance);
if ($field18) {
    echo "<li>The stdispatch_assdate field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_STDISPATCH_ASSDATE';
    $field18->name = 'stdispatch_assdate';
    $field18->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field18->column = 'stdispatch_assdate';   //  This will be the columnname in your database for the new field.
    $field18->columntype = 'DATE';
    $field18->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field18->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance1->addField($field18);
}
$field19 = Vtiger_Field::getInstance('stdispatch_acmembers', $moduleInstance);
if ($field19) {
    echo "<li>The stdispatch_acmembers field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_STDISPATCH_ACMEMBERS';
    $field19->name = 'stdispatch_acmembers';
    $field19->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field19->column = 'stdispatch_acmembers';   //  This will be the columnname in your database for the new field.
    $field19->columntype = 'VARCHAR(225)';
    $field19->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field19->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance1->addField($field19);
}
$field20 = Vtiger_Field::getInstance('stdispatch_adate', $moduleInstance);
if ($field20) {
    echo "<li>The stdispatch_adate field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_STDISPATCH_ACTUALD';
    $field20->name = 'stdispatch_adate';
    $field20->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field20->column = 'stdispatch_adate';   //  This will be the columnname in your database for the new field.
    $field20->columntype = 'DATE';
    $field20->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field20->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance1->addField($field20);
}
$field21 = Vtiger_Field::getInstance('stdispatch_actcmembers', $moduleInstance);
if ($field21) {
    echo "<li>The stdispatch_actcmembers field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_STDISPATCH_ACTCMEMBERS';
    $field21->name = 'stdispatch_actcmembers';
    $field21->table = 'vtiger_servicestodispatch';  // This is the tablename from your database that the new field will be added to.
    $field21->column = 'stdispatch_actcmembers';   //  This will be the columnname in your database for the new field.
    $field21->columntype = 'VARCHAR(225)';
    $field21->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $blockInstance1->addField($field21);
}
echo "</ul>";
$blockInstance1->save($moduleInstance);


if ($stdIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field15)->addField($field10, 1)->addField($field12, 2);

    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}
