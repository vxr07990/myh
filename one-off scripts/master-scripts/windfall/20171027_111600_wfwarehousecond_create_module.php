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

$module = Vtiger_Module_Model::getInstance('WFWarehouseCond');

$create = [
    'WFWarehouseCond' => [
        'LBL_WFWAREHOUSECOND_INFORMATION' => [
            'LBL_WFWAREHOUSECOND_LOCATION' => [
                'name' => 'wfwarehousecond_location',
                'table' => 'vtiger_wfwarehousecond',
                'column' => 'wfwarehousecond_location',
                'columntype' => 'TEXT',
                'uitype' => 33,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'entityIdentifier' => true,
            ],
            'LBL_WFWAREHOUSECOND_COND' => [
                'name' => 'wfwarehousecond_cond',
                'table' => 'vtiger_wfwarehousecond',
                'column' => 'wfwarehousecond_cond',
                'columntype' => 'TEXT',
                'uitype' => 33,
                'typeofdata' => 'V~M',
                'sequence' => 2,
            ],

        ],
    ],
];

multicreate($create);


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$db = PearDatabase::getInstance();

$moduleName = 'WFInventory';

$module = Vtiger_Module::getInstance($moduleName);

$module->setGuestBlocks('WFWarehouseCond', ['LBL_WFWAREHOUSECOND_INFORMATION']);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
