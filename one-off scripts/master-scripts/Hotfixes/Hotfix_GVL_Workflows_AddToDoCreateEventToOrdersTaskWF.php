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


// OT 3166 - Adding Create To Do and Create Event to workflows for Orders Task. Based off of Hotfix_Calendar_RelateEventsToOpps.php

echo "Starting add Create ToDo and Create Event to Orders Task workflows.<br/>";

$createToDoArray = [
    'include' => [
        'Leads',
        'Accounts',
        'Contacts',
        'HelpDesk',
        'Campaigns',
        'PurchaseOrder',
        'SalesOrder',
        'Invoice',
        'Opportunities',
        'Estimates',
        'Orders',
        'OrdersTask',
        'Documents',
    ],
    'exclude' => [
        'Calendar',
        'FAQ',
        'Events'
    ]
];

$createEventArray = [
    'include' => [
        'Leads',
        'Accounts',
        'Contacts',
        'HelpDesk',
        'Campaigns',
        'Opportunities',
        'Orders',
        'OrdersTask',
        'Documents',
    ],
    'exclude' => [
        'Calendar',
        'FAQ',
        'Events'
    ]
];

Vtiger_Utils::ExecuteQuery("UPDATE `com_vtiger_workflow_tasktypes` SET modules='".json_encode($createToDoArray)."' WHERE id=3 AND label='Create Todo'");
Vtiger_Utils::ExecuteQuery("UPDATE `com_vtiger_workflow_tasktypes` SET modules='".json_encode($createEventArray)."' WHERE id=4 AND label='Create Event'");

echo "Finished add Create ToDo and Create Event to Orders Task workflows.<br/>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";