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


/*
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');*/

$ordersTaskInstance = Vtiger_Module::getInstance('OrdersTask');

    if ($ordersTaskInstance) {
        echo "<br> module 'OrdersTask' already exists. <br>";
    } else {
        $ordersTaskInstance = new Vtiger_Module();
        $ordersTaskInstance->name = 'OrdersTask';
        $ordersTaskInstance->save();
        $ordersTaskInstance->initTables();
        $ordersTaskInstance->setDefaultSharing();
        $ordersTaskInstance->initWebservice();
        ModTracker::enableTrackingForModule($ordersTaskInstance->id);
    }

$ordersMilestoneInstance = Vtiger_Module::getInstance('OrdersMilestone');
    if ($ordersMilestoneInstance) {
        echo "<br> module 'OrdersMilestone' already exists. <br>";
    } else {
        $ordersMilestoneInstance = new Vtiger_Module();
        $ordersMilestoneInstance->name = 'OrdersMilestone';
        $ordersMilestoneInstance->save();
        $ordersMilestoneInstance->initTables();
        $ordersMilestoneInstance->setDefaultSharing();
        $ordersMilestoneInstance->initWebservice();
        ModTracker::enableTrackingForModule($ordersMilestoneInstance->id);
    }

$ordersInstance =Vtiger_Module::getInstance('Orders');
    if ($ordersInstance) {
        echo "<br> module 'Orders' already exists. <br>";
    } else {
        $ordersInstance = new Vtiger_Module();
        $ordersInstance->name = 'Orders';
        $ordersInstance->save();
        $ordersInstance->initTables();
        $ordersInstance->setDefaultSharing();
        $ordersInstance->initWebservice();
        ModTracker::enableTrackingForModule($ordersInstance->id);
    }
        
$lddInstance = Vtiger_Module::getInstance('LongDistanceDispatch');
if ($lddInstance || file_exists('modules/LongDistanceDispatch')) {
    echo "Module already present";
} else {
    // Extension Module. No need to initTables/Webservices

   $lddInstance = new Vtiger_Module();
    $lddInstance->name = 'LongDistanceDispatch';
    $lddInstance->save();
    $lddInstance->setDefaultSharing();
}

$ordersTaskblockInstance1 = Vtiger_Block::getInstance('LBL_ORDERS_TASK_INFORMATION', $ordersTaskInstance);
    if ($ordersTaskblockInstance1) {
        echo "<br> block 'LBL_ORDERS_TASK_INFORMATION' already exists.<br>";
    } else {
        $ordersTaskblockInstance1 = new Vtiger_Block();
        $ordersTaskblockInstance1->label = 'LBL_ORDERS_TASK_INFORMATION';
        $ordersTaskInstance->addBlock($ordersTaskblockInstance1);
    }

$ordersTaskblockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $ordersTaskInstance);
    if ($ordersTaskblockInstance2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $ordersTaskblockInstance2 = new Vtiger_Block();
        $ordersTaskblockInstance2->label = 'LBL_CUSTOM_INFORMATION';
        $ordersTaskInstance->addBlock($ordersTaskblockInstance2);
    }

$ordersTaskblockInstance3 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $ordersTaskInstance);
    if ($ordersTaskblockInstance3) {
        echo "<br> block 'LBL_DESCRIPTION_INFORMATION' already exists.<br>";
    } else {
        $ordersTaskblockInstance3 = new Vtiger_Block();
        $ordersTaskblockInstance3->label = 'LBL_DESCRIPTION_INFORMATION';
        $ordersTaskInstance->addBlock($ordersTaskblockInstance3);
    }

$ordersTaskblockInstance4 = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $ordersTaskInstance);
    if ($ordersTaskblockInstance4) {
        echo "<br> block 'LBL_OPERATIVE_TASK_INFORMATION' already exists.<br>";
    } else {
        $ordersTaskblockInstance4 = new Vtiger_Block();
        $ordersTaskblockInstance4->label = 'LBL_OPERATIVE_TASK_INFORMATION';
        $ordersTaskInstance->addBlock($ordersTaskblockInstance4);
    }

$ordersTaskblockInstance5 = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $ordersTaskInstance);
    if ($ordersTaskblockInstance5) {
        echo "<br> block 'LBL_DISPATCH_UPDATES' already exists.<br>";
    } else {
        $ordersTaskblockInstance5 = new Vtiger_Block();
        $ordersTaskblockInstance5->label = 'LBL_DISPATCH_UPDATES';
        $ordersTaskInstance->addBlock($ordersTaskblockInstance5);
    }


$ordersMilestoneblockInstance1 = Vtiger_Block::getInstance('LBL_ORDERS_MILESTONE_INFORMATION', $ordersMilestoneInstance);
    if ($ordersMilestoneblockInstance1) {
        echo "<br> block 'LBL_ORDERS_MILESTONE_INFORMATION' already exists.<br>";
    } else {
        $ordersMilestoneblockInstance1 = new Vtiger_Block();
        $ordersMilestoneblockInstance1->label = 'LBL_ORDERS_MILESTONE_INFORMATION';
        $ordersMilestoneInstance->addBlock($ordersMilestoneblockInstance1);
    }

$ordersMilestoneblockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $ordersMilestoneInstance);
    if ($ordersMilestoneblockInstance2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $ordersMilestoneblockInstance2 = new Vtiger_Block();
        $ordersMilestoneblockInstance2->label = 'LBL_CUSTOM_INFORMATION';
        $ordersMilestoneInstance->addBlock($ordersMilestoneblockInstance2);
    }

$ordersMilestoneblockInstance3 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $ordersMilestoneInstance);
    if ($ordersMilestoneblockInstance3) {
        echo "<br> block 'LBL_DESCRIPTION_INFORMATION' already exists.<br>";
    } else {
        $ordersMilestoneblockInstance3 = new Vtiger_Block();
        $ordersMilestoneblockInstance3->label = 'LBL_DESCRIPTION_INFORMATION';
        $ordersMilestoneInstance->addBlock($ordersMilestoneblockInstance3);
    }

$ordersblockInstance1 = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersInstance);
    if ($ordersblockInstance1) {
        echo "<br> block 'LBL_ORDERS_INFORMATION' already exists.<br>";
    } else {
        $ordersblockInstance1 = new Vtiger_Block();
        $ordersblockInstance1->label = 'LBL_ORDERS_INFORMATION';
        $ordersInstance->addBlock($ordersblockInstance1);
    }

$ordersblockInstance2 = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS', $ordersInstance);
    if ($ordersblockInstance2) {
        echo "<br> block 'LBL_ORDERS_ORIGINADDRESS' already exists.<br>";
    } else {
        $ordersblockInstance2 = new Vtiger_Block();
        $ordersblockInstance2->label = 'LBL_ORDERS_ORIGINADDRESS';
        $ordersInstance->addBlock($ordersblockInstance2);
    }

$ordersblockInstance3 = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $ordersInstance);
    if ($ordersblockInstance3) {
        echo "<br> block 'LBL_ORDERS_INVOICE' already exists.<br>";
    } else {
        $ordersblockInstance3 = new Vtiger_Block();
        $ordersblockInstance3->label = 'LBL_ORDERS_INVOICE';
        $ordersInstance->addBlock($ordersblockInstance3);
    }

$ordersblockInstance4 = Vtiger_Block::getInstance('LBL_ORDERS_DATES', $ordersInstance);
    if ($ordersblockInstance4) {
        echo "<br> block 'LBL_ORDERS_DATES' already exists.<br>";
    } else {
        $ordersblockInstance4 = new Vtiger_Block();
        $ordersblockInstance4->label = 'LBL_ORDERS_DATES';
        $ordersInstance->addBlock($ordersblockInstance4);
    }

$ordersblockInstance5 = Vtiger_Block::getInstance('LBL_ORDERS_WEIGHTS', $ordersInstance);
    if ($ordersblockInstance5) {
        echo "<br> block 'LBL_ORDERS_WEIGHTS' already exists.<br>";
    } else {
        $ordersblockInstance5 = new Vtiger_Block();
        $ordersblockInstance5->label = 'LBL_ORDERS_WEIGHTS';
        $ordersInstance->addBlock($ordersblockInstance5);
    }

$ordersblockInstance6 = Vtiger_Block::getInstance('LBL_ORDERS_DESCRIPTION', $ordersInstance);
    if ($ordersblockInstance6) {
        echo "<br> block 'LBL_ORDERS_DESCRIPTION' already exists.<br>";
    } else {
        $ordersblockInstance6 = new Vtiger_Block();
        $ordersblockInstance6->label = 'LBL_ORDERS_DESCRIPTION';
        $ordersInstance->addBlock($ordersblockInstance6);
    }

$ordersblockInstance7 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $ordersInstance);
    if ($ordersblockInstance7) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $ordersblockInstance7 = new Vtiger_Block();
        $ordersblockInstance7->label = 'LBL_CUSTOM_INFORMATION';
        $ordersInstance->addBlock($ordersblockInstance7);
    }

$ordersblock5 = Vtiger_Block::getInstance('LBL_ORDERS_PARTICIPANTS', $ordersInstance);
if ($ordersblock5) {
    echo "<li>The LBL_ORDERS_PARTICIPANTS field already exists</li><br>";
} else {
    $ordersblock5 = new Vtiger_Block();
    $ordersblock5->label = 'LBL_ORDERS_PARTICIPANTS';
    $ordersblock5->sequence = 1;
    $ordersInstance->addBlock($ordersblock5);
}

if (!Vtiger_Utils::CheckTable('vtiger_orders_participatingagents')) {
    echo "<li>creating vtiger_orders_participatingagents </li><br>";
    Vtiger_Utils::CreateTable('vtiger_orders_participatingagents',
                              '(ordersid INT(10),
								agentid INT(10),
                                agenttype INT(15),
								permissions TINYINT(4),
								participantid VARCHAR(150)
                               )', true);
}

//add orderStask fields
$field1 = Vtiger_Field::getInstance('orderstaskname', $ordersTaskInstance);
    if ($field1) {
        echo "<br> Field 'orderstaskname' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'Orders Task Name';
        $field1->name = 'orderstaskname';
        $field1->table = 'vtiger_orderstask';
        $field1->column = 'orderstaskname';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 2;
        $field1->typeofdata = 'V~M';
        $field1->quickcreate = 0;
        $field1->summaryfield = 1;

        $ordersTaskblockInstance1->addField($field1);
        
        $ordersTaskInstance->setEntityIdentifier($field1);
    }

$field2 = Vtiger_Field::getInstance('orderstasktype', $ordersTaskInstance);
    if ($field2) {
        echo "<br> Field 'orderstasktype' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Type';
        $field2->name = 'orderstasktype';
        $field2->table = 'vtiger_orderstask';
        $field2->column = 'orderstasktype';
        $field2->columntype = 'VARCHAR(100)';
        $field2->uitype = 16;
        $field2->typeofdata = 'V~O';
        $field2->summaryfield = 1;

        $ordersTaskblockInstance1->addField($field2);
        $field2->setPicklistValues(array('--none--', 'administrative', 'operative', 'other'));
    }

$field3 = Vtiger_Field::getInstance('orderstaskpriority', $ordersTaskInstance);
    if ($field3) {
        echo "<br> Field 'orderstaskpriority' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'Priority';
        $field3->name = 'orderstaskpriority';
        $field3->table = 'vtiger_orderstask';
        $field3->column = 'orderstaskpriority';
        $field3->columntype = 'VARCHAR(100)';
        $field3->uitype = 16;
        $field3->typeofdata = 'V~O';
        $field3->summaryfield = 1;
        $ordersTaskblockInstance1->addField($field3);
        $field3->setPicklistValues(array('--none--', 'low', 'normal', 'high'));
    }

$field4 = Vtiger_Field::getInstance('ordersid', $ordersTaskInstance);
    if ($field4) {
        echo "<br> Field 'ordersid' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'Related to';
        $field4->name = 'ordersid';
        $field4->table = 'vtiger_orderstask';
        $field4->column = 'ordersid';
        $field4->columntype = 'VARCHAR(100)';
        $field4->uitype = 10;
        $field4->typeofdata = 'V~M';
        $field4->presence = 0;
        $field4->quickcreate = 0;
        $field4->summaryfield = 1;
        $ordersTaskblockInstance1->addField($field4);
        $field4->setRelatedModules(array('Orders'));
    }

$field5 = Vtiger_Field::getInstance('assigned_user_id', $ordersTaskInstance);
    if ($field5) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'Assigned To';
        $field5->name = 'assigned_user_id';
        $field5->table = 'vtiger_crmentity';
        $field5->column = 'smownerid';
        $field5->uitype = 53;
        $field5->typeofdata = 'V~M';
        $field5->quickcreate = 0;
    
        $ordersTaskblockInstance1->addField($field5);
    }

$field6 = Vtiger_Field::getInstance('orderstasknumber', $ordersTaskInstance);
    if ($field6) {
        echo "<br> Field 'orderstasknumber' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'Orders Task Number';
        $field6->name = 'orderstasknumber';
        $field6->table = 'vtiger_orderstask';
        $field6->column = 'orderstasknumber';
        $field6->columntype = 'INT(11)';
        $field6->uitype = 7;
        $field6->typeofdata = 'I~O';

        $ordersTaskblockInstance1->addField($field6);
    }

