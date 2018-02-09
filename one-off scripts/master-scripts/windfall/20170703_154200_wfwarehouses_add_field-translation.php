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

$create = ['WFWarehouses' =>
               ['LBL_WFWAREHOUSE_INFORMATION' => [
                   'LBL_WFWAREHOUSES_TRANSLATION' => [
                       'name'              => 'translation',
                       'table'             => 'vtiger_wfwarehouses',
                       'column'            => 'translation',
                       'columntype'        => 'VARCHAR(40)',
                       'uitype'            => 1,
                       'typeofdata'        => 'V~O',
                       'sequence'          => 3,
                   ],
               ],
               ]
];

multicreate($create);
