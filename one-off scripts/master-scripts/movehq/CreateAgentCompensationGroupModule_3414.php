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

$isNew=false;
$moduleInstance = Vtiger_Module::getInstance('AgentCompensationGroup');
if($moduleInstance)
{
    echo "<h2>Agent Compensation Group already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AgentCompensationGroup';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew= true;
}


$blockInstance1 = Vtiger_Block::getInstance('LBL_AGENTCOMPENSATION_GROUP',$moduleInstance);

if($blockInstance1)
{
    echo "<h3>The LBL_AGENTCOMPENSATION_GROUP block already exists</h3><br> \n";
}
else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_AGENTCOMPENSATION_GROUP';
    $moduleInstance->addBlock($blockInstance1);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_RECORDUPDATEINFORMATION',$moduleInstance);

if($blockInstance2)
{
    echo "<h3>The LBL_RECORDUPDATEINFORMATION block already exists</h3><br> \n";
}
else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_RECORDUPDATEINFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

// Agent Compensation Group
//Agent Compensation Field
$field1 = Vtiger_Field::getInstance('agentcompensation_id', $moduleInstance);
if($field1) {
    echo "<br> The agentcompensation_name field already exists in Agent Compensation Group <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_AGENTCOMPENSATION';
    $field1->name = 'agentcompensation_id';
    $field1->table = 'vtiger_agentcompensationgroup';
    $field1->column ='agentcompensation_id';
    $field1->columntype = 'varchar(100)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = '1';

    $blockInstance1->addField($field1);
    $field1->setRelatedModules(array('AgentCompensation'));
}

//Owner Field
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if($field2) {
    echo "<br> The agentid field already exists in Agent Compensation Group <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'Owner';
    $field2->name       = 'agentid';
    $field2->table      = 'vtiger_crmentity';
    $field2->column     = 'agentid';
    $field2->columntype = 'INT(10)';
    $field2->uitype     = 1002;
    $field2->typeofdata = 'I~M';

    $blockInstance1->addField($field2);
}
//Business Line	 Field
$field3 = Vtiger_Field::getInstance('agentcompgr_businessline', $moduleInstance);
if($field3) {
    echo "<br> The agentcompgr_businessline field already exists in Agent Compensation Group <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_BUSINESSLINE';
    $field3->name = 'agentcompgr_businessline';
    $field3->table = 'vtiger_agentcompensationgroup';
    $field3->column ='agentcompgr_businessline';
    $field3->columntype = 'text';
    $field3->uitype = 3333;
    $field3->typeofdata = 'V~M';

    $blockInstance1->addField($field3);

    $picklistvalues = array(
        'HHG - Interstate',
        'HHG - Intrastate',
        'HHG - Local',
        'HHG - International',
        'Electronics - Interstate',
        'Electronics - Intrastate',
        'Electronics - Local',
        'Electronics - International',
        'Display & Exhibits - Interstate',
        'Display & Exhibits - Intrastate',
        'Display & Exhibits - Local',
        'Display & Exhibits - International',
        'General Commodities - Interstate',
        'General Commodities - Intrastate',
        'General Commodities - Local',
        'General Commodities - International',
        'Auto - Interstate',
        'Auto - Intrastate',
        'Auto - Local',
        'Auto - International',
        'Commercial - Interstate',
        'Commercial - Intrastate',
        'Commercial - Local',
        'Commercial - International',
    );
    $field3->setPicklistValues($picklistvalues);
    $moduleInstance->setEntityIdentifier($field3);

}

//Billing Type
$field4 = Vtiger_Field::getInstance('agentcompgr_billingtype', $moduleInstance);
if($field4) {
    echo "<br> The agentcompgr_billingtype field already exists in Agent Compensation Group <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_BILLINGTYPE';
    $field4->name = 'agentcompgr_billingtype';
    $field4->table = 'vtiger_agentcompensationgroup';
    $field4->column ='agentcompgr_billingtype';
    $field4->columntype = 'text';
    $field4->uitype = 3333;
    $field4->typeofdata = 'V~M';

    $blockInstance1->addField($field4);

    $field4->setPicklistValues(array('COD','National Account','Military','GSA'));
    $moduleInstance->setEntityIdentifier($field4);
}