$field7 = Vtiger_Field::getInstance('orderstask_no', $ordersTaskInstance);
    if ($field7) {
        echo "<br> Field 'orderstask_no' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'Orders Task No';
        $field7->name = 'orderstask_no';
        $field7->table = 'vtiger_orderstask';
        $field7->column = 'orderstask_no';
        $field7->columntype = 'VARCHAR(100)';
        $field7->uitype = 4;
        $field7->typeofdata = 'V~O';
        $field7->quickcreate =3;
        $field7->presence = 0;

        $ordersTaskblockInstance1->addField($field7);
        $entity = new CRMEntity();
        $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'ORTSK', 1);
    }

$field8 = Vtiger_Field::getInstance('orderstaskprogress', $ordersTaskInstance);
    if ($field8) {
        echo "<br> Field 'orderstaskprogress' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'Progress';
        $field8->name = 'orderstaskprogress';
        $field8->table = 'vtiger_orderstask';
        $field8->column = 'orderstaskprogress';
        $field8->columntype = 'VARCHAR(100)';
        $field8->uitype = 16;
        $field8->typeofdata = 'V~O';
        $field8->presence = 1;
        $field8->summaryfield = 1;
        $ordersTaskblockInstance2->addField($field8);
        $field8->setPicklistValues(array('--none--', '10%', '20%', '30%', '40%', '50%', '60%', '70%', '80%', '90%', '100%'));
    }

$field9 = Vtiger_Field::getInstance('orderstaskhours', $ordersTaskInstance);
    if ($field9) {
        echo "<br> Field 'orderstaskhours' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'Worked Hours';
        $field9->name = 'orderstaskhours';
        $field9->table = 'vtiger_orderstask';
        $field9->column = 'orderstaskhours';
        $field9->columntype = 'VARCHAR(255)';
        $field9->uitype = 7;
        $field9->typeofdata = 'V~O';
        $field9->summaryfield = 1;
        $ordersTaskblockInstance2->addField($field9);
    }

$field10 = Vtiger_Field::getInstance('startdate', $ordersTaskInstance);
    if ($field10) {
        echo "<br> Field 'startdate' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'Start Date';
        $field10->name = 'startdate';
        $field10->table = 'vtiger_orderstask';
        $field10->column = 'startdate';
        $field10->columntype = 'DATE';
        $field10->uitype = 5;
        $field10->typeofdata = 'D~O';
        $field10->quickcreate = 0;
        $field10->summaryfield = 1;
        $ordersTaskblockInstance2->addField($field10);
    }

$field11 = Vtiger_Field::getInstance('enddate', $ordersTaskInstance);
    if ($field11) {
        echo "<br> Field 'enddate' is already present. <br>";
    } else {
        $field11 = new Vtiger_Field();
        $field11->label = 'End Date';
        $field11->name = 'enddate';
        $field11->table = 'vtiger_orderstask';
        $field11->column = 'enddate';
        $field11->columntype = 'DATE';
        $field11->uitype = 5;
        $field11->typeofdata = 'D~O~OTH~GE~startdate~Start Date';
        $field11->summaryfield = 1;
        $ordersTaskblockInstance2->addField($field11);
    }

$field12 = Vtiger_Field::getInstance('createdtime', $ordersTaskInstance);
    if ($field12) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field12 = new Vtiger_Field();
        $field12->label = 'Created Time';
        $field12->name = 'createdtime';
        $field12->table = 'vtiger_crmentity';
        $field12->column = 'createdtime';
        $field12->uitype = 70;
        $field12->typeofdata = 'T~O';
        $field12->displaytype = 2;

        $ordersTaskblockInstance2->addField($field12);
    }

$field13 = Vtiger_Field::getInstance('modifiedtime', $ordersTaskInstance);
    if ($field13) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field13 = new Vtiger_Field();
        $field13->label = 'Modified Time';
        $field13->name = 'modifiedtime';
        $field13->table = 'vtiger_crmentity';
        $field13->column = 'modifiedtime';
        $field13->uitype = 70;
        $field13->typeofdata = 'T~O';
        $field13->displaytype = 2;

        $ordersTaskblockInstance2->addField($field13);
    }

$field14 = Vtiger_Field::getInstance('modifiedby', $ordersTaskInstance);
    if ($field14) {
        echo "<br> Field 'modifiedby' is already present. <br>";
    } else {
        $field14 = new Vtiger_Field();
        $field14->label = 'Last Modified By';
        $field14->name = 'modifiedby';
        $field14->table = 'vtiger_crmentity';
        $field14->column = 'modifiedby';
        $field14->uitype = 52;
        $field14->typeofdata = 'V~O';
        $field14->displaytype = 3;
        $field14->presence = 0;

        $ordersTaskblockInstance2->addField($field14);
    }

$field15 = Vtiger_Field::getInstance('description', $ordersTaskInstance);
    if ($field15) {
        echo "<br> Field 'description' is already present. <br>";
    } else {
        $field15 = new Vtiger_Field();
        $field15->label = 'description';
        $field15->name = 'description';
        $field15->table = 'vtiger_crmentity';
        $field15->column = 'description';
        $field15->uitype = 19;
        $field15->typeofdata = 'V~O';

        $ordersTaskblockInstance3->addField($field15);
    }

$field16 = Vtiger_Field::getInstance('orderstaskstatus', $ordersTaskInstance);
    if ($field16) {
        echo "<br> Field 'orderstaskstatus' is already present. <br>";
    } else {
        $field16 = new Vtiger_Field();
        $field16->label = 'LBL_ORDERSTASK_STATUS';
        $field16->name = 'orderstaskstatus';
        $field16->table = 'vtiger_orderstask';
        $field16->column = 'orderstaskstatus';
        $field16->columntype = 'VARCHAR(200)';
        $field16->uitype = 16;
        $field16->typeofdata = 'V~O';

        $ordersTaskblockInstance1->addField($field16);
        $field16->setPicklistValues(array('Open', 'In Progress', 'Completed', 'Deferred', 'Canceled'));
    }

$field17 = Vtiger_Field::getInstance('start_hour', $ordersTaskInstance);
    if ($field17) {
        echo "<br> Field 'start_hour' is already present. <br>";
    } else {
        $field17 = new Vtiger_Field();
        $field17->label = 'Start Hour';
        $field17->name = 'start_hour';
        $field17->table = 'vtiger_orderstask';
        $field17->column = 'start_hour';
        $field17->columntype = 'TIME';
        $field17->uitype = 14;
        $field17->typeofdata = 'T~O';

        $ordersTaskblockInstance2->addField($field17);
    }

$field18 = Vtiger_Field::getInstance('end_hour', $ordersTaskInstance);
    if ($field18) {
        echo "<br> Field 'end_hour' is already present. <br>";
    } else {
        $field18 = new Vtiger_Field();
        $field18->label = 'End Hour';
        $field18->name = 'end_hour';
        $field18->table = 'vtiger_orderstask';
        $field18->column = 'end_hour';
        $field18->columntype = 'TIME';
        $field18->uitype = 14;
        $field18->typeofdata = 'T~O';

        $ordersTaskblockInstance2->addField($field18);
    }

$field19 = Vtiger_Field::getInstance('service', $ordersTaskInstance);
    if ($field19) {
        echo "<br> Field 'service' is already present. <br>";
    } else {
        $field19 = new Vtiger_Field();
        $field19->label = 'Service';
        $field19->name = 'service';
        $field19->table = 'vtiger_orderstask';
        $field19->column = 'service';
        $field19->columntype = 'VARCHAR(100)';
        $field19->uitype = 2;
        $field19->typeofdata = 'V~O';

        $ordersTaskblockInstance4->addField($field19);
    }

$field20 = Vtiger_Field::getInstance('crew_number', $ordersTaskInstance);
    if ($field20) {
        echo "<br> Field 'crew_number' is already present. <br>";
    } else {
        $field20 = new Vtiger_Field();
        $field20->label = 'Crew Number';
        $field20->name = 'crew_number';
        $field20->table = 'vtiger_orderstask';
        $field20->column = 'crew_number';
        $field20->columntype = 'INT(10)';
        $field20->uitype = 7;
        $field20->typeofdata = 'I~O';

        $ordersTaskblockInstance4->addField($field20);
    }

$field22 = Vtiger_Field::getInstance('estimated_hours', $ordersTaskInstance);
    if ($field22) {
        echo "<br> Field 'estimated_hours' is already present. <br>";
    } else {
        $field22 = new Vtiger_Field();
        $field22->label = 'Estimate Travel Time';
        $field22->name = 'estimated_hours';
        $field22->table = 'vtiger_orderstask';
        $field22->column = 'estimated_hours';
        $field22->columntype = 'DECIMAL(10,2)';
        $field22->uitype = 7;
        $field22->typeofdata = 'N~O';

        $ordersTaskblockInstance4->addField($field22);
    }

$field23 = Vtiger_Field::getInstance('estimate_travel', $ordersTaskInstance);
    if ($field23) {
        echo "<br> Field 'estimate_travel' is already present. <br>";
    } else {
        $field23 = new Vtiger_Field();
        $field23->label = 'Estimate Travel Time';
        $field23->name = 'estimate_travel';
        $field23->table = 'vtiger_orderstask';
        $field23->column = 'estimate_travel';
        $field23->columntype = 'DECIMAL(10,2)';
        $field23->uitype = 7;
        $field23->typeofdata = 'N~O';

        $ordersTaskblockInstance4->addField($field23);
    }

$field24 = Vtiger_Field::getInstance('stops_number', $ordersTaskInstance);
    if ($field24) {
        echo "<br> Field 'stops_number' is already present. <br>";
    } else {
        $field24 = new Vtiger_Field();
        $field24->label = 'Stops Numbers';
        $field24->name = 'stops_number';
        $field24->table = 'vtiger_orderstask';
        $field24->column = 'stops_number';
        $field24->columntype = 'INT(10)';
        $field24->uitype = 7;
        $field24->typeofdata = 'I~O';

        $ordersTaskblockInstance4->addField($field24);
    }

/**$field25 = Vtiger_Field::getInstance('agent_number', $ordersTaskInstance);
    if($field25) {
        echo "<br> Field 'agent_number' is already present. <br>";
    } else {
    $field25 = new Vtiger_Field();
    $field25->label = 'Agent Number';
    $field25->name = 'agent_number';
    $field25->table = 'vtiger_orderstask';
    $field25->column = 'agent_number';
    $field25->columntype = 'INT(10)';
    $field25->uitype = 7;
    $field25->typeofdata = 'I~O';

    $ordersTaskblockInstance4->addField($field25);
    }*/

$field25 = Vtiger_Field::getInstance('stopnumber', $ordersTaskInstance);
    if ($field25) {
        echo "<li>the stopnumber already exists</li><br>";
    } else {
        $field25 = new Vtiger_Field();
        $field25->label = 'Stop Number';
        $field25->name = 'stopnumber';
        $field25->table = 'vtiger_orderstask';
        $field25->column = 'stopnumber';
        $field25->columntype = 'INT(19)';
        $field25->uitype = 10;
        $field25->typeofdata = 'V~O';
    
        $ordersTaskblockInstance4->addField($field25);
        $field15->setRelatedModules(array('Stops'));
    }


if ($field155) {
    echo "<li>the participating_agent already exists</li><br>";
} else {
    $field155 = new Vtiger_Field();
    $field155->label = 'Agent';
    $field155->name = 'participating_agent';
    $field155->table = 'vtiger_orderstask';
    $field155->column = 'participating_agent';
    $field155->columntype = 'INT(19)';
    $field155->uitype = 10;
    $field155->typeofdata = 'V~O';
    
    $ordersTaskblockInstance4->addField($field155);
    $field155->setRelatedModules(array('Agents'));
}

$field26 = Vtiger_Field::getInstance('service_date_from', $ordersTaskInstance);
    if ($field26) {
        echo "<br> Field 'service_date_from' is already present. <br>";
    } else {
        $field26 = new Vtiger_Field();
        $field26->label = 'Service Date From';
        $field26->name = 'service_date_from';
        $field26->table = 'vtiger_orderstask';
        $field26->column = 'service_date_from';
        $field26->columntype = 'DATE';
        $field26->uitype = 5;
        $field26->typeofdata = 'D~O';

        $ordersTaskblockInstance4->addField($field26);
    }

$field27 = Vtiger_Field::getInstance('service_date_to', $ordersTaskInstance);
    if ($field27) {
        echo "<br> Field 'service_date_to' is already present. <br>";
    } else {
        $field27 = new Vtiger_Field();
        $field27->label = 'Service Date To';
        $field27->name = 'service_date_to';
        $field27->table = 'vtiger_orderstask';
        $field27->column = 'service_date_to';
        $field27->columntype = 'DATE';
        $field27->uitype = 5;
        $field27->typeofdata = 'D~O';

        $ordersTaskblockInstance4->addField($field27);
    }

