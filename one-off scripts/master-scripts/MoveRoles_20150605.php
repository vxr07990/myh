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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
$moveRolesIsNew = false;

$module1 = Vtiger_Module::getInstance('MoveRoles');
if ($module1) {
    echo "<h2>Updating MoveRoles Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'MoveRoles';
    $module1->save();
    echo "<h2>Creating Module MoveRoles and Updating Fields</h2><br>";
    $module1->initTables();
}

$block1 = Vtiger_Block::getInstance('LBL_MOVEROLES_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_MOVEROLES_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_MOVEROLES_INFORMATION';
    $module1->addBlock($block1);
    $moveRolesIsNew = true;
}
echo "<ul>";
$field1 = Vtiger_Field::getInstance('moveroles_role', $module1);
if ($field1) {
    echo "<li>The moveroles_role field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_MOVEROLES_ROLE';
    $field1->name = 'moveroles_role';
    $field1->table = 'vtiger_moveroles';
    $field1->column = 'moveroles_role';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Salesperson', 'Surveyor', 'Customer Service Cordinator', 'O/A Coordinator', 'D/A Coordinator', 'Packing', 'Contractor', 'Claims Rep', 'Billing Clerk'));
        
    $module1->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $module1);
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
$field3 = Vtiger_Field::getInstance('createdtime', $module1);
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
$field4 = Vtiger_Field::getInstance('modifiedtime', $module1);
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
$field5 = Vtiger_Field::getInstance('moveroles_employees', $module1);
if ($field5) {
    echo "<li>The moveroles_employees field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_MOVEROLES_EMPLOYEES';
    $field5->name = 'moveroles_employees';
    $field5->table = 'vtiger_moveroles';  // This is the tablename from your database that the new field will be added to.
    $field5->column = 'moveroles_employees';   //  This will be the columnname in your database for the new field.
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field5);
    $field5->setRelatedModules(array('Employees'));
}
$field6 = Vtiger_Field::getInstance('moveroles_orders', $module1);
if ($field6) {
    echo "<li>The moveroles_orders field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_MOVEROLES_ORDERS';
    $field6->name = 'moveroles_orders';
    $field6->table = 'vtiger_moveroles';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'moveroles_orders';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field6);
    $field6->setRelatedModules(array('Orders'));
}

echo "<li>Move role fields complete.</li><br>";

if ($moveRolesIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field1);
    
    $module1->setDefaultSharing();
    $module1->initWebservice();
}
echo "<li>moveroles if new cleanup complete.</li><br>";
//removed due to rearranging the database versioning
//START Add navigation link in module opportunities to orders
//$ordersInstance = Vtiger_Module::getInstance('Orders');
//$ordersInstance->setRelatedList(Vtiger_Module::getInstance('MoveRoles'), 'MoveRoles',Array('ADD'),'get_related_list');
//END Add navigation link in module
echo "<li>Navigation links complete</li><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";