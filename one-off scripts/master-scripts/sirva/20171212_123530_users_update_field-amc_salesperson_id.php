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

// TFS32785 - Users AMC Party Id location

$fields = [
    'user_name',
    'email1',
    'first_name',
    'last_name',
    'user_password',
    'confirm_password',
    'is_admin',
    'lead_view',
    'status',
    'end_hour',
    'is_owner',
    'agent_ids',
    'amc_salesperson_id',
    'move_coordinator',
    'amc_salesperson_id_nvl',
    'cf_oa_da_coordinator',
    'move_coordinator_navl',
    'mcid',
    'sts_salesperson_avl',
    'sts_salesperson_navl',
];

reorderFields('Users', $fields, false);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
