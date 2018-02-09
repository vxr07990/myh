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
    include_once('modules/ModTracker/ModTracker.php');
    include_once('modules/ModComments/ModComments.php');
    include_once 'includes/main/WebUI.php';
    include_once 'include/Webservices/Create.php';
    include_once 'modules/Users/Users.php';

$isNew = false;

$moduleInstance = Vtiger_Module::getInstance('Containers');

if ($moduleInstance) {
    echo "<h2>Containers already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Containers';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_CONTAINERS_DETAILS', $moduleInstance);

if ($blockInstance) {
    echo "<h3>The LBL_CONTAINERS_DETAILS block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_CONTAINERS_DETAILS';
    $moduleInstance->addBlock($blockInstance);
    $isNew = true;
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CONTAINERS_DIMENSIONS', $moduleInstance);

if ($blockInstance2) {
    echo "<h3>The LBL_CONTAINERS_DIMENSIONS block already exists</h3><br> \n";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CONTAINERS_DIMENSIONS';
    $moduleInstance->addBlock($blockInstance2);
}

$blockInstance3 = Vtiger_Block::getInstance('LBL_CONTAINERS_WEIGHTS', $moduleInstance);

if ($blockInstance3) {
    echo "<h3>The LBL_CONTAINERS_WEIGHTS block already exists</h3><br> \n";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_CONTAINERS_WEIGHTS';
    $moduleInstance->addBlock($blockInstance3);
}

$blockInstance4 = Vtiger_Block::getInstance('LBL_CONTAINERS_COSTS', $moduleInstance);

if ($blockInstance4) {
    echo "<h3>The LBL_CONTAINERS_COSTS block already exists</h3><br> \n";
} else {
    $blockInstance4 = new Vtiger_Block();
    $blockInstance4->label = 'LBL_CONTAINERS_COSTS';
    $moduleInstance->addBlock($blockInstance4);
}

$blockInstance5 = Vtiger_Block::getInstance('LBL_CONTAINERS_WHINFO', $moduleInstance);

if ($blockInstance5) {
    echo "<h3>The LBL_CONTAINERS_WHINFO block already exists</h3><br> \n";
} else {
    $blockInstance5 = new Vtiger_Block();
    $blockInstance5->label = 'LBL_CONTAINERS_WHINFO';
    $moduleInstance->addBlock($blockInstance5);
}

$blockInstance6 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);

if ($blockInstance6) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance6 = new Vtiger_Block();
    $blockInstance6->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance6);
}

//Name Field - related to ContainerTypes
$field1 = Vtiger_Field::getInstance('containers_containertypes', $moduleInstance);
if ($field1) {
    echo "<br> The containers_containertypes field already exists in Containers <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CONTAINERS_CONTAINERTYPES';
    $field1->name = 'containers_containertypes';
    $field1->table = 'vtiger_containers';
    $field1->column ='containers_containertypes';
    $field1->columntype = 'varchar(255)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = '1';

    $blockInstance->addField($field1);

    $tempModule = Vtiger_Module::getInstance('ContainerTypes');
    if ($tempModule) {
        $field1->setRelatedModules(array('ContainerTypes'));
    } else {
        echo "<h2>Unable to set Related to ContainerTypes as ContainerTypes does not exist</h2><br>";
    }
}

//Orders Field - related to Orders
$field0 = Vtiger_Field::getInstance('containers_orders', $moduleInstance);
if ($field0) {
    echo "<br> The containers_orders field already exists in Containers <br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_CONTAINERS_ORDERS';
    $field0->name = 'containers_orders';
    $field0->table = 'vtiger_containers';
    $field0->column ='containers_orders';
    $field0->columntype = 'varchar(255)';
    $field0->uitype = 10;
    $field0->typeofdata = 'V~M';

    $blockInstance->addField($field0);

    $tempModule = Vtiger_Module::getInstance('Orders');
    if ($tempModule) {
        $field0->setRelatedModules(array('Orders'));
    } else {
        echo "<h2>Unable to set Related to Orders as Orders does not exist</h2><br>";
    }
}

//Owner Field
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($field2) {
    echo "<br> The agentid field already exists in Containers <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'Owner';
    $field2->name       = 'agentid';
    $field2->table      = 'vtiger_crmentity';
    $field2->column     = 'agentid';
    $field2->columntype = 'INT(10)';
    $field2->uitype     = 1002;
    $field2->typeofdata = 'I~M';

    $blockInstance->addField($field2);
}

//Description Field
$field3 = Vtiger_Field::getInstance('containers_desc', $moduleInstance);
if ($field3) {
    echo "<br> The containers_desc field already exists in Containers <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CONTAINERS_DESC';
    $field3->name = 'containers_desc';
    $field3->table = 'vtiger_containers';
    $field3->column ='containers_desc';
    $field3->columntype = 'varchar(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $field3->summaryfield = '1';

    $blockInstance->addField($field3);
    $moduleInstance->setEntityIdentifier($field3);
}