$field28 = Vtiger_Field::getInstance('pref_date_service', $ordersTaskInstance);
    if ($field28) {
        echo "<br> Field 'pref_date_service' is already present. <br>";
    } else {
        $field28 = new Vtiger_Field();
        $field28->label = 'Preferred Date Service';
        $field28->name = 'pref_date_service';
        $field28->table = 'vtiger_orderstask';
        $field28->column = 'pref_date_service';
        $field28->columntype = 'DATE';
        $field28->uitype = 5;
        $field28->typeofdata = 'D~O';

        $ordersTaskblockInstance4->addField($field28);
    }

$field29 = Vtiger_Field::getInstance('drivers_notes', $ordersTaskInstance);
    if ($field29) {
        echo "<br> Field 'drivers_notes' is already present. <br>";
    } else {
        $field29 = new Vtiger_Field();
        $field29->label = 'Drivers Notes';
        $field29->name = 'drivers_notes';
        $field29->table = 'vtiger_orderstask';
        $field29->column = 'drivers_notes';
        $field29->columntype = 'TEXT';
        $field29->uitype = 19;
        $field29->typeofdata = 'V~O';

        $ordersTaskblockInstance4->addField($field29);
    }

$field77 = Vtiger_Field::getInstance('est_vehicle_number', $ordersTaskInstance);
    if ($field77) {
        echo "<br> Field 'est_vehicle_number' is already present. <br>";
    } else {
        $field77 = new Vtiger_Field();
        $field77->label = 'Estimate Vehicle Number';
        $field77->name = 'est_vehicle_number';
        $field77->table = 'vtiger_orderstask';
        $field77->column = 'est_vehicle_number';
        $field77->columntype = 'INT(50)';
        $field77->uitype = 7;
        $field77->typeofdata = 'I~O';
        

        $ordersTaskblockInstance4->addField($field77);
    }

$field78 = Vtiger_Field::getInstance('dispatch_status', $ordersTaskInstance);
    if ($field78) {
        echo "<br> Field 'dispatch_status' is already present. <br>";
    } else {
        $field78 = new Vtiger_Field();
        $field78->label = 'Dispatch Status';
        $field78->name = 'dispatch_status';
        $field78->table = 'vtiger_orderstask';
        $field78->column = 'dispatch_status';
        $field78->columntype = 'VARCHAR(100)';
        $field78->uitype = 16;
        $field78->typeofdata = 'V~O';
        

        $ordersTaskblockInstance4->addField($field78);
        $field78->setPicklistValues(array('--', 'Accepted', 'Unassigned', 'Assigned', 'Rejected'));
    }

$field79 = Vtiger_Field::getInstance('operationtasktype', $ordersTaskInstance);
    if ($field79) {
        echo "<br> Field 'operationtasktype' is already present. <br>";
    } else {
        $field79 = new Vtiger_Field();
        $field79->label = 'Operation Task Type';
        $field79->name = 'operationtasktype';
        $field79->table = 'vtiger_orderstask';
        $field79->column = 'operationtasktype';
        $field79->columntype = 'VARCHAR(250)';
        $field79->uitype = 16;
        $field79->typeofdata = 'V~O';
        $field79->setPicklistValues(array('Origin Services', 'Transportation Services', 'Destination Services', 'Warehouse Services'));

    
        $ordersTaskblockInstance4->addField($field79);
    }

$field80 = Vtiger_Field::getInstance('servicenameoptions', $ordersTaskInstance);
    if ($field80) {
        echo "<br> Field 'servicenameoptions' is already present. <br>";
    } else {
        $field80 = new Vtiger_Field();
        $field80->label = 'Service Name Options';
        $field80->name = 'servicenameoptions';
        $field80->table = 'vtiger_orderstask';
        $field80->column = 'servicenameoptions';
        $field80->columntype = 'VARCHAR(250)';
        $field80->uitype = 16;
        $field80->typeofdata = 'V~O';
        $field80->setPicklistValues(array('Pack', 'Load', 'Pack/Load', 'Extra Pickup', 'Carton Delivery', 'APU', 'Pack/Load/Deliver', 'Transportation', 'Deliver', 'Unpack', 'Extra Delivery', 'Debris Pickup', 'Storage Pickup', 'Storage Delivery', 'Storage Access'));

        $ordersTaskblockInstance4->addField($field80);
        $field80->setPicklistValues(array('--', 'Accepted', 'Unassigned', 'Assigned', 'Rejected'));
    }

$field81 = Vtiger_Field::getInstance('disp_assigneddate', $ordersTaskInstance);
    if ($field81) {
        echo "<br> Field 'disp_assigneddate' is already present. <br>";
    } else {
        $field81 = new Vtiger_Field();
        $field81->label = 'Assigned Date';
        $field81->name = 'disp_assigneddate';
        $field81->table = 'vtiger_orderstask';
        $field81->column = 'disp_assigneddate';
        $field81->columntype = 'DATE';
        $field81->uitype = 5;
        $field81->typeofdata = 'D~O';

        $ordersTaskblockInstance5->addField($field81);
    }

$field82 = Vtiger_Field::getInstance('disp_assignedstart', $ordersTaskInstance);
    if ($field82) {
        echo "<br> Field 'disp_assignedstart' is already present. <br>";
    } else {
        $field82 = new Vtiger_Field();
        $field82->label = 'Assigned Start Time';
        $field82->name = 'disp_assignedstart';
        $field82->table = 'vtiger_orderstask';
        $field82->column = 'disp_assignedstart';
        $field82->columntype = 'TIME';
        $field82->uitype = 14;
        $field82->typeofdata = 'T~O';

        $ordersTaskblockInstance5->addField($field82);
    };

$field83 = Vtiger_Field::getInstance('disp_assignedcrew', $ordersTaskInstance);
    if ($field83) {
        echo "<br> Field 'disp_assignedcrew' is already present. <br>";
    } else {
        $field83 = new Vtiger_Field();
        $field83->label = 'Assigned Crew Members';
        $field83->name = 'disp_assignedcrew';
        $field83->table = 'vtiger_orderstask';
        $field83->column = 'disp_assignedcrew';
        $field83->columntype = 'INT(10)';
        $field83->uitype = 7;
        $field83->typeofdata = 'I~O';

        $ordersTaskblockInstance5->addField($field83);
    };

$field84 = Vtiger_Field::getInstance('disp_actualdate', $ordersTaskInstance);
    if ($field84) {
        echo "<br> Field 'disp_actualdate' is already present. <br>";
    } else {
        $field84 = new Vtiger_Field();
        $field84->label = 'Actual Date';
        $field84->name = 'disp_actualdate';
        $field84->table = 'vtiger_orderstask';
        $field84->column = 'disp_actualdate';
        $field84->columntype = 'DATE';
        $field84->uitype = 5;
        $field84->typeofdata = 'D~O';

        $ordersTaskblockInstance5->addField($field84);
    };

$field85 = Vtiger_Field::getInstance('disp_actualstart', $ordersTaskInstance);
    if ($field85) {
        echo "<br> Field 'disp_actualstart' is already present. <br>";
    } else {
        $field85 = new Vtiger_Field();
        $field85->label = 'Actual Start Time';
        $field85->name = 'disp_actualstart';
        $field85->table = 'vtiger_orderstask';
        $field85->column = 'disp_actualstart';
        $field85->columntype = 'TIME';
        $field85->uitype = 14;
        $field85->typeofdata = 'T~O';

        $ordersTaskblockInstance5->addField($field85);
    };

$field86 = Vtiger_Field::getInstance('disp_actualcrew', $ordersTaskInstance);
    if ($field86) {
        echo "<br> Field 'disp_actualcrew' is already present. <br>";
    } else {
        $field86 = new Vtiger_Field();
        $field86->label = 'Actual Crew Members';
        $field86->name = 'disp_actualcrew';
        $field86->table = 'vtiger_orderstask';
        $field86->column = 'disp_actualcrew';
        $field86->columntype = 'INT(10)';
        $field86->uitype = 7;
        $field86->typeofdata = 'I~O';

        $ordersTaskblockInstance5->addField($field86);
    };

$field87 = Vtiger_Field::getInstance('disp_actualhours', $ordersTaskInstance);
    if ($field87) {
        echo "<br> Field 'disp_actualhours' is already present. <br>";
    } else {
        $field87 = new Vtiger_Field();
        $field87->label = 'Actual Total Hours';
        $field87->name = 'disp_actualhours';
        $field87->table = 'vtiger_orderstask';
        $field87->column = 'disp_actualhours';
        $field87->columntype = 'DECIMAL(10,2)';
        $field87->uitype = 7;
        $field87->typeofdata = 'N~O';

        $ordersTaskblockInstance5->addField($field87);
    };
        
        
        
 $field88 = Vtiger_Field::getInstance('related_employee', $ordersTaskInstance);
if ($field88) {
    echo "<br> Field 'disp_actualhours' is already present. <br>";
} else {
    $field88 = new Vtiger_Field();
    $field88->label = 'Driver Name';
    $field88->name = 'related_employee';
    $field88->table = 'vtiger_orderstask';
    $field88->column = 'related_employee';
    $field88->columntype = 'INT(10)';
    $field88->uitype = 10;
    $field88->typeofdata = 'I~O';

    $ordersTaskblockInstance5->addField($field88);
    $field88->setRelatedModules(array('Employees'));
};



//add filter in ordertask module
$filter1 = Vtiger_Filter::getInstance('All', $ordersTaskInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $ordersTaskInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field4, 1)->addField($field3, 2)->addField($field8, 3)->addField($field9, 4)->addField($field10, 5)->addField($field11, 6)->addField($field5, 7);
    }


    

if (!Vtiger_Utils::CheckTable('vtiger_calsettings_colors')) {
    echo "<li>creating vtiger_calsettings_colors </li><br>";
    Vtiger_Utils::CreateTable('vtiger_calsettings_colors',
                             '(percentage INT(10),
    						 	color VARCHAR(11),
    						 	primary key
								(percentage)
    						 	)', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_calsettings_days')) {
    echo "<li>creating vtiger_calsettings_days </li><br>";
    Vtiger_Utils::CreateTable('vtiger_calsettings_days',
                             '(saturday INT(2),
    						 	sunday INT(2)
    						 	)', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_colorsettings')) {
    echo "<li>creating vtiger_colorsettings </li><br>";
    Vtiger_Utils::CreateTable("CREATE TABLE `vtiger_colorsettings` (
                        `id` int(11) NOT NULL,
                        `value` varchar(255) NOT NULL,
                        `color` varchar(8) NOT NULL DEFAULT '#FFFFFF'
                      ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;", true);
}

//start ordersMilestone fields


$field1 = Vtiger_Field::getInstance('ordersmilestonename', $ordersMilestoneInstance);
    if ($field1) {
        echo "<br> Field 'ordersmilestonename' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'Orders Milestone Name';
        $field1->name = 'ordersmilestonename';
        $field1->table = 'vtiger_ordersmilestone';
        $field1->column = 'ordersmilestonename';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 2;
        $field1->typeofdata = 'V~M';
        $field1->quickcreate = 0;

        $ordersMilestoneblockInstance1->addField($field1);
        
        $ordersMilestoneInstance->setEntityIdentifier($field1);
    }

$field2 = Vtiger_Field::getInstance('ordersmilestonedate', $ordersMilestoneInstance);
    if ($field2) {
        echo "<br> Field 'ordersmilestonename' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Milestone Date';
        $field2->name = 'ordersmilestonedate';
        $field2->table = 'vtiger_ordersmilestone';
        $field2->column = 'ordersmilestonedate';
        $field2->columntype = 'DATE';
        $field2->uitype = 5;
        $field2->typeofdata = 'D~O';
        $field2->quickcreate = 0;

        $ordersMilestoneblockInstance1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('ordersid', $ordersMilestoneInstance);
    if ($field3) {
        echo "<br> Field 'ordersid' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'Related to';
        $field3->name = 'ordersid';
        $field3->table = 'vtiger_ordersmilestone';
        $field3->column = 'ordersid';
        $field3->columntype = 'VARCHAR(100)';
        $field3->uitype = 10;
        $field3->typeofdata = 'V~M';
        $field3->quickcreate = 0;

        $ordersMilestoneblockInstance1->addField($field3);
        $field3->setRelatedModules(array('Orders'));
    }

$field4 = Vtiger_Field::getInstance('ordersmilestonetype', $ordersMilestoneInstance);
    if ($field4) {
        echo "<br> Field 'ordersmilestonetype' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'Type';
        $field4->name = 'ordersmilestonetype';
        $field4->table = 'vtiger_ordersmilestone';
        $field4->column = 'ordersmilestonetype';
        $field4->columntype = 'VARCHAR(100)';
        $field4->uitype = 16;
        $field4->typeofdata = 'V~M';
    

        $ordersMilestoneblockInstance1->addField($field4);
        $field4->setPicklistValues(array('--none--', 'administrative', 'operative', 'other'));
    }

$field5 = Vtiger_Field::getInstance('assigned_user_id', $ordersMilestoneInstance);
    if ($field5) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'Assigned To';
        $field5->name = 'assigned_user_id';
        $field5->table = 'vtiger_crmentity';
        $field5->column = 'smownerid';
        $field5->uitype = 53;
        $field5->typeofdata = 'V~M';
        $field5->quickcreate = 0;

        $ordersMilestoneblockInstance1->addField($field5);
    }

$field6 = Vtiger_Field::getInstance('ordersmilestone_no', $ordersMilestoneInstance);
    if ($field6) {
        echo "<br> Field 'ordersmilestone_no' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'Orders Milestone No';
        $field6->name = 'ordersmilestone_no';
        $field6->table = 'vtiger_ordersmilestone';
        $field6->column = 'ordersmilestone_no';
        $field6->columntype = 'VARCHAR(100)';
        $field6->uitype = 4;
        $field6->typeofdata = 'V~O';
        $field6->quickcreate =3;
        $field6->presence = 0;

        $ordersMilestoneblockInstance1->addField($field6);
        $entity = new CRMEntity();
        $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'ORDML', 1);
    }

