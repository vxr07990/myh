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

// Create AgentCompensationItems module
$isNew=false;
$moduleInstance = Vtiger_Module::getInstance('AgentCompensationItems');
if($moduleInstance)
{
    echo "<h2>AgentCompensationItems already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AgentCompensationItems';
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

//
$field0 = Vtiger_Field::getInstance('agcomitem_name', $moduleInstance);
if($field0) {
    echo "<br> The agcomitem_name field already exists in Escrows <br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_NAME';
    $field0->name = 'agcomitem_name';
    $field0->table = 'vtiger_agentcompensationitems';
    $field0->column ='agcomitem_name';
    $field0->columntype = 'VARCHAR(200)';
    $field0->uitype = 1;
    $field0->typeofdata = 'V~O';
    $blockInstance1->addField($field0);
    $moduleInstance->setEntityIdentifier($field0);
}

//Booker Distribution
$field1 = Vtiger_Field::getInstance('agcomitem_bookerdistribution', $moduleInstance);
if($field1) {
    echo "<br> The agcomitem_bookerdistribution field already exists in Escrows <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_BOOKER_DISTRIBUTION';
    $field1->name = 'agcomitem_bookerdistribution';
    $field1->table = 'vtiger_agentcompensationitems';
    $field1->column ='agcomitem_bookerdistribution';
    $field1->columntype = 'DECIMAL(5,2)';
    $field1->uitype = 9;
    $field1->typeofdata = 'N~O';
    $blockInstance1->addField($field1);
}

//Origin Distribution
$field2 = Vtiger_Field::getInstance('agcomitem_origindistribution', $moduleInstance);
if($field2) {
    echo "<br> The agcomitem_origindistribution field already exists in Escrows <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ORIGIN_DISTRIBUTION';
    $field2->name = 'agcomitem_origindistribution';
    $field2->table = 'vtiger_agentcompensationitems';
    $field2->column ='agcomitem_origindistribution';
    $field2->columntype = 'DECIMAL(5,2)';
    $field2->uitype = 9;
    $field2->typeofdata = 'N~O';
    $blockInstance1->addField($field2);
}


//Hauling Distribution
$field3 = Vtiger_Field::getInstance('agcomitem_haulingdistribution', $moduleInstance);
if($field3) {
    echo "<br> The agcomitem_haulingdistribution field already exists in Escrows <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_HAULING_DISTRIBUTION';
    $field3->name = 'agcomitem_haulingdistribution';
    $field3->table = 'vtiger_agentcompensationitems';
    $field3->column ='agcomitem_haulingdistribution';
    $field3->columntype = 'DECIMAL(5,2)';
    $field3->uitype = 9;
    $field3->typeofdata = 'N~O';
    $blockInstance1->addField($field3);
}


//General Office Distribution
$field4 = Vtiger_Field::getInstance('agcomitem_general_officedistribution', $moduleInstance);
if($field4) {
    echo "<br> The agcomitem_general_officedistribution field already exists in Escrows <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_GENERAL_OFFICE_DISTRIBUTION';
    $field4->name = 'agcomitem_general_officedistribution';
    $field4->table = 'vtiger_agentcompensationitems';
    $field4->column ='agcomitem_general_officedistribution';
    $field4->columntype = 'DECIMAL(5,2)';
    $field4->uitype = 9;
    $field4->typeofdata = 'N~O';
    $blockInstance1->addField($field4);
}


//Distribution
$field5 = Vtiger_Field::getInstance('agcomitem_distribution', $moduleInstance);
if($field5) {
    echo "<br> The agcomitem_distribution field already exists in Escrows <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_DISTRIBUTION';
    $field5->name = 'agcomitem_distribution';
    $field5->table = 'vtiger_agentcompensationitems';
    $field5->column ='agcomitem_distribution';
    $field5->columntype = 'DECIMAL(5,2)';
    $field5->uitype = 9;
    $field5->typeofdata = 'N~O';
    $blockInstance1->addField($field5);
}


//Related Field to AgentCompensationGroup
$field11 = Vtiger_Field::getInstance('agcomitem_agentcompgr', $moduleInstance);
if($field11) {
    echo "<br> The agcomitem_agentcompgr field already exists in Escrows <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_AGENTCOMPENSATIONGROUP';
    $field11->name = 'agcomitem_agentcompgr';
    $field11->table = 'vtiger_agentcompensationitems';
    $field11->column ='agcomitem_agentcompgr';
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

    $filter1->addField($field0)->addField($field1,1)->addField($field2, 2)->addField($field3, 3)->addField($field4, 4)->addField($field5, 5);

    // Add Agent Compensation Items to Admin Table / CRM Settings (OT Item 3319)
    $adb->pquery("UPDATE vtiger_tab SET parent = '',tabsequence = '-1' WHERE `name` ='AgentCompensationItems'");
    $max_id = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`,`pinned`) VALUES (?, ?, ?, ?, ?, ?,?)", array($max_id, '4', 'AgentCompensationItems', 'Agent Compensation Items', 'index.php?module=AgentCompensationItems&view=List', $max_id, '1'));
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";