//Container ID
$field4 = Vtiger_Field::getInstance('containers_id', $moduleInstance);
if ($field4) {
    echo "<br> The containers_id field already exists in Containers <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CONTAINERS_ID';
    $field4->name = 'containers_id';
    $field4->table = 'vtiger_containers';
    $field4->column ='containers_id';
    $field4->columntype = 'varchar(255)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';
    $field4->summaryfield = '1';

    $blockInstance->addField($field4);
}

// Content Field
$field5 = Vtiger_Field::getInstance('containers_content', $moduleInstance);
if ($field5) {
    echo "<br> The containers_content field already exists in Containers <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CONTAINERS_CONTENT';
    $field5->name = 'containers_content';
    $field5->table = 'vtiger_containers';
    $field5->column ='containers_content';
    $field5->columntype = 'varchar(255)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $field5->summaryfield = '1';

    $blockInstance->addField($field5);
}

// Seal Number
$field6 = Vtiger_Field::getInstance('containers_sealnum', $moduleInstance);
if ($field6) {
    echo "<br> The containers_sealnum field already exists in Containers <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CONTAINERS_SEALNUM';
    $field6->name = 'containers_sealnum';
    $field6->table = 'vtiger_containers';
    $field6->column ='containers_sealnum';
    $field6->columntype = 'varchar(255)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';
    $field6->summaryfield = '1';

    $blockInstance->addField($field6);
}

// Container Supplier
$field7 = Vtiger_Field::getInstance('containers_supplies', $moduleInstance);
if ($field7) {
    echo "<br> The containers_supplier field already exists in Containers <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CONTAINERS_SUPPLIER';
    $field7->name = 'containers_supplier';
    $field7->table = 'vtiger_containers';
    $field7->column ='containers_supplier';
    $field7->columntype = 'varchar(255)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';

    $blockInstance->addField($field7);
}

//Length Field
$field8 = Vtiger_Field::getInstance('containers_length', $moduleInstance);
if ($field8) {
    echo "<br> The containers_length field already exists in Containers <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CONTAINERS_LENGTH';
    $field8->name = 'containers_length';
    $field8->table = 'vtiger_containers';
    $field8->column ='containers_length';
    $field8->columntype = 'INT(10)';
    $field8->uitype = 7;
    $field8->typeofdata = 'I~O';

    $blockInstance2->addField($field8);
}

//Width Field
$field9 = Vtiger_Field::getInstance('containers_width', $moduleInstance);
if ($field9) {
    echo "<br> The containers_width field already exists in Containers <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CONTAINERS_WIDTH';
    $field9->name = 'containers_width';
    $field9->table = 'vtiger_containers';
    $field9->column ='containers_width';
    $field9->columntype = 'INT(10)';
    $field9->uitype = 7;
    $field9->typeofdata = 'I~O';

    $blockInstance2->addField($field9);
}

//Height Field
$field10 = Vtiger_Field::getInstance('containers_height', $moduleInstance);
if ($field10) {
    echo "<br> The containers_height field already exists in Containers <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CONTAINERS_HEIGHT';
    $field10->name = 'containers_height';
    $field10->table = 'vtiger_containers';
    $field10->column ='containers_height';
    $field10->columntype = 'INT(10)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';

    $blockInstance2->addField($field10);
}

//Cubic Ft Field
$field11 = Vtiger_Field::getInstance('containers_cuft', $moduleInstance);
if ($field11) {
    echo "<br> The containers_cuft field already exists in Containers <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CONTAINERS_CUFT';
    $field11->name = 'containers_cuft';
    $field11->table = 'vtiger_containers';
    $field11->column ='containers_cuft';
    $field11->columntype = 'decimal(5,2)';
    $field11->uitype = 7;
    $field11->typeofdata = 'N~O';

    $blockInstance2->addField($field11);
}

//Gross Weight Field
$field12 = Vtiger_Field::getInstance('containers_grosswt', $moduleInstance);
if ($field12) {
    echo "<br> The containers_grosswt field already exists in Containers <br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CONTAINERS_GROSSWT';
    $field12->name = 'containers_grosswt';
    $field12->table = 'vtiger_containers';
    $field12->column ='containers_grosswt';
    $field12->columntype = 'INT(10)';
    $field12->uitype = 7;
    $field12->typeofdata = 'I~O';

    $blockInstance3->addField($field12);
}

//Tare Weight Field
$field13 = Vtiger_Field::getInstance('containers_tarewt', $moduleInstance);
if ($field13) {
    echo "<br> The containers_tarewt field already exists in Containers <br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_CONTAINERS_TAREWT';
    $field13->name = 'containers_tarewt';
    $field13->table = 'vtiger_containers';
    $field13->column ='containers_tarewt';
    $field13->columntype = 'INT(10)';
    $field13->uitype = 7;
    $field13->typeofdata = 'I~O';

    $blockInstance3->addField($field13);
}