$field7 = Vtiger_Field::getInstance('createdtime', $ordersMilestoneInstance);
    if ($field7) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'Created Time';
        $field7->name = 'createdtime';
        $field7->table = 'vtiger_crmentity';
        $field7->column = 'createdtime';
        $field7->uitype = 70;
        $field7->typeofdata = 'T~O';
        $field7->displaytype = 2;

        $ordersMilestoneblockInstance2->addField($field7);
    }

$field8 = Vtiger_Field::getInstance('modifiedtime', $ordersMilestoneInstance);
    if ($field8) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'Modified Time';
        $field8->name = 'modifiedtime';
        $field8->table = 'vtiger_crmentity';
        $field8->column = 'modifiedtime';
        $field8->uitype = 70;
        $field8->typeofdata = 'T~O';
        $field8->displaytype = 2;

        $ordersMilestoneblockInstance2->addField($field8);
    }

$field9 = Vtiger_Field::getInstance('modifiedby', $ordersMilestoneInstance);
    if ($field9) {
        echo "<br> Field 'modifiedby' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'Last Modified By';
        $field9->name = 'modifiedby';
        $field9->table = 'vtiger_crmentity';
        $field9->column = 'modifiedby';
        $field9->uitype = 52;
        $field9->typeofdata = 'V~O';
        $field9->displaytype = 3;
        $field9->presence = 0;

        $ordersMilestoneblockInstance2->addField($field9);
    }

$field10 = Vtiger_Field::getInstance('description', $ordersMilestoneInstance);
    if ($field10) {
        echo "<br> Field 'description' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'description';
        $field10->name = 'description';
        $field10->table = 'vtiger_crmentity';
        $field10->column = 'description';
        $field10->uitype = 19;
        $field10->typeofdata = 'V~O';

        $ordersMilestoneblockInstance3->addField($field10);
    }

//add filter in ordermilestone module
$filter1 = Vtiger_Filter::getInstance('All', $ordersMilestoneInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $ordersMilestoneInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field2, 1)->addField($field10, 2);
    }


    //orders fields

$field1 = Vtiger_Field::getInstance('ordersname', $ordersInstance);
    if ($field1) {
        echo "<br> Field 'ordersname' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'Orders Name';
        $field1->name = 'ordersname';
        $field1->table = 'vtiger_orders';
        $field1->column = 'ordersname';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 2;
        $field1->typeofdata = 'V~M';
        $field1->quickcreate = 0;

        $ordersblockInstance1->addField($field1);
        
        $ordersInstance->setEntityIdentifier($field1);
    }

$field2 = Vtiger_Field::getInstance('startdate', $ordersInstance);
    if ($field2) {
        echo "<br> Field 'startdate' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Start Date';
        $field2->name = 'startdate';
        $field2->table = 'vtiger_orders';
        $field2->column = 'startdate';
        $field2->columntype = 'DATE';
        $field2->uitype = 23;
        $field2->typeofdata = 'D~O';
        $field2->quickcreate = 0;
        $field2->sequence = 21;

        $ordersblockInstance1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('targetenddate', $ordersInstance);
    if ($field3) {
        echo "<br> Field 'targetenddate' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'Target End Date';
        $field3->name = 'targetenddate';
        $field3->table = 'vtiger_orders';
        $field3->column = 'targetenddate';
        $field3->columntype = 'DATE';
        $field3->uitype = 23;
        $field3->typeofdata = 'D~O~OTH~GE~startdate~Start Date';
        $field3->quickcreate = 0;
        $field3->sequence = 22;

        $ordersblockInstance1->addField($field3);
    }

$field4 = Vtiger_Field::getInstance('actualenddate', $ordersInstance);
    if ($field4) {
        echo "<br> Field 'actualenddate' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'Actual End Date';
        $field4->name = 'actualenddate';
        $field4->table = 'vtiger_orders';
        $field4->column = 'actualenddate';
        $field4->columntype = 'DATE';
        $field4->uitype = 23;
        $field4->typeofdata = 'D~O~OTH~GE~startdate~Start Date';
        $field4->sequence = 20;
        
        $ordersblockInstance1->addField($field4);
    }

$field5 = Vtiger_Field::getInstance('ordersstatus', $ordersInstance);
    if ($field5) {
        echo "<br> Field 'ordersstatus' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'Status';
        $field5->name = 'ordersstatus';
        $field5->table = 'vtiger_orders';
        $field5->column = 'ordersstatus';
        $field5->columntype = 'VARCHAR(100)';
        $field5->uitype = 16;
        $field5->typeofdata = 'V~O';
        $field5->sequence = 9;

        $ordersblockInstance1->addField($field5);
        $field5->setPicklistValues(array('Booked', 'Registered', 'On Hold', 'Canceled', 'Sent to Dispatch', 'Packing', 'Loading', 'Delivered', 'In SIT', 'In Perm Storage'));
    }

$field6 = Vtiger_Field::getInstance('orderstype', $ordersInstance);
    if ($field6) {
        echo "<br> Field 'orderstype' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'Type';
        $field6->name = 'orderstype';
        $field6->table = 'vtiger_orders';
        $field6->column = 'orderstype';
        $field6->columntype = 'VARCHAR(100)';
        $field6->uitype = 16;
        $field6->typeofdata = 'V~O';
        $field6->sequence = 8;

        $ordersblockInstance1->addField($field6);
        $field6->setPicklistValues(array('--none--', 'administrative', 'operative', 'other'));
    }

$field7 = Vtiger_Field::getInstance('linktoaccountscontacts', $ordersInstance);
    if ($field7) {
        echo "<br> Field 'linktoaccountscontacts' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'Related To';
        $field7->name = 'linktoaccountscontacts';
        $field7->table = 'vtiger_orders';
        $field7->column = 'linktoaccountscontacts';
        $field7->columntype = 'VARCHAR(100)';
        $field7->uitype = 10;
        $field7->typeofdata = 'V~O';
        $field7->presence = 1;

        $ordersblockInstance1->addField($field7);
        $field7->setRelatedModules(array('Accounts', 'Contacts'));
    }

$field8 = Vtiger_Field::getInstance('assigned_user_id', $ordersInstance);
    if ($field8) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'Assigned To';
        $field8->name = 'assigned_user_id';
        $field8->table = 'vtiger_crmentity';
        $field8->column = 'smownerid';
        $field8->uitype = 53;
        $field8->typeofdata = 'V~M';
        $field8->quickcreate = 0;
        $field8->sequence = 24;

        $ordersblockInstance1->addField($field8);
    }

//Adding new UI Type for
        $adb = PearDatabase::getInstance();
            
            $adb->pquery("INSERT INTO vtiger_ws_fieldtype(uitype, fieldtype) 
                        VALUES(?,?)", array('1001', 'ordersautoseq'));
        
        
        
$field9 = Vtiger_Field::getInstance('orders_no', $ordersInstance);
    if ($field9) {
        echo "<br> Field 'orders_no' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'Orders No';
        $field9->name = 'orders_no';
        $field9->table = 'vtiger_orders';
        $field9->column = 'orders_no';
        $field9->columntype = 'VARCHAR(100)';
        $field9->uitype = 1001;
        $field9->typeofdata = 'V~O';
        $field9->quickcreate =3;
        $field9->presence = 0;
        $field9->sequence = 9;

        $ordersblockInstance1->addField($field9);
        $entity = new CRMEntity();
        $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'ORD', 1);
    }



$field10 = Vtiger_Field::getInstance('targetbudget', $ordersInstance);
    if ($field10) {
        echo "<br> Field 'targetbudget' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'Target Budget';
        $field10->name = 'targetbudget';
        $field10->table = 'vtiger_orders';
        $field10->column = 'targetbudget';
        $field10->columntype = 'VARCHAR(255)';
        $field10->uitype = 7;
        $field10->typeofdata = 'V~O';
        $field10->presence = 1;

        $ordersblockInstance1->addField($field10);
    }

$field11 = Vtiger_Field::getInstance('ordersurl', $ordersInstance);
    if ($field11) {
        echo "<br> Field 'ordersurl' is already present. <br>";
    } else {
        $field11 = new Vtiger_Field();
        $field11->label = 'orders Url';
        $field11->name = 'ordersurl';
        $field11->table = 'vtiger_orders';
        $field11->column = 'ordersurl';
        $field11->columntype = 'VARCHAR(255)';
        $field11->uitype = 17;
        $field11->typeofdata = 'V~O';
        $field11->presence = 1;

        $ordersblockInstance1->addField($field11);
    }

$field12 = Vtiger_Field::getInstance('orderspriority', $ordersInstance);
    if ($field12) {
        echo "<br> Field 'orderspriority' is already present. <br>";
    } else {
        $field12 = new Vtiger_Field();
        $field12->label = 'Priority';
        $field12->name = 'orderspriority';
        $field12->table = 'vtiger_orders';
        $field12->column = 'orderspriority';
        $field12->columntype = 'VARCHAR(100)';
        $field12->uitype = 16;
        $field12->typeofdata = 'V~M';
        $field12->sequence = 13;

        $ordersblockInstance1->addField($field12);
        $field12->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move', 'International Move', 'Commercial Move'));
    }

$field13 = Vtiger_Field::getInstance('progress', $ordersInstance);
    if ($field13) {
        echo "<br> Field 'progress' is already present. <br>";
    } else {
        $field13 = new Vtiger_Field();
        $field13->label = 'Progress';
        $field13->name = 'progress';
        $field13->table = 'vtiger_orders';
        $field13->column = 'progress';
        $field13->columntype = 'VARCHAR(100)';
        $field13->uitype = 16;
        $field13->typeofdata = 'V~O';

        $ordersblockInstance1->addField($field13);
        $field13->setPicklistValues(array('--none--', '10%', '20%', '30%', '40%', '50%', '60%', '70%', '80%', '90%', '100%'));
    }

$field14 = Vtiger_Field::getInstance('createdtime', $ordersInstance);
    if ($field14) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field14 = new Vtiger_Field();
        $field14->label = 'Created Time';
        $field14->name = 'createdtime';
        $field14->table = 'vtiger_crmentity';
        $field14->column = 'createdtime';
        $field14->uitype = 70;
        $field14->typeofdata = 'T~O';
        $field14->displaytype = 2;
        $field14->sequence = 5;

        $ordersblockInstance7->addField($field14);
    }

$field15 = Vtiger_Field::getInstance('modifiedtime', $ordersInstance);
    if ($field15) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field15 = new Vtiger_Field();
        $field15->label = 'Modified Time';
        $field15->name = 'modifiedtime';
        $field15->table = 'vtiger_crmentity';
        $field15->column = 'modifiedtime';
        $field15->uitype = 70;
        $field15->typeofdata = 'T~O';
        $field15->displaytype = 2;
        $field15->sequence = 6;

        $ordersblockInstance7->addField($field15);
    }



$field16 = Vtiger_Field::getInstance('modifiedby', $ordersInstance);
    if ($field16) {
        echo "<br> Field 'modifiedby' is already present. <br>";
    } else {
        $field16 = new Vtiger_Field();
        $field16->label = 'Last Modified By';
        $field16->name = 'modifiedby';
        $field16->table = 'vtiger_crmentity';
        $field16->column = 'modifiedby';
        $field16->uitype = 52;
        $field16->typeofdata = 'V~O';
        $field16->displaytype = 3;
        $field16->presence = 0;

        $ordersblockInstance7->addField($field16);
    }

$field17 = Vtiger_Field::getInstance('description', $ordersInstance);
    if ($field17) {
        echo "<br> Field 'description' is already present. <br>";
    } else {
        $field17 = new Vtiger_Field();
        $field17->label = 'description';
        $field17->name = 'description';
        $field17->table = 'vtiger_crmentity';
        $field17->column = 'description';
        $field17->uitype = 19;
        $field17->typeofdata = 'V~O';

        $ordersblockInstance6->addField($field17);
    }

