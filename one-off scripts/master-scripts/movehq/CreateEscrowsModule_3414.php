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
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');
$Vtiger_Utils_Log = true;

$adb = PearDatabase::getInstance();

// Create Escrows module
$isNew=false;
$moduleInstance = Vtiger_Module::getInstance('Escrows');
if($moduleInstance)
{
    echo "<h2>Escrows already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Escrows';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew= true;
}


$blockInstance1 = Vtiger_Block::getInstance('LBL_DETAIL',$moduleInstance);

if($blockInstance1)
{
    echo "<h3>The LBL_DETAIL block already exists</h3><br> \n";
}
else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_DETAIL';
    $moduleInstance->addBlock($blockInstance1);
}

//Description Field
$field1 = Vtiger_Field::getInstance('escrows_desc', $moduleInstance);
if($field1) {
    echo "<br> The escrows_desc field already exists in Escrows <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_DESCRIPTION';
    $field1->name = 'escrows_desc';
    $field1->table = 'vtiger_escrows';
    $field1->column ='escrows_desc';
    $field1->columntype = 'varchar(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = '1';

    $blockInstance1->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}


//Status Field
$field2 = Vtiger_Field::getInstance('escrows_status', $moduleInstance);
if($field2) {
    echo "<br> The escrows_status field already exists in Escrows <br>";
    // Update default value
    $adb->pquery("update `vtiger_field` set `defaultvalue`='Active' where `fieldid`=?;", array($field2->id));
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_STATUS';
    $field2->name = 'escrows_status';
    $field2->table = 'vtiger_escrows';
    $field2->column ='escrows_status';
    $field2->columntype = 'varchar(10)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~M';
    $field2->defaultvalue = 'Active';
    $blockInstance1->addField($field2);
    $field2->setPicklistValues(array('Active','Inactive'));

}

//Calculation Type Field
$field3 = Vtiger_Field::getInstance('escrows_calculation_type', $moduleInstance);
if($field3) {
    echo "<br> The escrows_calculation_type field already exists in Escrows <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CALCULATION_TYPE';
    $field3->name = 'escrows_calculation_type';
    $field3->table = 'vtiger_escrows';
    $field3->column ='escrows_calculation_type';
    $field3->columntype = 'varchar(20)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~M';

    $blockInstance1->addField($field3);
    $field3->setPicklistValues(array('Amount','Percent','Flat Rate','Weight-CWT'));

}

//Pct / Amount
$field4 = Vtiger_Field::getInstance('escrows_pct_amount', $moduleInstance);
if($field4) {
    echo "<br> The escrows_pct_amount field already exists in Escrows <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_PCT_AMOUNT';
    $field4->name = 'escrows_pct_amount';
    $field4->table = 'vtiger_escrows';
    $field4->column ='escrows_pct_amount';
    $field4->columntype = 'DECIMAL(10,2)';
    $field4->uitype = 7;
    $field4->typeofdata = 'N~M';
    $blockInstance1->addField($field4);
}

//Chargeback From Field
$field5 = Vtiger_Field::getInstance('escrows_chargeback_from', $moduleInstance);
if($field5) {
    echo "<br> The escrows_chargeback_from field already exists in Escrows <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CHARGEBACK_FROM';
    $field5->name = 'escrows_chargeback_from';
    $field5->table = 'vtiger_escrows';
    $field5->column ='escrows_chargeback_from';
    $field5->columntype = 'varchar(50)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~M';

    $blockInstance1->addField($field5);
    $field5->setPicklistValues(array('All Agents','Booking Agent','Destination Agent','Hauling Agent','SIT Agent','Invoicing Agent','Distributing Agent', 'General Office'));

}

//Discount Type Field
$field6 = Vtiger_Field::getInstance('escrows_discount_type', $moduleInstance);
if($field6) {
    echo "<br> The escrows_discount_type field already exists in Escrows <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_DISCOUNT_TYPE';
    $field6->name = 'escrows_discount_type';
    $field6->table = 'vtiger_escrows';
    $field6->column ='escrows_discount_type';
    $field6->columntype = 'varchar(50)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance1->addField($field6);
    $field6->setPicklistValues(array('Net','Gross','Flat Amount','Distributed Revenue'));
}

