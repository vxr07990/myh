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


$module = Vtiger_Module::getInstance('TariffManager');

$block1 = new Vtiger_Block();
$block1->label = 'LBL_TARIFFMANAGER_LOCALTARIFF';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_AGENCYNAME';
$field1->name = 'tariffmanager_agencyname';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_agencyname';
$field1->columntype = 'VARCHAR(55)';
$field1->uitype = 7;
$field1->typeofdata = 'V~M';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_AGENCYNUMBER';
$field1->name = 'tariffmanager_agencynumber';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_agencynumber';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~M';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_EFFECTIVEDATE';
$field1->name = 'tariffmanager_effectivedate';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_effectivedate';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~M';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TARIFFSTATE';
$field1->name = 'tariffmanager_tariffstate';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_tariffstate';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 15;
$field1->typeofdata = 'V~M';
$field1->setPicklistValues(array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY', 'AS', 'DC', 'FM', 'GU', 'MH', 'MP', 'PW', 'PR', 'VI', 'AE', 'AA', 'AP'));

$block1->addField($field1);

$block1->save($module);

$block1 = new Vtiger_Block();
$block1->label = 'LBL_TARIFFMANAGER_TARIFF';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TARIFFBYWEIGHT';
$field1->name = 'tariffmanager_tariffbyweight';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_tariffbyweight';
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TARIFFBYDISTANCE';
$field1->name = 'tariffmanager_tariffbydistance';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_tariffbydistance';
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TARIFFMANAGER_TARIFFBYFUEL';
$field1->name = 'tariffmanager_tariffbyfuel';
$field1->table = 'vtiger_tariffmanager';
$field1->column = 'tariffmanager_tariffbyfuel';
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);



$block1->save($module);