//Net Weight Field
$field14 = Vtiger_Field::getInstance('containers_netwt', $moduleInstance);
if ($field14) {
    echo "<br> The containers_netwt field already exists in Containers <br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_CONTAINERS_NETWT';
    $field14->name = 'containers_netwt';
    $field14->table = 'vtiger_containers';
    $field14->column ='containers_netwt';
    $field14->columntype = 'INT(10)';
    $field14->uitype = 7;
    $field14->typeofdata = 'I~O';

    $blockInstance3->addField($field14);
}

//Desnity Field
$field15 = Vtiger_Field::getInstance('containers_density', $moduleInstance);
if ($field15) {
    echo "<br> The containers_density field already exists in Containers <br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_CONTAINERS_DENSITY';
    $field15->name = 'containers_density';
    $field15->table = 'vtiger_containers';
    $field15->column ='containers_density';
    $field15->columntype = 'decimal(5,2)';
    $field15->uitype = 7;
    $field15->typeofdata = 'N~O';

    $blockInstance3->addField($field15);
}

//Bill for Container Cost Field
$field16 = Vtiger_Field::getInstance('containers_billcontcost', $moduleInstance);
if ($field16) {
    echo "<br> The containers_billcontcost field already exists in Containers <br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_CONTAINERS_BILLCONTCOST';
    $field16->name = 'containers_billcontcost';
    $field16->table = 'vtiger_containers';
    $field16->column ='containers_billcontcost';
    $field16->columntype = 'varchar(3)';
    $field16->uitype = 56;
    $field16->typeofdata = 'C~O';

    $blockInstance4->addField($field16);
}

//Container Cost Field
$field17 = Vtiger_Field::getInstance('containers_contcost', $moduleInstance);
if ($field17) {
    echo "<br> The containers_contcost field already exists in Containers <br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_CONTAINERS_CONTCOST';
    $field17->name = 'containers_contcost';
    $field17->table = 'vtiger_containers';
    $field17->column ='containers_contcost';
    $field17->columntype = 'DECIMAL(13,2)';
    $field17->uitype = 71;
    $field17->typeofdata = 'N~O';

    $blockInstance4->addField($field17);
}

//Bill for Seal Cost Field
$field18 = Vtiger_Field::getInstance('containers_billsealcost', $moduleInstance);
if ($field18) {
    echo "<br> The containers_billsealcost field already exists in Containers <br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_CONTAINERS_BILLSEALCOST';
    $field18->name = 'containers_billsealcost';
    $field18->table = 'vtiger_containers';
    $field18->column ='containers_billsealcost';
    $field18->columntype = 'varchar(3)';
    $field18->uitype = 56;
    $field18->typeofdata = 'C~O';

    $blockInstance4->addField($field18);
}

//Seal Cost Field
$field19 = Vtiger_Field::getInstance('containers_sealcost', $moduleInstance);
if ($field19) {
    echo "<br> The containers_sealcost field already exists in Containers <br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_CONTAINERS_SEALCOST';
    $field19->name = 'containers_sealcost';
    $field19->table = 'vtiger_containers';
    $field19->column ='containers_sealcost';
    $field19->columntype = 'DECIMAL(13,2)';
    $field19->uitype = 71;
    $field19->typeofdata = 'N~O~10,2';

    $blockInstance4->addField($field19);
}

//Bill for Repair Cost Field
$field20 = Vtiger_Field::getInstance('containers_billrepaircost', $moduleInstance);
if ($field20) {
    echo "<br> The containers_billrepaircost field already exists in Containers <br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_CONTAINERS_BILLREPAIRCOST';
    $field20->name = 'containers_billrepaircost';
    $field20->table = 'vtiger_containers';
    $field20->column ='containers_billrepaircost';
    $field20->columntype = 'varchar(3)';
    $field20->uitype = 56;
    $field20->typeofdata = 'C~O';

    $blockInstance4->addField($field20);
}

//Repair / Recoupt Cost Field
$field21 = Vtiger_Field::getInstance('containers_repaircost', $moduleInstance);
if ($field21) {
    echo "<br> The containers_repaircost field already exists in Employee_Roles <br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_CONTAINERS_REPAIRCOST';
    $field21->name = 'containers_repaircost';
    $field21->table = 'vtiger_containers';
    $field21->column ='containers_repaircost';
    $field21->columntype = 'DECIMAL(13,2)';
    $field21->uitype = 71;
    $field21->typeofdata = 'N~O';

    $blockInstance4->addField($field21);
}

