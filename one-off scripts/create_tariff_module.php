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
$moduleInstance->name = 'Tariffs';
$moduleInstance->save();

$moduleInstance->initTables();

$menu = Vtiger_Menu::getInstance('COMPANY_ADMIN_TAB');
$menu->addModule($moduleInstance);

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_TARIFFS_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$blockInstance2 = new Vtiger_Block();
$blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
$moduleInstance->addBlock($blockInstance2);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFS_NAME';
$field1->name = 'tariff_name';
$field1->table = 'vtiger_tariffs';
$field1->column = 'tariff_name';
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);
    
$moduleInstance->setEntityIdentifier($field1);
    
$field2 = new Vtiger_Field();
$field2->label = 'LBL_TARIFFS_STATE';
$field2->name = 'tariff_state';
$field2->table = 'vtiger_tariffs';
$field2->column = 'tariff_state';
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 2;
$field2->typeofdata = 'V~M';

$blockInstance->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_TARIFFS_RELATEDAGENT';
$field3->name = 'related_agent';
$field3->table = 'vtiger_tariffs';
$field3->column = 'related_agent';
$field3->columntype = 'INT(19)';
$field3->uitype = 10;
$field3->typeofdata = 'V~M';

$blockInstance->addField($field3);

$field3->setRelatedModules(array('Agents'));

$field4 = new Vtiger_Field();
$field4->label = 'Commodity';
$field4->name = 'commodity_type';
$field4->table = 'vtiger_tariffs';
$field4->column = 'commodity_type';
$field4->columntype = 'VARCHAR(255)';
$field4->uitype = 15;
$field4->typeofdata = 'V~M';

$blockInstance->addField($field4);

$field4->setPicklistValues(array('HHG', 'Comm. Goods', 'Truckload'));

$field5 = new Vtiger_Field();
$field5->label = 'Assigned To';
$field5->name = 'assigned_user_id';
$field5->table = 'vtiger_crmentity';
$field5->column = 'smownerid';
$field5->uitype = 53;
$field5->typeofdata = 'V~M';

$blockInstance->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'Created Time';
$field6->name = 'CreatedTime';
$field6->table = 'vtiger_crmentity';
$field6->column = 'createdtime';
$field6->uitype = 70;
$field6->typeofdata = 'T~O';
$field6->displaytype = 2;

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'Modified Time';
$field7->name = 'ModifiedTime';
$field7->table = 'vtiger_crmentity';
$field7->column = 'modifiedtime';
$field7->uitype = 70;
$field7->typeofdata = 'T~O';
$field7->displaytype = 2;

$blockInstance->addField($field7);

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

/*$sectionInstance = Vtiger_Module::getInstance('TariffSections');
$relationLabel = 'Tariff Sections';
$moduleInstance->setRelatedList($sectionInstance, $relationLabel, Array('Add'));

$dateInstance = Vtiger_Module::getInstance('EffectiveDates');
$relationLabel = 'Effective Dates';
$moduleInstance->setRelatedList($dateInstance, $relationLabel, Array('Add'));
*/;
