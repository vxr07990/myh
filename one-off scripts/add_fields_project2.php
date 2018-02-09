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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Project');

// Address Details Block
$block1 = new Vtiger_Block();
$block1->label = 'LBL_PROJECT_ADDRESS_DETAILS';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_ADDRESS1';
$field1->name = 'origin_address1';
$field1->table = 'vtiger_project';
$field1->column = 'origin_address1';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_ADDRESS2';
$field1->name = 'origin_address2';
$field1->table = 'vtiger_project';
$field1->column = 'origin_address2';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_CITY';
$field1->name = 'origin_city';
$field1->table = 'vtiger_project';
$field1->column = 'origin_city';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_STATE';
$field1->name = 'origin_state';
$field1->table = 'vtiger_project';
$field1->column = 'origin_state';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_ZIP';
$field1->name = 'origin_zip';
$field1->table = 'vtiger_project';
$field1->column = 'origin_zip';
$field1->columntype = 'VARCHAR(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_PHONE1';
$field1->name = 'origin_phone1';
$field1->table = 'vtiger_project';
$field1->column = 'origin_phone1';
$field1->columntype = 'VARCHAR(30)';
$field1->uitype = 11;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_ORIGIN_PHONE2';
$field1->name = 'origin_phone2';
$field1->table = 'vtiger_project';
$field1->column = 'origin_phone2';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 11;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_ADDRESS1';
$field1->name = 'destination_address1';
$field1->table = 'vtiger_project';
$field1->column = 'destination_address1';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_ADDRESS2';
$field1->name = 'destination_address2';
$field1->table = 'vtiger_project';
$field1->column = 'destination_address2';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_CITY';
$field1->name = 'destination_city';
$field1->table = 'vtiger_project';
$field1->column = 'destination_city';
$field1->columntype = 'VARCHAR(50)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O~LE~50';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_STATE';
$field1->name = 'destination_state';
$field1->table = 'vtiger_project';
$field1->column = 'destination_state';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_ZIP';
$field1->name = 'destination_zip';
$field1->table = 'vtiger_project';
$field1->column = 'destination_zip';
$field1->columntype = 'VARCHAR(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_PHONE1';
$field1->name = 'destination_phone1';
$field1->table = 'vtiger_project';
$field1->column = 'destination_phone1';
$field1->columntype = 'VARCHAR(30)';
$field1->uitype = 11;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DESTINATION_PHONE2';
$field1->name = 'destination_phone2';
$field1->table = 'vtiger_project';
$field1->column = 'destination_phone2';
$field1->columntype = 'VARCHAR(30)';
$field1->uitype = 11;
$field1->typeofdata = 'V~O';

$block1->addField($field1);
$block1->save($module);


// Dates Block
$block1 = new Vtiger_Block();
$block1->label = 'LBL_PROJECT_DATES';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_PACK_DATE';
$field1->name = 'pack_date';
$field1->table = 'vtiger_project';
$field1->column = 'pack_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_PACK_TO_DATE';
$field1->name = 'pack_to_date';
$field1->table = 'vtiger_project';
$field1->column = 'pack_to_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_LOAD_DATE';
$field1->name = 'load_date';
$field1->table = 'vtiger_project';
$field1->column = 'load_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_LOAD_TO_DATE';
$field1->name = 'load_to_date';
$field1->table = 'vtiger_project';
$field1->column = 'load_to_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DELIVER_DATE';
$field1->name = 'deliver_date';
$field1->table = 'vtiger_project';
$field1->column = 'deliver_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DELIVER_TO_DATE';
$field1->name = 'deliver_to_date';
$field1->table = 'vtiger_project';
$field1->column = 'deliver_to_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_SURVEY_DATE';
$field1->name = 'survey_date';
$field1->table = 'vtiger_project';
$field1->column = 'survey_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_SURVEY_TIME';
$field1->name = 'survey_time';
$field1->table = 'vtiger_project';
$field1->column = 'survey_time';
$field1->columntype = 'TIME';
$field1->uitype = 14;
$field1->typeofdata = 'T~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_FOLLOWUP_DATE';
$field1->name = 'followup_date';
$field1->table = 'vtiger_project';
$field1->column = 'followup_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_PROJECT_DECISION_DATE';
$field1->name = 'decision_date';
$field1->table = 'vtiger_project';
$field1->column = 'decision_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);



$block1->save($module);
