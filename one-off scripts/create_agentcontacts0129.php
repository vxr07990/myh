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

$moduleInstance = new Vtiger_Module();
$moduleInstance->name = 'AgentContacts';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_AGENTCONTACTS_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_AGENTCONTACTS_FNAME';
$field1->name = 'acontacts_fname';
$field1->table = 'vtiger_agentcontacts';
$field1->column = 'acontacts_fname';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);
    
$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_AGENTCONTACTS_AGENTS';
$field2->name = 'acontacts_agents';
$field2->table = 'vtiger_agentcontacts';
$field2->column = 'acontacts_agents';
$field2->columntype = 'INT(19)';
$field2->uitype = 10;
$field2->typeofdata = 'V~O';

$blockInstance->addField($field2);
$field2->setRelatedModules(array('Agents'));

$field3 = new Vtiger_Field();
$field3->label = 'Assigned To';
$field3->name = 'assigned_user_id';
$field3->table = 'vtiger_crmentity';
$field3->column = 'smownerid';
$field3->uitype = 53;
$field3->typeofdata = 'V~M';

$blockInstance->addField($field3);

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

$field6 = new Vtiger_Field();
$field6->label = 'LBL_AGENTCONTACTS_LNAME';
$field6->name = 'acontacts_lname';
$field6->table = 'vtiger_agentcontacts';
$field6->column = 'acontacts_lname';
$field6->columntype = 'VARCHAR(255)';
$field6->uitype = 2;
$field6->typeofdata = 'V~M';

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_AGENTCONTACTS_ADDRESS1';
$field7->name = 'acontacts_address1';
$field7->table = 'vtiger_agentcontacts';
$field7->column = 'acontacts_address1';
$field7->columntype = 'VARCHAR(255)';
$field7->uitype = 1;
$field7->typeofdata = 'V~O';

$blockInstance->addField($field7);


$field8 = new Vtiger_Field();
$field8->label = 'LBL_AGENTCONTACTS_P3';
$field8->name = 'acontacts_p1';
$field8->table = 'vtiger_agentcontacts';
$field8->column = 'acontacts_p1';
$field8->columntype = 'INT(20)';
$field8->uitype = 7;
$field8->typeofdata = 'I~O';

$blockInstance->addField($field8);



$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field6, 1)->addField($field7, 2)->addField($field8, 3);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