$field77 = Vtiger_Field::getInstance('orders_fname', $ordersInstance);
    if ($field77) {
        echo "<br> Field 'orders_fname' is already present. <br>";
    } else {
        $field77 = new Vtiger_Field();
        $field77->label = 'LBL_ORDERS_FNAME';
        $field77->name = 'orders_fname';
        $field77->table = 'vtiger_orders';
        $field77->column = 'orders_fname';
        $field77->columntype = 'VARCHAR(50)';
        $field77->uitype = 1;
        $field77->typeofdata = 'V~O';
        $field77->sequence = 2;

        $ordersblockInstance1->addField($field77);
    }

$field18 = Vtiger_Field::getInstance('orders_account', $ordersInstance);
    if ($field18) {
        echo "<br> Field 'orders_account' is already present. <br>";
    } else {
        $field18 = new Vtiger_Field();
        $field18->label = 'LBL_ORDERS_ACCOUNT';
        $field18->name = 'orders_account';
        $field18->table = 'vtiger_orders';
        $field18->column = 'orders_account';
        $field18->columntype = 'VARCHAR(100)';
        $field18->uitype = 10;
        $field18->typeofdata = 'V~O';
        $field18->sequence = 7;

        $ordersblockInstance1->addField($field18);
        $field18->setRelatedModules(array('Accounts'));
    }

$field018 = Vtiger_Field::getInstance('orders_contacts', $ordersInstance);
    if ($field018) {
        echo "<br> Field 'orders_contacts' is already present. <br>";
    } else {
        $field018 = new Vtiger_Field();
        $field018->label = 'LBL_ORDERS_CONTACTS';
        $field018->name = 'orders_contacts';
        $field018->table = 'vtiger_orders';
        $field018->column = 'orders_contacts';
        $field018->columntype = 'VARCHAR(100)';
        $field018->uitype = 10;
        $field018->typeofdata = 'V~O';
        $field018->sequence = 7;

        $ordersblockInstance1->addField($field018);
        $field018->setRelatedModules(array('Contacts'));
    }

$field19 = Vtiger_Field::getInstance('orders_accounttype', $ordersInstance);
    if ($field19) {
        echo "<br> Field 'orders_accounttype' is already present. <br>";
    } else {
        $field19 = new Vtiger_Field();
        $field19->label = 'LBL_ORDERS_ACCOUNTTYPE';
        $field19->name = 'orders_accounttype';
        $field19->table = 'vtiger_orders';
        $field19->column = 'orders_accounttype';
        $field19->columntype = 'VARCHAR(220)';
        $field19->uitype = 16;
        $field19->typeofdata = 'V~M';
        $field19->sequence = 4;

        $ordersblockInstance1->addField($field19);
        $field19->setPicklistValues(array('National Account', 'COD', 'Third Party Relocation'));
    }

$field20 = Vtiger_Field::getInstance('orders_vanlineregnum', $ordersInstance);
    if ($field20) {
        echo "<br> Field 'orders_vanlineregnum' is already present. <br>";
    } else {
        $field20 = new Vtiger_Field();
        $field20->label = 'LBL_ORDERS_VANLINEREGNUM';
        $field20->name = 'orders_vanlineregnum';
        $field20->table = 'vtiger_orders';
        $field20->column = 'orders_vanlineregnum';
        $field20->columntype = 'VARCHAR(100)';
        $field20->uitype = 1;
        $field20->typeofdata = 'V~O';
        $field20->sequence = 6;

        $ordersblockInstance1->addField($field20);
    }

$field21 = Vtiger_Field::getInstance('orders_bolnumber', $ordersInstance);
    if ($field21) {
        echo "<br> Field 'orders_bolnumber' is already present. <br>";
    } else {
        $field21 = new Vtiger_Field();
        $field21->label = 'LBL_ORDERS_BOLNUMBER';
        $field21->name = 'orders_bolnumber';
        $field21->table = 'vtiger_orders';
        $field21->column = 'orders_bolnumber';
        $field21->columntype = 'VARCHAR(100)';
        $field21->uitype = 1;
        $field21->typeofdata = 'V~O';
        $field21->sequence = 8;

        $ordersblockInstance1->addField($field21);
    }

$field22 = Vtiger_Field::getInstance('orders_gblnumber', $ordersInstance);
    if ($field22) {
        echo "<br> Field 'orders_gblnumber' is already present. <br>";
    } else {
        $field22 = new Vtiger_Field();
        $field22->label = 'LBL_ORDERS_GBLNUMBER';
        $field22->name = 'orders_gblnumber';
        $field22->table = 'vtiger_orders';
        $field22->column = 'orders_gblnumber';
        $field22->columntype = 'VARCHAR(100)';
        $field22->uitype = 1;
        $field22->typeofdata = 'V~O';
        $field22->sequence = 11;

        $ordersblockInstance1->addField($field22);
    }

$field23 = Vtiger_Field::getInstance('orders_ponumber', $ordersInstance);
    if ($field23) {
        echo "<br> Field 'orders_ponumber' is already present. <br>";
    } else {
        $field23 = new Vtiger_Field();
        $field23->label = 'LBL_ORDERS_PONUMBER';
        $field23->name = 'orders_ponumber';
        $field23->table = 'vtiger_orders';
        $field23->column = 'orders_ponumber';
        $field23->columntype = 'VARCHAR(100)';
        $field23->uitype = 1;
        $field23->typeofdata = 'V~O';
        $field23->sequence = 10;

        $ordersblockInstance1->addField($field23);
    }

$field24 = Vtiger_Field::getInstance('orders_commodity', $ordersInstance);
    if ($field24) {
        echo "<br> Field 'orders_commodity' is already present. <br>";
    } else {
        $field24 = new Vtiger_Field();
        $field24->label = 'LBL_ORDERS_COMMODITY';
        $field24->name = 'orders_commodity';
        $field24->table = 'vtiger_orders';
        $field24->column = 'orders_commodity';
        $field24->columntype = 'VARCHAR(255)';
        $field24->uitype = 16;
        $field24->typeofdata = 'V~M';
        $field24->sequence = 12;

        $ordersblockInstance1->addField($field24);
        $field24->setPicklistValues(array('Household Goods', 'Commercial', 'Military HHG', 'Military Commercial', 'Government Commercial', 'Government HHG'));
    }

$field25 = Vtiger_Field::getInstance('orders_elinehaul', $ordersInstance);
    if ($field25) {
        echo "<br> Field 'orders_elinehaul' is already present. <br>";
    } else {
        $field25 = new Vtiger_Field();
        $field25->label = 'LBL_ORDERS_ELINEHAUL';
        $field25->name = 'orders_elinehaul';
        $field25->table = 'vtiger_orders';
        $field25->column = 'orders_elinehaul';
        $field25->columntype = 'DECIMAL(25,8)';
        $field25->uitype = 71;
        $field25->typeofdata = 'N~O';
        $field25->sequence = 14;

        $ordersblockInstance1->addField($field25);
    }

$field26 = Vtiger_Field::getInstance('orders_etotal', $ordersInstance);
    if ($field26) {
        echo "<br> Field 'orders_etotal' is already present. <br>";
    } else {
        $field26 = new Vtiger_Field();
        $field26->label = 'LBL_ORDERS_ETOTAL';
        $field26->name = 'orders_etotal';
        $field26->table = 'vtiger_orders';
        $field26->column = 'orders_etotal';
        $field26->columntype = 'DECIMAL(25,8)';
        $field26->uitype = 71;
        $field26->typeofdata = 'N~O';
        $field26->sequence = 17;

        $ordersblockInstance1->addField($field26);
    }

$field26 = Vtiger_Field::getInstance('orders_etype', $ordersInstance);
    if ($field26) {
        echo "<br> Field 'orders_etype' is already present. <br>";
    } else {
        $field26 = new Vtiger_Field();
        $field26->label = 'LBL_ORDERS_ETYPE';
        $field26->name = 'orders_etype';
        $field26->table = 'vtiger_orders';
        $field26->column = 'orders_etype';
        $field26->columntype = 'VARCHAR(255)';
        $field26->uitype = 16;
        $field26->typeofdata = 'V~M';
        $field26->sequence = 16;

        $ordersblockInstance1->addField($field26);
        $field26->setPicklistValues(array('Binding', 'Non Binding', 'Not To Exceed'));
    }

$field27 = Vtiger_Field::getInstance('orders_discount', $ordersInstance);
    if ($field27) {
        echo "<br> Field 'orders_discount' is already present. <br>";
    } else {
        $field27 = new Vtiger_Field();
        $field27->label = 'LBL_ORDERS_DISCOUNT';
        $field27->name = 'orders_discount';
        $field27->table = 'vtiger_orders';
        $field27->column = 'orders_discount';
        $field27->columntype = 'DECIMAL(5,2)';
        $field27->uitype = 9;
        $field27->typeofdata = 'N~O';
        $field27->sequence = 19;

        $ordersblockInstance1->addField($field27);
    }

$field28 = Vtiger_Field::getInstance('orders_miles', $ordersInstance);
    if ($field28) {
        echo "<br> Field 'orders_miles' is already present. <br>";
    } else {
        $field28 = new Vtiger_Field();
        $field28->label = 'LBL_ORDERS_MILES';
        $field28->name = 'orders_miles';
        $field28->table = 'vtiger_orders';
        $field28->column = 'orders_miles';
        $field28->columntype = 'INT(100)';
        $field28->uitype = 7;
        $field28->typeofdata = 'I~O';
        $field28->sequence = 18;

        $ordersblockInstance1->addField($field28);
    }

$field29 = Vtiger_Field::getInstance('orders_opportunities', $ordersInstance);
    if ($field29) {
        echo "<br> Field 'orders_opportunities' is already present. <br>";
    } else {
        $field29 = new Vtiger_Field();
        $field29->label = 'LBL_ORDERS_OPPORTUNITIES';
        $field29->name = 'orders_opportunities';
        $field29->table = 'vtiger_orders';
        $field29->column = 'orders_opportunities';
        $field29->columntype = 'VARCHAR(220)';
        $field29->uitype = 10;
        $field29->typeofdata = 'V~O';
        $field29->sequence = 25;

        $ordersblockInstance1->addField($field29);
        $field29->setRelatedModules(array('Opportunities'));
    }

$field30 = Vtiger_Field::getInstance('orders_relatedorders', $ordersInstance);
    if ($field30) {
        echo "<br> Field 'orders_relatedorders' is already present. <br>";
    } else {
        $field30 = new Vtiger_Field();
        $field30->label = 'LBL_ORDERS_RELATED';
        $field30->name = 'orders_relatedorders';
        $field30->table = 'vtiger_orders';
        $field30->column = 'orders_relatedorders';
        $field30->columntype = 'VARCHAR(220)';
        $field30->uitype = 10;
        $field30->typeofdata = 'V~O';
        $field30->sequence = 23;

        $ordersblockInstance1->addField($field30);
        $field30->setRelatedModules(array('Orders'));
    }

//$blockInstance->save($moduleInstance);


// Add orders fields to Date block
$field31 = Vtiger_Field::getInstance('orders_pdate', $ordersInstance);
    if ($field31) {
        echo "<br> Field 'orders_pdate' is already present. <br>";
    } else {
        $field31 = new Vtiger_Field();
        $field31->label = 'LBL_ORDERS_PDATE';
        $field31->name = 'orders_pdate';
        $field31->table = 'vtiger_orders';
        $field31->column = 'orders_pdate';
        $field31->columntype = 'DATE';
        $field31->uitype = 5;
        $field31->typeofdata = 'D~O';
        $field31->sequence = 1;

        $ordersblockInstance4->addField($field31);
    }

$field32 = Vtiger_Field::getInstance('orders_ldate', $ordersInstance);
    if ($field32) {
        echo "<br> Field 'orders_ldate' is already present. <br>";
    } else {
        $field32 = new Vtiger_Field();
        $field32->label = 'LBL_ORDERS_LDATE';
        $field32->name = 'orders_ldate';
        $field32->table = 'vtiger_orders';
        $field32->column = 'orders_ldate';
        $field32->columntype = 'DATE';
        $field32->uitype = 5;
        $field32->typeofdata = 'D~O';
        $field32->sequence = 4;

        $ordersblockInstance4->addField($field32);
    }

$field33 = Vtiger_Field::getInstance('orders_ddate', $ordersInstance);
    if ($field33) {
        echo "<br> Field 'orders_ddate' is already present. <br>";
    } else {
        $field33 = new Vtiger_Field();
        $field33->label = 'LBL_ORDERS_DDATE';
        $field33->name = 'orders_ddate';
        $field33->table = 'vtiger_orders';
        $field33->column = 'orders_ddate';
        $field33->columntype = 'DATE';
        $field33->uitype = 5;
        $field33->typeofdata = 'D~O';
        $field33->sequence = 7;

        $ordersblockInstance4->addField($field33);
    }

