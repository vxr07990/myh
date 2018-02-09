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
$moduleInstance->name = 'Surveys';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_SURVEYS_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$blockInstance2 = new Vtiger_Block();
$blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
$moduleInstance->addBlock($blockInstance2);

$blockInstance3 = new Vtiger_Block();
$blockInstance3->label = 'LBL_BLOCK_SYSTEM_INFORMATION';
$moduleInstance->addBlock($blockInstance3);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SURVEYS_NO';
$field1->name = 'survey_no';
$field1->table = 'vtiger_surveys';
$field1->column = 'survey_no';
$field1->columntype = 'VARCHAR(32)';
$field1->uitype = 4;
$field1->typeofdata = 'V~M';
$field1->displaytype = 3;

$blockInstance->addField($field1);

$moduleInstance->setEntityIdentifier($field1);

//Setup auto numbering field default value
$entity = new CRMEntity();
$entity->setModuleSeqNumber('configure', $moduleInstance->name, 'SUR', 1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_SURVEYS_DATE';
$field2->name = 'survey_date';
$field2->table = 'vtiger_surveys';
$field2->column = 'survey_date';
$field2->columntype = 'DATE';
$field2->uitype = 5;
$field2->typeofdata = 'D~M';
$field2->quickcreate = 0;

$blockInstance->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_SURVEYS_SURVEYOR';
$field3->name = 'assigned_user_id';
$field3->table = 'vtiger_crmentity';
$field3->column = 'smownerid';
$field3->uitype = 53;
$field3->typeofdata = 'V~M';
$field3->quickcreate = 0;

$blockInstance->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_SURVEYS_STATUS';
$field4->name = 'survey_status';
$field4->table = 'vtiger_surveys';
$field4->column = 'survey_status';
$field4->columntype = 'VARCHAR(128)';
$field4->uitype = 16;
$field4->typeofdata = 'V~M';
$field4->quickcreate = 0;

$blockInstance->addField($field4);

$field4->setPicklistValues(array('Assigned', 'Completed', 'Cancelled'));

$field5 = new Vtiger_Field();
$field5->label = 'LBL_SURVEYS_ACCOUNTID';
$field5->name = 'account_id';
$field5->table = 'vtiger_surveys';
$field5->column = 'account_id';
$field5->columntype = 'INT(11)';
$field5->uitype = 73;
$field5->typeofdata = 'V~O';
$field5->quickcreate = 1;

$blockInstance->addField($field5);

$field5->setRelatedModules(array('Accounts'));

$field6 = new Vtiger_Field();
$field6->label = 'LBL_SURVEYS_CONTACTID';
$field6->name = 'contact_id';
$field6->table = 'vtiger_surveys';
$field6->column = 'contact_id';
$field6->columntype = 'INT(11)';
$field6->uitype = 57;
$field6->typeofdata = 'V~O';
$field6->quickcreate = 1;

$blockInstance->addField($field6);

$field6->setRelatedModules(array('Contacts'));

$field7 = new Vtiger_Field();
$field7->label = 'LBL_SURVEYS_POTENTIALID';
$field7->name = 'potential_id';
$field7->table = 'vtiger_surveys';
$field7->column = 'potential_id';
$field7->columntype = 'INT(11)';
$field7->uitype = 76;
$field7->typeofdata = 'V~O';
$field7->quickcreate = 1;

$blockInstance->addField($field7);

$field7->setRelatedModules(array('Potentials'));

$field8 = new Vtiger_Field();
$field8->label = 'LBL_SURVEYS_CREATEDTIME';
$field8->name = 'createdtime';
$field8->table = 'vtiger_crmentity';
$field8->column = 'createdtime';
$field8->uitype = 70;
$field8->typeofdata = 'T~O';
$field8->displaytype = 2;

$blockInstance3->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_SURVEYS_MODIFIEDTIME';
$field9->name = 'modifiedtime';
$field9->table = 'vtiger_crmentity';
$field9->column = 'modifiedtime';
$field9->uitype = 19;
$field9->typeofdata = 'T~O';
$field9->displaytype = 2;

$blockInstance3->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_SURVEYS_SURVEYTIME';
$field10->name = 'survey_time';
$field10->table = 'vtiger_surveys';
$field10->column = 'survey_time';
$field10->columntype = 'TIME';
$field10->uitype = 14;
$field10->typeofdata = 'T~M';
$field10->quickcreate = 0;

$blockInstance->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_SURVEYS_MOBILEPUSH';
$field11->name = 'sent_to_mobile';
$field11->table = 'vtiger_surveys';
$field11->column = 'sent_to_mobile';
$field11->columntype = 'INT(1)';
$field11->uitype = 7;
$field11->typeofdata = 'N~O';
$field11->displaytype = 3;

$blockInstance3->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_SURVEYS_ORDERS';
$field12->name = 'order_id';
$field12->table = 'vtiger_surveys';
$field12->column = 'order_id';
$field12->columntype = 'INT(11)';
$field12->uitype = 10;
$field12->typeofdata = 'V~O';
$field12->quickcreate = 1;

$blockInstance->addField($field12);

$field12->setRelatedModules(array('Orders'));

$field13 = new Vtiger_Field();
$field13->label = 'LBL_SURVEYS_ADDRESS1';
$field13->name = 'address1';
$field13->table = 'vtiger_surveys';
$field13->column = 'address1';
$field13->columntype = 'VARCHAR(50)';
$field13->uitype = 1;
$field13->typeofdata = 'V~O';
$field13->quickcreate = 1;

$blockInstance->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_SURVEYS_ADDRESS2';
$field14->name = 'address2';
$field14->table = 'vtiger_surveys';
$field14->column = 'address2';
$field14->columntype = 'VARCHAR(50)';
$field14->uitype = 1;
$field14->typeofdata = 'V~O';
$field14->quickcreate = 1;

$blockInstance->addField($field14);

$field15 = new Vtiger_Field();
$field15->label = 'LBL_SURVEYS_CITY';
$field15->name = 'city';
$field15->table = 'vtiger_surveys';
$field15->column = 'city';
$field15->columntype = 'VARCHAR(50)';
$field15->uitype = 1;
$field15->typeofdata = 'V~O';
$field15->quickcreate = 1;

$blockInstance->addField($field15);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_SURVEYS_STATE';
$field16->name = 'state';
$field16->table = 'vtiger_surveys';
$field16->column = 'state';
$field16->columntype = 'VARCHAR(255)';
$field16->uitype = 1;
$field16->typeofdata = 'V~O';
$field16->quickcreate = 1;

$blockInstance->addField($field16);

$field17 = new Vtiger_Field();
$field17->label = 'LBL_SURVEYS_ZIP';
$field17->name = 'zip';
$field17->table = 'vtiger_surveys';
$field17->column = 'zip';
$field17->columntype = 'VARCHAR(20)';
$field17->uitype = 1;
$field17->typeofdata = 'V~O';
$field17->quickcreate = 1;

$blockInstance->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_SURVEYS_COUNTRY';
$field18->name = 'country';
$field18->table = 'vtiger_surveys';
$field18->column = 'country';
$field18->columntype = 'VARCHAR(100)';
$field18->uitype = 1;
$field18->typeofdata = 'V~O';
$field18->quickcreate = 1;

$blockInstance->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_SURVEYS_PHONE1';
$field19->name = 'phone1';
$field19->table = 'vtiger_surveys';
$field19->column = 'phone1';
$field19->columntype = 'VARCHAR(30)';
$field19->uitype = 11;
$field19->typeofdata = 'V~O';
$field19->quickcreate = 1;

$blockInstance->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_SURVEYS_PHONE2';
$field20->name = 'phone2';
$field20->table = 'vtiger_surveys';
$field20->column = 'phone2';
$field20->columntype = 'VARCHAR(30)';
$field20->uitype = 11;
$field20->typeofdata = 'V~O';
$field20->quickcreate = 1;

$blockInstance->addField($field20);

$field21 = new Vtiger_Field();
$field21->label = 'LBL_SURVEYS_ADDRESSDESCRIPTION';
$field21->name = 'address_desc';
$field21->table = 'vtiger_surveys';
$field21->column = 'address_desc';
$field21->columntype = 'VARCHAR(255)';
$field21->uitype = 1;
$field21->typeofdata = 'V~O';
$field21->quickcreate = 1;

$blockInstance->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_SURVEYS_COMMERCIALORRESIDENTIAL';
$field22->name = 'comm_res';
$field22->table = 'vtiger_surveys';
$field22->column = 'comm_res';
$field22->columntype = 'VARCHAR(255)';
$field22->uitype = 16;
$field22->typeofdata = 'V~M';
$field22->quickcreate = 0;
$field22->defaultvalue = 'Residential';

$blockInstance->addField($field22);

$field22->setPicklistValues(array('Residential', 'Commercial'));

$field22 = new Vtiger_Field();
$field22->label = 'LBL_SURVEYS_SURVEYENDTIME';
$field22->name = 'survey_end_time';
$field22->table = 'vtiger_surveys';
$field22->column = 'survey_end_time';
$field22->columntype = 'TIME';
$field22->uitype = 14;
$field22->typeofdata = 'T~M';
$field22->quickcreate = 0;

$blockInstance->addField($field22);

$field23 = new Vtiger_Field();
$field23->label = 'LBL_SURVEYS_NOTES';
$field23->name = 'survey_notes';
$field23->table = 'vtiger_surveys';
$field23->column = 'survey_notes';
$field23->columntype = 'VARCHAR(255)';
$field23->uitype = 19;
$field23->typeofdata = 'V~O';
$field23->quickcreate = 1;

$blockInstance->addField($field23);

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field2)->addField($field3, 1)->addField($field4, 2)->addField($field5, 3)->addField($field6, 4)->addField($field7, 5);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

$documentsInstance = Vtiger_Module::getInstance('Documents');
$relationLabel = 'Documents';
$moduleInstance->setRelatedList($documentsInstance, $relationLabel, array('Add'));

$potentialInstance = Vtiger_Module::getInstance('Potentials');
$relationLabel = 'Survey Appointments';
$potentialInstance->setRelatedList($moduleInstance, $relationLabel, array('Add'));

$accountInstance = Vtiger_Module::getInstance('Accounts');
$relationLabel = 'Survey Appointments';
$accountInstance->setRelatedList($moduleInstance, $relationLabel, array('Add'));

$contactInstance = Vtiger_Module::getInstance('Contacts');
$relationLabel = 'Survey Appointments';
$contactInstance->setRelatedList($moduleInstance, $relationLabel, array('Add'));