//W/H Date In
$field22 = Vtiger_Field::getInstance('containers_whdatein', $moduleInstance);
if ($field22) {
    echo "<li>The containers_whdatein field already exists in Containers </li><br> \n";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_CONTAINERS_WHDATEIN';
    $field22->name = 'containers_whdatein';
    $field22->table = 'vtiger_containers';
    $field22->column = 'containers_whdatein';
    $field22->columntype = 'date';
    $field22->uitype = 5;
    $field22->typeofdata = 'D~O';

    $blockInstance5->addField($field22);
}

//W/H Date Out
$field23 = Vtiger_Field::getInstance('containers_whdateout', $moduleInstance);
if ($field23) {
    echo "<li>The containers_whdateout field already exists in Containers </li><br> \n";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_CONTAINERS_WHDATEOUT';
    $field23->name = 'containers_whdateout';
    $field23->table = 'vtiger_containers';
    $field23->column = 'containers_whdateout';
    $field23->columntype = 'date';
    $field23->uitype = 5;
    $field23->typeofdata = 'D~O';

    $blockInstance5->addField($field23);
}

//W/H Location
$field24 = Vtiger_Field::getInstance('containers_whlocation', $moduleInstance);
if ($field24) {
    echo "<li>The containers_whlocation field already exists in Containers </li><br> \n";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_CONTAINERS_WHLOCATION';
    $field24->name = 'containers_whlocation';
    $field24->table = 'vtiger_containers';
    $field24->column = 'containers_whlocation';
    $field24->columntype = 'varchar(255)';
    $field24->uitype = 1;
    $field24->typeofdata = 'V~O';

    $blockInstance5->addField($field24);
}

//W/H Employees
$field25 = Vtiger_Field::getInstance('containers_whemps', $moduleInstance);
if ($field25) {
    echo "<li>The containers_whemps field already exists in Containers </li><br> \n";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CONTAINERS_WHEMPS';
    $field25->name = 'containers_whemps';
    $field25->table = 'vtiger_containers';
    $field25->column = 'containers_whemps';
    $field25->columntype = 'varchar(255)';
    $field25->uitype = 1;
    $field25->typeofdata = 'V~O';

    $blockInstance5->addField($field25);
}

//W/H Notes
$field25 = Vtiger_Field::getInstance('containers_whnotes', $moduleInstance);
if ($field25) {
    echo "<li>The containers_whnotes field already exists in Containers </li><br> \n";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CONTAINERS_WHNOTES';
    $field25->name = 'containers_whnotes';
    $field25->table = 'vtiger_containers';
    $field25->column = 'containers_whnotes';
    $field25->columntype = 'text';
    $field25->uitype = 21;
    $field25->typeofdata = 'V~O';

    $blockInstance5->addField($field25);
}

//Date Created
$field26 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field26) {
    echo "<li>The createdtime field already exists in Containers </li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CONTAINERS_CREATEDTIME';
    $field26->name = 'createdtime';
    $field26->table = 'vtiger_crmentity';
    $field26->column = 'createdtime';
    $field26->uitype = 70;
    $field26->typeofdata = 'T~O';
    $field26->displaytype = 2;

    $blockInstance6->addField($field26);
}

//Date Modified
$field27 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field27) {
    echo "<li>The modifiedtime field already exists in Containers </li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_CONTAINERS_MODIFIEDTIME';
    $field27->name = 'modifiedtime';
    $field27->table = 'vtiger_crmentity';
    $field27->column = 'modifiedtime';
    $field27->uitype = 70;
    $field27->typeofdata = 'T~O';
    $field27->displaytype = 2;

    $blockInstance6->addField($field27);
}

//Created By
$field28 = Vtiger_Field::getInstance('createdby', $moduleInstance);
if ($field28) {
    echo "<li>The createdby field already exists in Containers </li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CONTAINERS_CREATEDBY';
    $field28->name = 'createdby';
    $field28->table = 'vtiger_crmentity';
    $field28->column = 'smcreatorid';
    $field28->uitype = 52;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 2;

    $blockInstance6->addField($field28);
}

//Assigned To
$field29 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field29) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_CONTAINERS_ASSIGNED_TO';
    $field29->name = 'assigned_user_id';
    $field29->table = 'vtiger_crmentity';
    $field29->column = 'smownerid';
    $field29->uitype = 53;
    $field29->typeofdata = 'V~M';
    $field29->displaytype = 2;

    $blockInstance6->addField($field29);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field3)->addField($field1, 1)->addField($field4, 2)->addfield($field5, 3)->addfield($field6, 4)->addfield($field0, 5);

    $moduleInstance->setDefaultSharing();

    $moduleInstance->initWebservice();

    //Add Containers to Orders
    $modulerel = Vtiger_Module::getInstance('Orders');
    $modulerel->setRelatedList(Vtiger_Module::getInstance('Containers'), 'Containers', array('ADD', 'SELECT'), 'get_related_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";