$field34 = Vtiger_Field::getInstance('orders_ptdate', $ordersInstance);
    if ($field34) {
        echo "<br> Field 'orders_ptdate' is already present. <br>";
    } else {
        $field34 = new Vtiger_Field();
        $field34->label = 'LBL_ORDERS_PTDATE';
        $field34->name = 'orders_ptdate';
        $field34->table = 'vtiger_orders';
        $field34->column = 'orders_ptdate';
        $field34->columntype = 'DATE';
        $field34->uitype = 5;
        $field34->typeofdata = 'D~O';
        $field34->sequence = 2;

        $ordersblockInstance4->addField($field34);
    }

$field35 = Vtiger_Field::getInstance('orders_ltdate', $ordersInstance);
    if ($field35) {
        echo "<br> Field 'orders_ltdate' is already present. <br>";
    } else {
        $field35 = new Vtiger_Field();
        $field35->label = 'LBL_ORDERS_LTDATE';
        $field35->name = 'orders_ltdate';
        $field35->table = 'vtiger_orders';
        $field35->column = 'orders_ltdate';
        $field35->columntype = 'DATE';
        $field35->uitype = 5;
        $field35->typeofdata = 'D~O';
        $field35->sequence = 5;

        $ordersblockInstance4->addField($field35);
    }

$field36 = Vtiger_Field::getInstance('orders_dtdate', $ordersInstance);
    if ($field36) {
        echo "<br> Field 'dtdate' is already present. <br>";
    } else {
        $field36 = new Vtiger_Field();
        $field36->label = 'LBL_ORDERS_DTDATE';
        $field36->name = 'orders_dtdate';
        $field36->table = 'vtiger_orders';
        $field36->column = 'orders_dtdate';
        $field36->columntype = 'DATE';
        $field36->uitype = 5;
        $field36->typeofdata = 'D~O';
        $field36->sequence = 8;

        $ordersblockInstance4->addField($field36);
    }

$field37 = Vtiger_Field::getInstance('orders_surveyd', $ordersInstance);
    if ($field37) {
        echo "<br> Field 'orders_surveyd' is already present. <br>";
    } else {
        $field37 = new Vtiger_Field();
        $field37->label = 'LBL_ORDERS_SURVEYD';
        $field37->name = 'orders_surveyd';
        $field37->table = 'vtiger_orders';
        $field37->column = 'orders_surveyd';
        $field37->columntype = 'DATE';
        $field37->uitype = 5;
        $field37->typeofdata = 'D~O';
        $field37->sequence = 10;

        $ordersblockInstance4->addField($field37);
    }

$field38 = Vtiger_Field::getInstance('orders_surveyt', $ordersInstance);
    if ($field38) {
        echo "<br> Field 'orders_surveyt' is already present. <br>";
    } else {
        $field38 = new Vtiger_Field();
        $field38->label = 'LBL_ORDERS_SURVEYT';
        $field38->name = 'orders_surveyt';
        $field38->table = 'vtiger_orders';
        $field38->column = 'orders_surveyt';
        $field38->columntype = 'TIME';
        $field38->uitype = 14;
        $field38->typeofdata = 'T~O';
        $field38->sequence = 11;

        $ordersblockInstance4->addField($field38);
    }

$field39 = Vtiger_Field::getInstance('orders_ppdate', $ordersInstance);
    if ($field39) {
        echo "<br> Field 'orders_ppdate' is already present. <br>";
    } else {
        $field39 = new Vtiger_Field();
        $field39->label = 'LBL_ORDERS_PPDATE';
        $field39->name = 'orders_ppdate';
        $field39->table = 'vtiger_orders';
        $field39->column = 'orders_ppdate';
        $field39->columntype = 'DATE';
        $field39->uitype = 5;
        $field39->typeofdata = 'D~O';
        $field39->sequence = 3;

        $ordersblockInstance4->addField($field39);
    }

$field40 = Vtiger_Field::getInstance('orders_pldate', $ordersInstance);
    if ($field40) {
        echo "<br> Field 'orders_pldate' is already present. <br>";
    } else {
        $field40 = new Vtiger_Field();
        $field40->label = 'LBL_ORDERS_PLDATE';
        $field40->name = 'orders_pldate';
        $field40->table = 'vtiger_orders';
        $field40->column = 'orders_pldate';
        $field40->columntype = 'DATE';
        $field40->uitype = 5;
        $field40->typeofdata = 'D~O';
        $field40->sequence = 6;

        $ordersblockInstance4->addField($field40);
    }

$field41 = Vtiger_Field::getInstance('orders_pddate', $ordersInstance);
    if ($field41) {
        echo "<br> Field 'orders_pddate' is already present. <br>";
    } else {
        $field41 = new Vtiger_Field();
        $field41->label = 'LBL_ORDERS_PDDATE';
        $field41->name = 'orders_pddate';
        $field41->table = 'vtiger_orders';
        $field41->column = 'orders_pddate';
        $field41->columntype = 'DATE';
        $field41->uitype = 5;
        $field41->typeofdata = 'D~O';
        $field41->sequence = 9;

        $ordersblockInstance4->addField($field41);
    }

//add fields to address block

$field42 = Vtiger_Field::getInstance('origin_address1', $ordersInstance);
    if ($field42) {
        echo "<br> Field 'origin_address1' is already present. <br>";
    } else {
        $field42 = new Vtiger_Field();
        $field42->label = 'LBL_ORDERS_OADDRESS1';
        $field42->name = 'origin_address1';
        $field42->table = 'vtiger_orders';
        $field42->column = 'origin_address1';
        $field42->columntype = 'VARCHAR(220)';
        $field42->uitype = 1;
        $field42->typeofdata = 'V~O';
        $field42->sequence = 1;

        $ordersblockInstance2->addField($field42);
    }

$field43 = Vtiger_Field::getInstance('origin_address2', $ordersInstance);
    if ($field43) {
        echo "<br> Field 'origin_address2' is already present. <br>";
    } else {
        $field43 = new Vtiger_Field();
        $field43->label = 'LBL_ORDERS_OADDRESS2';
        $field43->name = 'origin_address2';
        $field43->table = 'vtiger_orders';
        $field43->column = 'origin_address2';
        $field43->columntype = 'VARCHAR(220)';
        $field43->uitype = 1;
        $field43->typeofdata = 'V~O';
        $field43->sequence = 3;

        $ordersblockInstance2->addField($field43);
    }

$field44 = Vtiger_Field::getInstance('origin_city', $ordersInstance);
    if ($field44) {
        echo "<br> Field 'origin_city' is already present. <br>";
    } else {
        $field44 = new Vtiger_Field();
        $field44->label = 'LBL_ORDERS_OCITY';
        $field44->name = 'origin_city';
        $field44->table = 'vtiger_orders';
        $field44->column = 'origin_city';
        $field44->columntype = 'VARCHAR(220)';
        $field44->uitype = 1;
        $field44->typeofdata = 'V~O';
        $field44->sequence = 5;

        $ordersblockInstance2->addField($field44);
    }

$field45 = Vtiger_Field::getInstance('origin_state', $ordersInstance);
    if ($field45) {
        echo "<br> Field 'origin_state' is already present. <br>";
    } else {
        $field45 = new Vtiger_Field();
        $field45->label = 'LBL_ORDERS_OSTATE';
        $field45->name = 'origin_state';
        $field45->table = 'vtiger_orders';
        $field45->column = 'origin_state';
        $field45->columntype = 'VARCHAR(220)';
        $field45->uitype = 1;
        $field45->typeofdata = 'V~O';
        $field45->sequence = 7;

        $ordersblockInstance2->addField($field45);
    }

$field46 = Vtiger_Field::getInstance('origin_zip', $ordersInstance);
    if ($field46) {
        echo "<br> Field 'origin_zip' is already present. <br>";
    } else {
        $field46 = new Vtiger_Field();
        $field46->label = 'LBL_ORDERS_OZIP';
        $field46->name = 'origin_zip';
        $field46->table = 'vtiger_orders';
        $field46->column = 'origin_zip';
        $field46->columntype = 'VARCHAR(220)';
        $field46->uitype = 1;
        $field46->typeofdata = 'V~O';
        $field46->sequence = 9;

        $ordersblockInstance2->addField($field46);
    }

$field47 = Vtiger_Field::getInstance('origin_country', $ordersInstance);
    if ($field47) {
        echo "<br> Field 'origin_country' is already present. <br>";
    } else {
        $field47 = new Vtiger_Field();
        $field47->label = 'LBL_ORDERS_OCOUNTRY';
        $field47->name = 'origin_country';
        $field47->table = 'vtiger_orders';
        $field47->column = 'origin_country';
        $field47->columntype = 'VARCHAR(220)';
        $field47->uitype = 1;
        $field47->typeofdata = 'V~O';
        $field47->sequence = 11;

        $ordersblockInstance2->addField($field47);
    }

$field48 = Vtiger_Field::getInstance('origin_phone1', $ordersInstance);
    if ($field48) {
        echo "<br> Field 'origin_phone1' is already present. <br>";
    } else {
        $field48 = new Vtiger_Field();
        $field48->label = 'LBL_ORDERS_OPHONE1';
        $field48->name = 'origin_phone1';
        $field48->table = 'vtiger_orders';
        $field48->column = 'origin_phone1';
        $field48->columntype = 'VARCHAR(50)';
        $field48->uitype = 11;
        $field48->typeofdata = 'V~O';
        $field48->sequence = 13;

        $ordersblockInstance2->addField($field48);
    }

$field49 = Vtiger_Field::getInstance('origin_phone2', $ordersInstance);
    if ($field49) {
        echo "<br> Field 'origin_phone2' is already present. <br>";
    } else {
        $field49 = new Vtiger_Field();
        $field49->label = 'LBL_ORDERS_OPHONE2';
        $field49->name = 'origin_phone2';
        $field49->table = 'vtiger_orders';
        $field49->column = 'origin_phone2';
        $field49->columntype = 'VARCHAR(50)';
        $field49->uitype = 11;
        $field49->typeofdata = 'V~O';
        $field49->sequence = 15;

        $ordersblockInstance2->addField($field49);
    }

$field50 = Vtiger_Field::getInstance('origin_description', $ordersInstance);
    if ($field50) {
        echo "<br> Field 'origin_description' is already present. <br>";
    } else {
        $field50 = new Vtiger_Field();
        $field50->label = 'LBL_ORDERS_ODESCRIPTION';
        $field50->name = 'origin_description';
        $field50->table = 'vtiger_orders';
        $field50->column = 'origin_description';
        $field50->columntype = 'VARCHAR(220)';
        $field50->uitype = 16;
        $field50->typeofdata = 'V~O';
        $field50->sequence = 17;

        $ordersblockInstance2->addField($field50);
        $field50->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));
    }

$field51 = Vtiger_Field::getInstance('destination_address1', $ordersInstance);
    if ($field51) {
        echo "<br> Field 'destination_address1' is already present. <br>";
    } else {
        $field51 = new Vtiger_Field();
        $field51->label = 'LBL_ORDERS_DADDRESS1';
        $field51->name = 'destination_address1';
        $field51->table = 'vtiger_orders';
        $field51->column = 'destination_address1';
        $field51->columntype = 'VARCHAR(220)';
        $field51->uitype = 1;
        $field51->typeofdata = 'V~O';
        $field51->sequence = 2;

        $ordersblockInstance2->addField($field51);
    }

$field52 = Vtiger_Field::getInstance('destination_address2', $ordersInstance);
    if ($field52) {
        echo "<br> Field 'destination_address2' is already present. <br>";
    } else {
        $field52 = new Vtiger_Field();
        $field52->label = 'LBL_ORDERS_DADDRESS2';
        $field52->name = 'destination_address2';
        $field52->table = 'vtiger_orders';
        $field52->column = 'destination_address2';
        $field52->columntype = 'VARCHAR(220)';
        $field52->uitype = 1;
        $field52->typeofdata = 'V~O';
        $field52->sequence = 4;

        $ordersblockInstance2->addField($field52);
    }

$field53 = Vtiger_Field::getInstance('destination_city', $ordersInstance);
    if ($field53) {
        echo "<br> Field 'destination_city' is already present. <br>";
    } else {
        $field53 = new Vtiger_Field();
        $field53->label = 'LBL_ORDERS_DCITY';
        $field53->name = 'destination_city';
        $field53->table = 'vtiger_orders';
        $field53->column = 'destination_city';
        $field53->columntype = 'VARCHAR(220)';
        $field53->uitype = 1;
        $field53->typeofdata = 'V~O';
        $field53->sequence = 6;

        $ordersblockInstance2->addField($field53);
    }

$field54 = Vtiger_Field::getInstance('destination_state', $ordersInstance);
    if ($field54) {
        echo "<br> Field 'destination_state' is already present. <br>";
    } else {
        $field54 = new Vtiger_Field();
        $field54->label = 'LBL_ORDERS_DSTATE';
        $field54->name = 'destination_state';
        $field54->table = 'vtiger_orders';
        $field54->column = 'destination_state';
        $field54->columntype = 'VARCHAR(220)';
        $field54->uitype = 1;
        $field54->typeofdata = 'V~O';
        $field54->sequence = 8;

        $ordersblockInstance2->addField($field54);
    }