//Authority
$field5 = Vtiger_Field::getInstance('agentcompgr_authority', $moduleInstance);
if($field5) {
    echo "<br> The agentcompgr_authority field already exists in Agent Compensation Group <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_AUTHORITY';
    $field5->name = 'agentcompgr_authority';
    $field5->table = 'vtiger_agentcompensationgroup';
    $field5->column ='agentcompgr_authority';
    $field5->columntype = 'text';
    $field5->uitype = 3333;
    $field5->typeofdata = 'V~M';

    $blockInstance1->addField($field5);

    $field5->setPicklistValues(array('Van Line','Own Authority','Other Agent Authority'));
    $moduleInstance->setEntityIdentifier($field5);
}

// Status
$field6 = Vtiger_Field::getInstance('agentcompgr_status', $moduleInstance);
if($field6) {
    echo "<br> The agentcompgr_status field already exists in Agent Compensation Group <br>";
    // Update default value
    $adb->pquery("update `vtiger_field` set `defaultvalue`='Active' where `fieldid`=?;", array($field6->id));
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_STATUS';
    $field6->name = 'agentcompgr_status';
    $field6->table = 'vtiger_agentcompensationgroup';
    $field6->column ='agentcompgr_status';
    $field6->columntype = 'varchar(10)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~M';
    $field6->defaultvalue = 'Active';
    $blockInstance1->addField($field6);
    $field6->setPicklistValues(array('Active','Inactive'));

}


//Type
$field7 = Vtiger_Field::getInstance('agentcompgr_type', $moduleInstance);
if($field7) {
    echo "<br> The agentcompgr_type field already exists in Agent Compensation Group <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_TYPE';
    $field7->name = 'agentcompgr_type';
    $field7->table = 'vtiger_agentcompensationgroup';
    $field7->column ='agentcompgr_type';
    $field7->columntype = 'varchar(50)';
    $field7->uitype = 16;
    $field7->typeofdata = 'V~M';

    $blockInstance1->addField($field7);

    $field7->setPicklistValues(array('Tariffs','Contracts'));

}

//Tariff / Contract
$field8 = Vtiger_Field::getInstance('agentcompgr_tariffcontract', $moduleInstance);
if($field8) {
    echo "<br> The agentcompgr_tariffcontract field already exists in Agent Compensation Group <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_TARIFF_CONTRACT';
    $field8->name = 'agentcompgr_tariffcontract';
    $field8->table = 'vtiger_agentcompensationgroup';
    $field8->column ='agentcompgr_tariffcontract';
    $field8->columntype = 'varchar(50)';
    $field8->uitype = 10;
    $field8->typeofdata = 'V~M';

    $blockInstance1->addField($field8);

    $field8->setRelatedModules(array('Tariffs','Contracts','TariffManager'));

}


//Miles From
$field9 = Vtiger_Field::getInstance('agentcompgr_milesfrom', $moduleInstance);
if($field9) {
    echo "<br> The agentcompgr_milesfrom field already exists in Agent Compensation Group <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_MILESFROM';
    $field9->name = 'agentcompgr_milesfrom';
    $field9->table = 'vtiger_agentcompensationgroup';
    $field9->column ='agentcompgr_milesfrom';
    $field9->columntype = 'INT(10)';
    $field9->uitype = 7;
    $field9->typeofdata = 'I~O';
    $blockInstance1->addField($field9);
}

//Miles To
$field10 = Vtiger_Field::getInstance('agentcompgr_milesto', $moduleInstance);
if($field10) {
    echo "<br> The agentcompgr_milesto field already exists in Agent Compensation Group <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_MILESTO';
    $field10->name = 'agentcompgr_milesto';
    $field10->table = 'vtiger_agentcompensationgroup';
    $field10->column ='agentcompgr_milesto';
    $field10->columntype = 'INT(10)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';
    $blockInstance1->addField($field10);
}

//Weight From
$field11 = Vtiger_Field::getInstance('agentcompgr_weightfrom', $moduleInstance);
if($field11) {
    echo "<br> The agentcompgr_weightfrom field already exists in Agent Compensation Group <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_WEIGHTFROM';
    $field11->name = 'agentcompgr_weightfrom';
    $field11->table = 'vtiger_agentcompensationgroup';
    $field11->column ='agentcompgr_weightfrom';
    $field11->columntype = 'INT(10)';
    $field11->uitype = 7;
    $field11->typeofdata = 'I~O';
    $blockInstance1->addField($field11);
}