//Chargeback Type Field
$field7 = Vtiger_Field::getInstance('escrows_chargeback_type', $moduleInstance);
if($field7) {
    echo "<br> The escrows_discount_type field already exists in Escrows <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CHARGEBACK_TYPE';
    $field7->name = 'escrows_chargeback_type';
    $field7->table = 'vtiger_escrows';
    $field7->column ='escrows_chargeback_type';
    $field7->columntype = 'varchar(50)';
    $field7->uitype = 16;
    $field7->typeofdata = 'V~O';

    $blockInstance1->addField($field7);
    $field7->setPicklistValues(array('All'));
}

//Chargeback To Field
$field8 = Vtiger_Field::getInstance('escrows_chargeback_to', $moduleInstance);
if($field8) {
    echo "<br> The escrows_chargeback_to field already exists in Escrows <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CHARGEBACK_TO';
    $field8->name = 'escrows_chargeback_to';
    $field8->table = 'vtiger_escrows';
    $field8->column ='escrows_chargeback_to';
    $field8->columntype = 'varchar(50)';
    $field8->uitype = 16;
    $field8->typeofdata = 'V~O';

    $blockInstance1->addField($field8);
    $field8->setPicklistValues(array('All Agents','Booking Agent','Destination Agent','Hauling Agent','SIT Agent','Invoicing Agent','Distributing Agent', 'General Office'));

}

//From Item Code Field
$field9 = Vtiger_Field::getInstance('escrows_from_itemcode', $moduleInstance);
if($field9) {
    echo "<br> The escrows_from_itemcode field already exists in Escrows <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_FROM_ITEMCODE';
    $field9->name = 'escrows_from_itemcode';
    $field9->table = 'vtiger_escrows';
    $field9->column ='escrows_from_itemcode';
    $field9->columntype = 'INT(10)';
    $field9->uitype = 10;
    $field9->typeofdata = 'V~O';
    $field9->summaryfield = '1';

    $blockInstance1->addField($field9);
    $field9->setRelatedModules(array('ItemCodes'));
}

//To Item Code Field
$field10 = Vtiger_Field::getInstance('escrows_to_itemcode', $moduleInstance);
if($field10) {
    echo "<br> The escrows_to_itemcode field already exists in Escrows <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_TO_ITEMCODE';
    $field10->name = 'escrows_to_itemcode';
    $field10->table = 'vtiger_escrows';
    $field10->column ='escrows_to_itemcode';
    $field10->columntype = 'INT(10)';
    $field10->uitype = 10;
    $field10->typeofdata = 'V~O';
    $field10->summaryfield = '1';

    $blockInstance1->addField($field10);
    $field10->setRelatedModules(array('ItemCodes'));
}

//Related Field to AgentCompensationGroup
$field11 = Vtiger_Field::getInstance('escrows_agentcompgr', $moduleInstance);
if($field11) {
    echo "<br> The escrows_agentcompgr field already exists in Escrows <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_TO_ITEMCODE';
    $field11->name = 'escrows_agentcompgr';
    $field11->table = 'vtiger_escrows';
    $field11->column ='escrows_agentcompgr';
    $field11->columntype = 'INT(10)';
    $field11->uitype = 10;
    $field11->typeofdata = 'V~M';
    $field11->summaryfield = '1';

    $blockInstance1->addField($field11);
    $field11->setRelatedModules(array('AgentCompensationGroup'));
}

if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4);

    // Add Escrows to Admin Table / CRM Settings (OT Item 3319)
    $adb->pquery("UPDATE vtiger_tab SET parent = '',tabsequence = '-1' WHERE `name` ='Escrows'");
    $max_id = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`,`pinned`) VALUES (?, ?, ?, ?, ?, ?,?)", array($max_id, '4', 'Escrows', 'Escrows', 'index.php?module=Escrows&view=List', $max_id, '1'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";