$field55 = Vtiger_Field::getInstance('destination_zip', $ordersInstance);
    if ($field55) {
        echo "<br> Field 'destination_zip' is already present. <br>";
    } else {
        $field55 = new Vtiger_Field();
        $field55->label = 'LBL_ORDERS_DZIP';
        $field55->name = 'destination_zip';
        $field55->table = 'vtiger_orders';
        $field55->column = 'destination_zip';
        $field55->columntype = 'VARCHAR(220)';
        $field55->uitype = 1;
        $field55->typeofdata = 'V~O';
        $field55->sequence = 10;

        $ordersblockInstance2->addField($field55);
    }

$field56 = Vtiger_Field::getInstance('destination_country', $ordersInstance);
    if ($field56) {
        echo "<br> Field 'destination_country' is already present. <br>";
    } else {
        $field56 = new Vtiger_Field();
        $field56->label = 'LBL_ORDERS_DCOUNTRY';
        $field56->name = 'destination_country';
        $field56->table = 'vtiger_orders';
        $field56->column = 'destination_country';
        $field56->columntype = 'VARCHAR(220)';
        $field56->uitype = 1;
        $field56->typeofdata = 'V~O';
        $field56->sequence = 12;

        $ordersblockInstance2->addField($field56);
    }

$field57 = Vtiger_Field::getInstance('destination_phone1', $ordersInstance);
    if ($field57) {
        echo "<br> Field 'destination_phone1' is already present. <br>";
    } else {
        $field57 = new Vtiger_Field();
        $field57->label = 'LBL_ORDERS_DPHONE1';
        $field57->name = 'destination_phone1';
        $field57->table = 'vtiger_orders';
        $field57->column = 'destination_phone1';
        $field57->columntype = 'VARCHAR(50)';
        $field57->uitype = 11;
        $field57->typeofdata = 'V~O';
        $field57->sequence = 14;

        $ordersblockInstance2->addField($field57);
    }

$field58 = Vtiger_Field::getInstance('destination_phone2', $ordersInstance);
    if ($field58) {
        echo "<br> Field 'destination_phone2' is already present. <br>";
    } else {
        $field58 = new Vtiger_Field();
        $field58->label = 'LBL_ORDERS_DPHONE2';
        $field58->name = 'destination_phone2';
        $field58->table = 'vtiger_orders';
        $field58->column = 'destination_phone2';
        $field58->columntype = 'VARCHAR(50)';
        $field58->uitype = 11;
        $field58->typeofdata = 'V~O';
        $field58->sequence = 16;
 

        $ordersblockInstance2->addField($field58);
    }

$field59 = Vtiger_Field::getInstance('destination_description', $ordersInstance);
    if ($field59) {
        echo "<br> Field 'destination_description' is already present. <br>";
    } else {
        $field59 = new Vtiger_Field();
        $field59->label = 'LBL_ORDERS_DDESCRIPTION';
        $field59->name = 'destination_description';
        $field59->table = 'vtiger_orders';
        $field59->column = 'destination_description';
        $field59->columntype = 'VARCHAR(220)';
        $field59->uitype = 16;
        $field59->typeofdata = 'V~O';
        $field59->sequence = 18;

        $ordersblockInstance2->addField($field59);
        $field59->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));
    }

//add fields to invoice detail

$field60 = Vtiger_Field::getInstance('pricing_type', $ordersInstance);
    if ($field60) {
        echo "<br> Field 'pricing_type' is already present. <br>";
    } else {
        $field60 = new Vtiger_Field();
        $field60->label = 'LBL_ORDERS_PRICINGTYPE';
        $field60->name = 'pricing_type';
        $field60->table = 'vtiger_orders';
        $field60->column = 'pricing_type';
        $field60->columntype = 'VARCHAR(220)';
        $field60->uitype = 16;
        $field60->typeofdata = 'V~O';

        $ordersblockInstance3->addField($field60);
        $field60->setPicklistValues(array('Non Peak', 'Peak'));
    }

$field61 = Vtiger_Field::getInstance('bill_weight', $ordersInstance);
    if ($field61) {
        echo "<br> Field 'bill_weight' is already present. <br>";
    } else {
        $field61 = new Vtiger_Field();
        $field61->label = 'LBL_ORDERS_BILLWEIGHT';
        $field61->name = 'bill_weight';
        $field61->table = 'vtiger_orders';
        $field61->column = 'bill_weight';
        $field61->columntype = 'INT(50)';
        $field61->uitype = 7;
        $field61->typeofdata = 'I~O';

        $ordersblockInstance3->addField($field61);
    }

$field62 = Vtiger_Field::getInstance('estimate_type', $ordersInstance);
    if ($field62) {
        echo "<br> Field 'estimate_type' is already present. <br>";
    } else {
        $field62 = new Vtiger_Field();
        $field62->label = 'LBL_ORDERS_ESTIMATETYPE';
        $field62->name = 'estimate_type';
        $field62->table = 'vtiger_orders';
        $field62->column = 'estimate_type';
        $field62->columntype = 'VARCHAR(220)';
        $field62->uitype = 16;
        $field62->typeofdata = 'V~O';

        $ordersblockInstance3->addField($field62);
        $field62->setPicklistValues(array('Binding', 'Non-Binding', 'Not to Exceed'));
    }

$field63 = Vtiger_Field::getInstance('pricing_mode', $ordersInstance);
    if ($field63) {
        echo "<br> Field 'pricing_mode' is already present. <br>";
    } else {
        $field63 = new Vtiger_Field();
        $field63->label = 'LBL_ORDERS_PRICINGMODE';
        $field63->name = 'pricing_mode';
        $field63->table = 'vtiger_orders';
        $field63->column = 'pricing_mode';
        $field63->columntype = 'VARCHAR(220)';
        $field63->uitype = 1;
        $field63->typeofdata = 'V~O';

        $ordersblockInstance3->addField($field63);
    }

$field64 = Vtiger_Field::getInstance('payment_type', $ordersInstance);
    if ($field64) {
        echo "<br> Field 'payment_type' is already present. <br>";
    } else {
        $field64 = new Vtiger_Field();
        $field64->label = 'LBL_ORDERS_PAYTYPE';
        $field64->name = 'payment_type';
        $field64->table = 'vtiger_orders';
        $field64->column = 'payment_type';
        $field64->columntype = 'VARCHAR(220)';
        $field64->uitype = 16;
        $field64->typeofdata = 'V~O';

        $ordersblockInstance3->addField($field64);
        $field64->setPicklistValues(array('Check', 'Electronic Transfer', 'Credit', 'Cash'));
    }

$field65 = Vtiger_Field::getInstance('invoice_status', $ordersInstance);
    if ($field65) {
        echo "<br> Field 'invoice_status' is already present. <br>";
    } else {
        $field65 = new Vtiger_Field();
        $field65->label = 'LBL_ORDERS_INVOICESTATUS';
        $field65->name = 'invoice_status';
        $field65->table = 'vtiger_orders';
        $field65->column = 'invoice_status';
        $field65->columntype = 'VARCHAR(220)';
        $field65->uitype = 16;
        $field65->typeofdata = 'V~O';

        $ordersblockInstance3->addField($field65);
        $field65->setPicklistValues(array('Created', 'Cancel', 'Approved', 'Sent', 'Paid'));
    }

//add fields in Order weigths block

$field66 = Vtiger_Field::getInstance('orders_eweight', $ordersInstance);
    if ($field66) {
        echo "<br> Field 'orders_eweight' is already present. <br>";
    } else {
        $field66 = new Vtiger_Field();
        $field66->label = 'LBL_ORDERS_EWEIGHT';
        $field66->name = 'orders_eweight';
        $field66->table = 'vtiger_orders';
        $field66->column = 'orders_eweight';
        $field66->columntype = 'INT(50)';
        $field66->uitype = 7;
        $field66->typeofdata = 'I~O';
        $field66->sequence = 1;

        $ordersblockInstance5->addField($field66);
    }

$field67 = Vtiger_Field::getInstance('orders_ecube', $ordersInstance);
    if ($field67) {
        echo "<br> Field 'orders_ecube' is already present. <br>";
    } else {
        $field67 = new Vtiger_Field();
        $field67->label = 'LBL_ORDERS_ECUBE';
        $field67->name = 'orders_ecube';
        $field67->table = 'vtiger_orders';
        $field67->column = 'orders_ecube';
        $field67->columntype = 'INT(50)';
        $field67->uitype = 7;
        $field67->typeofdata = 'I~O';
        $field67->sequence = 4;

        $ordersblockInstance5->addField($field67);
    }

$field68 = Vtiger_Field::getInstance('orders_pcount', $ordersInstance);
    if ($field68) {
        echo "<br> Field 'orders_pcount' is already present. <br>";
    } else {
        $field68 = new Vtiger_Field();
        $field68->label = 'LBL_ORDERS_PCOUNT';
        $field68->name = 'orders_pcount';
        $field68->table = 'vtiger_orders';
        $field68->column = 'orders_pcount';
        $field68->columntype = 'INT(50)';
        $field68->uitype = 7;
        $field68->typeofdata = 'I~O';
        $field68->sequence = 7;

        $ordersblockInstance5->addField($field68);
    }

$field69 = Vtiger_Field::getInstance('orders_aweight', $ordersInstance);
    if ($field69) {
        echo "<br> Field 'orders_aweight' is already present. <br>";
    } else {
        $field69 = new Vtiger_Field();
        $field69->label = 'LBL_ORDERS_AWEIGHT';
        $field69->name = 'orders_aweight';
        $field69->table = 'vtiger_orders';
        $field69->column = 'orders_aweight';
        $field69->columntype = 'INT(50)';
        $field69->uitype = 7;
        $field69->typeofdata = 'I~O';
        $field69->sequence = 10;

        $ordersblockInstance5->addField($field69);
    }

$field70 = Vtiger_Field::getInstance('orders_gweight', $ordersInstance);
    if ($field70) {
        echo "<br> Field 'orders_gweight' is already present. <br>";
    } else {
        $field70 = new Vtiger_Field();
        $field70->label = 'LBL_ORDERS_GWEIGHT';
        $field70->name = 'orders_gweight';
        $field70->table = 'vtiger_orders';
        $field70->column = 'orders_gweight';
        $field70->columntype = 'INT(50)';
        $field70->uitype = 7;
        $field70->typeofdata = 'I~O';
        $field70->sequence = 2;

        $ordersblockInstance5->addField($field70);
    }

$field71 = Vtiger_Field::getInstance('orders_tweight', $ordersInstance);
    if ($field71) {
        echo "<br> Field 'orders_tweight' is already present. <br>";
    } else {
        $field71 = new Vtiger_Field();
        $field71->label = 'LBL_ORDERS_TWEIGHT';
        $field71->name = 'orders_tweight';
        $field71->table = 'vtiger_orders';
        $field71->column = 'orders_tweight';
        $field71->columntype = 'INT(50)';
        $field71->uitype = 7;
        $field71->typeofdata = 'I~O';
        $field71->sequence = 5;

        $ordersblockInstance5->addField($field71);
    }

$field72 = Vtiger_Field::getInstance('orders_netweight', $ordersInstance);
    if ($field72) {
        echo "<br> Field 'orders_netweight' is already present. <br>";
    } else {
        $field72 = new Vtiger_Field();
        $field72->label = 'LBL_ORDERS_NETWEIGHT';
        $field72->name = 'orders_netweight';
        $field72->table = 'vtiger_orders';
        $field72->column = 'orders_netweight';
        $field72->columntype = 'INT(50)';
        $field72->uitype = 7;
        $field72->typeofdata = 'I~O';
        $field72->sequence = 8;

        $ordersblockInstance5->addField($field72);
    }

$field73 = Vtiger_Field::getInstance('orders_minweight', $ordersInstance);
    if ($field73) {
        echo "<br> Field 'orders_minweight' is already present. <br>";
    } else {
        $field73 = new Vtiger_Field();
        $field73->label = 'LBL_ORDERS_MINWEIGHT';
        $field73->name = 'orders_minweight';
        $field73->table = 'vtiger_orders';
        $field73->column = 'orders_minweight';
        $field73->columntype = 'INT(50)';
        $field73->uitype = 7;
        $field73->typeofdata = 'I~O';
        $field73->sequence = 11;

        $ordersblockInstance5->addField($field73);
    }

$field74 = Vtiger_Field::getInstance('orders_rgweight', $ordersInstance);
    if ($field74) {
        echo "<br> Field 'orders_rgweight' is already present. <br>";
    } else {
        $field74 = new Vtiger_Field();
        $field74->label = 'LBL_ORDERS_RGWEIGHT';
        $field74->name = 'orders_rgweight';
        $field74->table = 'vtiger_orders';
        $field74->column = 'orders_rgweight';
        $field74->columntype = 'INT(50)';
        $field74->uitype = 7;
        $field74->typeofdata = 'I~O';
        $field74->sequence = 3;

        $ordersblockInstance5->addField($field74);
    }