//Weight To
$field12 = Vtiger_Field::getInstance('agentcompgr_weightto', $moduleInstance);
if($field12) {
    echo "<br> The agentcompgr_weightto field already exists in Agent Compensation Group <br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_WEIGHTTO';
    $field12->name = 'agentcompgr_weightto';
    $field12->table = 'vtiger_agentcompensationgroup';
    $field12->column ='agentcompgr_weightto';
    $field12->columntype = 'INT(10)';
    $field12->uitype = 7;
    $field12->typeofdata = 'I~O';
    $blockInstance1->addField($field12);
}

// Effective Date From
$field13 = Vtiger_Field::getInstance('agentcompgr_effdatefrom', $moduleInstance);
if($field13) {
    echo "<br> The agentcompgr_effdatefrom field already exists in Agent Compensation Group <br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_EFFECTIVEDATE_FROM';
    $field13->name = 'agentcompgr_effdatefrom';
    $field13->table = 'vtiger_agentcompensationgroup';
    $field13->column ='agentcompgr_effdatefrom';
    $field13->columntype = 'DATE';
    $field13->uitype = 5;
    $field13->typeofdata = 'D~O';
    $blockInstance1->addField($field13);
}

// Effective Date To
$field14 = Vtiger_Field::getInstance('agentcompgr_effdateto', $moduleInstance);
if($field14) {
    echo "<br> The agentcompgr_effdateto field already exists in Agent Compensation Group <br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_EFFECTIVEDATE_TO';
    $field14->name = 'agentcompgr_effdateto';
    $field14->table = 'vtiger_agentcompensationgroup';
    $field14->column ='agentcompgr_effdateto';
    $field14->columntype = 'DATE';
    $field14->uitype = 5;
    $field14->typeofdata = 'D~O';
    $blockInstance1->addField($field14);
}

// Record Update Information
//Date Created
$field26 = Vtiger_Field::getInstance('createdtime',$moduleInstance);
if($field26) {
    echo "<li>The createdtime field already exists in Agent Compensation Group </li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_DATECREATED';
    $field26->name = 'createdtime';
    $field26->table = 'vtiger_crmentity';
    $field26->column = 'createdtime';
    $field26->uitype = 70;
    $field26->typeofdata = 'T~O';
    $field26->displaytype = 2;

    $blockInstance2->addField($field26);
}

//Date Modified
$field27 = Vtiger_Field::getInstance('modifiedtime',$moduleInstance);
if($field27) {
    echo "<li>The modifiedtime field already exists in Agent Compensation Group </li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_DATEMODIFIED';
    $field27->name = 'modifiedtime';
    $field27->table = 'vtiger_crmentity';
    $field27->column = 'modifiedtime';
    $field27->uitype = 70;
    $field27->typeofdata = 'T~O';
    $field27->displaytype = 2;

    $blockInstance2->addField($field27);
}

//Created By
$field28 = Vtiger_Field::getInstance('createdby',$moduleInstance);
if($field28) {
    echo "<li>The createdby field already exists in Agent Compensation Group </li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CREATEDBY';
    $field28->name = 'createdby';
    $field28->table = 'vtiger_crmentity';
    $field28->column = 'smcreatorid';
    $field28->uitype = 52;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 2;

    $blockInstance2->addField($field28);
}

//Assigned To
$field29 = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
if($field29){
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_ASSIGNEDTO';
    $field29->name = 'assigned_user_id';
    $field29->table = 'vtiger_crmentity';
    $field29->column = 'smownerid';
    $field29->uitype = 53;
    $field29->typeofdata = 'V~M';
    $field29->displaytype = 2;

    $blockInstance2->addField($field29);
}


if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4);

    // Add Agent Compensation Group to Admin Table / CRM Settings (OT Item 3319)
    $adb->pquery("UPDATE vtiger_tab SET parent = '',tabsequence = '-1' WHERE `name` ='AgentCompensationGroup'");
    $max_id = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`,`pinned`) VALUES (?, ?, ?, ?, ?, ?,?)", array($max_id, '4', 'AgentCompensationGroup', 'Agent Compensation Group', 'index.php?module=AgentCompensationGroup&view=List', $max_id, '1'));

    $AgentCompensationModule = Vtiger_Module::getInstance('AgentCompensation');
    if($AgentCompensationModule)
    {
        $AgentCompensationModule->setRelatedList($moduleInstance,'AgentCompensationGroup','ADD','get_dependents_list');
    }

    $adb->pquery("update `vtiger_entityname` set `fieldname`='agentcompgr_businessline,agentcompgr_billingtype,agentcompgr_authority' where `modulename`='AgentCompensationGroup'", array());
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";