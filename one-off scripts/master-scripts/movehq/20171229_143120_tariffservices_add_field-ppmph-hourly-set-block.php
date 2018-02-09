<?php

/**
 * OT4812: This hotfix adds a 'Pounds Per Man Per Hour' field to the
 * Hourly Set block in the Tariff Services module
 */
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

$create = [
    'TariffServices' => [
        'LBL_TARIFFSERVICES_HOURLYSET' => [
            'LBL_POUNDS_PER_MAN_PER_HOUR' => [
                'name' => 'lbs_per_man_per_hour',
                'table' => 'vtiger_tariffservices',
                'column' => 'lbs_per_man_per_hour',
                'columntype' => 'INT(10)',
                'uitype' => 7,
                'typeofdata' => 'I~O~MIN=0~STEP=10'
            ]
        ]
    ]
];

multicreate($create);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
