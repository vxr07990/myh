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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module_Model::getInstance('WFDriverCond');

if($module){
    return;
}

$create = [
    'WFDriverCond' => [
        'LBL_WFDRIVERCOND_INFORMATION' => [
            'LBL_WFDRIVERCOND_INVENTORY' => [
                'name' => 'wfdrivercond_inventory',
                'table' => 'vtiger_wfdrivercond',
                'column' => 'wfdrivercond_inventory',
                'columntype' => 'VARCHAR(255)',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'entityIdentifier' => true,
                'setRelatedModules' => ['WFInventory'],
            ],
            'LBL_WFDRIVERCOND_LOCATION' => [
                'name' => 'wfwarehousecond_location',
                'table' => 'vtiger_wfdrivercond',
                'column' => 'wfwarehousecond_location',
                'columntype' => 'TEXT',
                'uitype' => 33,
                'typeofdata' => 'V~M',
                'sequence' => 2,
                'filterSequence' => 1,
            ],
            'LBL_WFDRIVERCOND_COND' => [
                'name' => 'wfwarehousecond_cond',
                'table' => 'vtiger_wfdrivercond',
                'column' => 'wfwarehousecond_cond',
                'columntype' => 'TEXT',
                'uitype' => 33,
                'typeofdata' => 'V~M',
                'sequence' => 3,
                'filterSequence' => 2,
            ],

        ],
    ],
];

multicreate($create);


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$db = PearDatabase::getInstance();

$module = Vtiger_Module_Model::getInstance('WFDriverCond');

$Inventory = Vtiger_Module::getInstance('WFInventory');
if ($Inventory) {
    $Inventory->setRelatedList($module, 'Driver Conditions', [], 'get_dependents_list');
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
