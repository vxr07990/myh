<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}
print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;
$create = ['WFLocations' =>
               ['LBL_WFLOCATIONS_DETAILS' => [
                   'LBL_WFLOCATIONS_VAULT_CAPACITY' => [
                       'name'       => 'vault_capacity',
                       'table'      => 'vtiger_wflocations',
                       'column'     => 'vault_capacity',
                       'columntype' => 'NUMERIC(65)',
                       'uitype'     => 1,
                       'typeofdata' => 'N~O~MIN=0',
                       'sequence'   => 10,
                   ],
               ],
               ],
];
multicreate($create);