$field75 = Vtiger_Field::getInstance('orders_rtweight', $ordersInstance);
    if ($field75) {
        echo "<br> Field 'orders_rtweight' is already present. <br>";
    } else {
        $field75 = new Vtiger_Field();
        $field75->label = 'LBL_ORDERS_RTWEIGHT';
        $field75->name = 'orders_rtweight';
        $field75->table = 'vtiger_orders';
        $field75->column = 'orders_rtweight';
        $field75->columntype = 'INT(50)';
        $field75->uitype = 7;
        $field75->typeofdata = 'I~O';
        $field75->sequence = 6;

        $ordersblockInstance5->addField($field75);
    }

$field76 = Vtiger_Field::getInstance('orders_rnetweight', $ordersInstance);
    if ($field76) {
        echo "<br> Field 'orders_rnetweight' is already present. <br>";
    } else {
        $field76 = new Vtiger_Field();
        $field76->label = 'LBL_ORDERS_RNETWEIGHT';
        $field76->name = 'orders_rnetweight';
        $field76->table = 'vtiger_orders';
        $field76->column = 'orders_rnetweight';
        $field76->columntype = 'INT(50)';
        $field76->uitype = 7;
        $field76->typeofdata = 'I~O';
        $field76->sequence = 9;

        $ordersblockInstance5->addField($field76);
    }


$ordersblockInstance8 = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $ordersInstance);
    if ($ordersblockInstance8) {
        echo "<br> block 'LBL_LONGDISPATCH_INFO' already exists.<br>";
    } else {
        $ordersblockInstance8  = new Vtiger_Block();
        $ordersblockInstance8 ->label = 'LBL_LONGDISPATCH_INFO';
        $ordersInstance ->addBlock($ordersblockInstance8);
    }


        $field77 = Vtiger_Field::getInstance('orders_onhold', $ordersInstance);
        
if ($field77) {
    echo "<br> Field 'orders_onhold' is already present. <br>";
} else {
    $field77 = new Vtiger_Field();
    $field77->label = 'On Hold';
    $field77->name = 'orders_onhold';
    $field77->table = 'vtiger_orders';
    $field77->column = 'orders_onhold';
    $field77->columntype = 'VARCHAR(3)';
    $field77->uitype = 56;
    $field77->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field77);
}

        $field78 = Vtiger_Field::getInstance('orders_apu', $ordersInstance);

if ($field78) {
    echo "<br> Field 'orders_onhold' is already present. <br>";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'Agent PickUp';
    $field78->name = 'orders_apu';
    $field78->table = 'vtiger_orders';
    $field78->column = 'orders_apu';
    $field78->columntype = 'VARCHAR(3)';
    $field78->uitype = 56;
    $field78->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field78);
}

 $field79 = Vtiger_Field::getInstance('orders_assignedtrip', $ordersInstance);

if ($field79) {
    echo "<br> Field 'orders_assignedtrip' is already present. <br>";
} else {
    $field79 = new Vtiger_Field();
    $field79->label = 'Assigned Trip';
    $field79->name = 'orders_assignedtrip';
    $field79->table = 'vtiger_orders';
    $field79->column = 'orders_assignedtrip';
    $field79->columntype = 'VARCHAR(3)';
    $field79->uitype = 56;
    $field79->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field79);
}

 $field80 = Vtiger_Field::getInstance('orders_trip', $ordersInstance);

if ($field80) {
    echo "<br> Field 'orders_trip' is already present. <br>";
} else {
    $field80 = new Vtiger_Field();
    $field80->label = 'Trip Id';
    $field80->name = 'orders_trip';
    $field80->table = 'vtiger_orders';
    $field80->column = 'orders_trip';
    $field80->columntype = 'VARCHAR(15)';
    $field80->uitype = 10;
    $field80->typeofdata = 'V~O';

    $ordersblockInstance8->addField($field80);
    $field80->setRelatedModules(array('Trips'));
}

 $field81 = Vtiger_Field::getInstance('orders_pudate', $ordersInstance);

if ($field81) {
    echo "<br> Field 'orders_trip' is already present. <br>";
} else {
    $field81 = new Vtiger_Field();
    $field81->label = 'PickUp Date';
    $field81->name = 'orders_pudate';
    $field81->table = 'vtiger_orders';
    $field81->column = 'orders_pudate';
    $field81->columntype = 'DATE';
    $field81->uitype = 5;
    $field81->typeofdata = 'D~O';

    $ordersblockInstance8->addField($field81);
}

 $field82 = Vtiger_Field::getInstance('orders_pudate', $ordersInstance);

if ($field82) {
    echo "<br> Field 'orders_actualpudate' is already present. <br>";
} else {
    $field82 = new Vtiger_Field();
    $field82->label = 'Actual Pickup Date';
    $field82->name = 'orders_actualpudate';
    $field82->table = 'vtiger_orders';
    $field82->column = 'orders_actualpudate';
    $field82->columntype = 'DATE';
    $field82->uitype = 5;
    $field82->typeofdata = 'D~O';

    $ordersblockInstance8->addField($field82);
}

$ordersblockInstance8->save($ordersInstance);

$ordersblockInstance2 = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS', $ordersInstance);
    if ($ordersblockInstance2) {
        echo "<br> block 'LBL_ORDERS_ORIGINADDRESS' already exists.<br>";
    } else {
        $ordersblockInstance2 = new Vtiger_Block();
        $ordersblockInstance2->label = 'LBL_ORDERS_ORIGINADDRESS';
        $ordersInstance->addBlock($ordersblockInstance2);
    }
  
        
$field83 = Vtiger_Field::getInstance('origin_zone', $ordersInstance);

if ($field83) {
    echo "<br> Field 'origin_zone' is already present. <br>";
} else {
    $field83 = new Vtiger_Field();
    $field83->label = 'Origin Zone';
    $field83->name = 'origin_zone';
    $field83->table = 'vtiger_orders';
    $field83->column = 'origin_zone';
    $field83->columntype = 'VARCHAR(150)';
    $field83->uitype = 16;
    $field83->typeofdata = 'V~O';
    
    $ordersblockInstance2->addField($field83);
}


$field84 = Vtiger_Field::getInstance('empty_zone', $ordersInstance);

if ($field84) {
    echo "<br> Field 'empty_zone' is already present. <br>";
} else {
    $field84 = new Vtiger_Field();
    $field84->label = 'Destination Zone';
    $field84->name = 'empty_zone';
    $field84->table = 'vtiger_orders';
    $field84->column = 'empty_zone';
    $field84->columntype = 'VARCHAR(150)';
    $field84->uitype = 16;
    $field84->typeofdata = 'V~O';

    $ordersblockInstance2->addField($field84);
}



$field85 = Vtiger_Field::getInstance('business_line', $ordersInstance);

if ($field85) {
    echo "<br> Field 'orders_rnetweight' is already present. <br>";
} else {
    $field85 = new Vtiger_Field();
    $field85->label = 'LBL_ORDERS_BUSINESSLINE';
    $field85->name = 'business_line';
    $field85->table = 'vtiger_orders';
    $field85->column = 'business_line';
    $field85->columntype = 'VARCHAR(150)';
    $field85->uitype = 16;
    $field85->typeofdata = 'V~O';

    $ordersblockInstance2->addField($field85);
}

$field86 = Vtiger_Field::getInstance('billing_type', $ordersInstance);
if ($field86) {
    echo "Field billing_type already exists in Orders module<br />";
} else {
    $field86 = new Vtiger_Field();
    $field86->label = 'LBL_ORDERS_BILLINGTYPE';
    $field86->name = 'billing_type';
    $field86->table = 'vtiger_orders';
    $field86->column = 'billing_type';
    $field86->columntype = 'VARCHAR(255)';
    $field86->uitype = 16;
    $field86->typeofdata = 'V~O';
    
    $ordersblockInstance2->addField($field86);
}

//add filter in orders module
$filter1 = Vtiger_Filter::getInstance('All', $ordersInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $ordersInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field77, 1)->addField($field12, 2)->addField($field24, 3)->addField($field18, 4);
    }
        
        //add filter in orders module -- LDD Unassigned
$filter2 = Vtiger_Filter::getInstance('LDD Un Assigned', $ordersInstance);
    if ($filter2) {
        echo "<br> Filter exists <br>";
    } else {
        $filter2 = new Vtiger_Filter();
        $filter2->name = 'LDD Un Assigned';
        $filter2->isdefault = false;
        $ordersInstance->addFilter($filter2);

        $filter2->addField($field32)->addField($field35, 1)->addField($field33, 2)->addField($field36, 3)->addField($field9, 4)->addField($field80, 5)->addField($field85, 6)->addField($field7, 7)->addField($field44, 8)->addField($field45, 9)->addField($field53, 10)->addField($field53, 11)->addField($field54, 12)->addField($field66, 13)->addField($field67, 14)->addField($field25, 15);
    
        $filter2->addRule($field79, 'EQUALS', '0')->addRule($field85, 'EQUALS', 'Interstate Move');
    }
        
        $filter3 = Vtiger_Filter::getInstance('LDD Assigned', $ordersInstance);
    if ($filter3) {
        echo "<br> Filter exists <br>";
    } else {
        $filter3 = new Vtiger_Filter();
        $filter3->name = 'LDD Assigned';
        $filter3->isdefault = false;
        $ordersInstance->addFilter($filter3);

        $filter3->addField($field32)->addField($field35, 1)->addField($field33, 2)->addField($field36, 3)->addField($field9, 4)->addField($field80, 5)->addField($field85, 6)->addField($field7, 7)->addField($field44, 8)->addField($field45, 9)->addField($field53, 10)->addField($field53, 11)->addField($field54, 12)->addField($field66, 13)->addField($field67, 14)->addField($field25, 15);
    
        $filter3->addRule($field79, 'EQUALS', '1')->addRule($field85, 'EQUALS', 'Interstate Move');
    }

//START Add navigation link in module
/*
 //Adds a a row in the vtiger_modtracker_tabs for "updates" in the navigation Bar
ModTracker::enableTrackingForModule($moduleInstance->id); */

//create comments relashionship and widget
//require_once 'vtlib/Vtiger/Module.php';
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Orders'));

//require_once 'modules/ModComments/ModComments.php';
$detailviewblock = ModComments::addWidgetTo('Orders');

//START Add navigation link in module opportunities to orders
/*$opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders',Array('ADD'),'get_dependents_list');
//END Add navigation link in module*/

//START Add navigation link in module orders to orderstask
$ordersInstance = Vtiger_Module::getInstance('Orders');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('OrdersTask'), 'Orders Task', array('ADD'), 'get_dependents_list');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('MoveRoles'), 'MoveRoles', array('ADD'), 'get_dependents_list');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('Stops'), 'Stops', array('ADD'), 'get_dependents_list');
//END Add navigation link in module

//START Add navigation link in module orders to orderstask
$ordersInstance1 = Vtiger_Module::getInstance('Orders');
$ordersInstance1->setRelatedList(Vtiger_Module::getInstance('OrdersMilestone'), 'Orders Milestone', array('ADD'), 'get_dependents_list');
$ordersInstance1->setRelatedList(Vtiger_Module::getInstance('Estimates'), 'Estimates', array('add'), 'get_related_list');
//END Add navigation link in module

//START Add navigation link in module orders to orderstask
$ordersInstance2 = Vtiger_Module::getInstance('Orders');
$ordersInstance2->setRelatedList(Vtiger_Module::getInstance('HelpDesk'), 'HelpDesk', array('ADD', 'SELECT'), 'get_related_list');
//END Add navigation link in module

//START Add navigation link in module orders to orderstask
$ordersInstance3 = Vtiger_Module::getInstance('Orders');
$ordersInstance3->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD', 'SELECT'), 'get_attachments');
//END Add navigation link in module

//include_once('vtlib/Vtiger/Module.php');
$moduleInstance = Vtiger_Module::getInstance('Orders');
$moduleInstance->addLink('DETAILVIEWBASIC', 'Add Orders Task', 'index.php?module=OrdersTask&action=EditView&ordersid=$RECORD$&return_module=Orders&return_action=DetailView&return_id=$RECORD$');

//include_once('vtlib/Vtiger/Module.php');
$moduleInstance1 = Vtiger_Module::getInstance('Orders');
$moduleInstance1->addLink('DETAILVIEWBASIC', 'Add Note', 'index.php?module=Documents&action=EditView&return_module=Orders&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$');


$stops = Vtiger_Module::getInstance('Stops');
$stopsblock = Vtiger_Block::getInstance('LBL_STOPS_INFORMATION', $stops);

$field054 = Vtiger_Field::getInstance('stop_opp', $module1);
if ($field054) {
    echo "<li>The stop_opp field already exists</li><br> \n";
} else {
    $field054 = new Vtiger_Field();
    $field054->label = 'LBL_STOPS_ORDER';
    $field054->name = 'stop_order';
    $field054->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
    $field054->column = 'stop_order';   //  This will be the columnname in your database for the new field.
    $field054->columntype = 'VARCHAR(255)';
    $field054->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field054->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $stopsblock->addField($field054);
    $field054->setRelatedModules(array('Orders'));
}

//Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = ". $field13->id ." AND block = ". $ordersblockInstance1->id);
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";