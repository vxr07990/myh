<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$map = [
    'Opportunities' => [
        'LBL_POTENTIALS_INFORMATION' => [
            'LBL_PRIMARY_CONTACT_EMAIL' => [
                'name' => 'primary_contact_email',
                'table' => 'vtiger_potentialscf',
                'columntype' => 'VARCHAR(255)',
                'uitype' => '13',
                'typeofdata' => 'V~O',
            ]
        ]
    ]
];

multicreate($map);