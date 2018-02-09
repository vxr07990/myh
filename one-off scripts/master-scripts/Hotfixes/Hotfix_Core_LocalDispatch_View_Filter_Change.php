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
$OrdersTask = Vtiger_Module::getInstance('OrdersTask');
if ($OrdersTask) {
    echo "<h2>OrdersTask exists, updating filters</h2><br>\n";
} else {
    echo "<h2>OrdersTask not present. Exiting.</h2><br>\n";
    return;
}
$Orders = Vtiger_Module_Model::getInstance('Orders');
if ($Orders) {
    echo "<h2>Orders exists, updating filters</h2><br>\n";
} else {
    echo "<h2>Orders not present. Exiting.</h2><br>\n";
    return;
}

//Removing filters by request.
$oldFilterNames = ['Local Dispatch'];
foreach($oldFilterNames as $oldFilterName){
    $filtersCleaned = false;
    while(!$filtersCleaned) {
        $removingFilter = Vtiger_Filter::getInstance($oldFilterName, $OrdersTask);
        if ($removingFilter) {
            $removingFilter->delete();
        } else {
            $filtersCleaned = true;
        }
    }
}
$filter = new Vtiger_Filter();
$filter->name = 'Local Dispatch Day Page';
$filter->isdefault = false;
$OrdersTask->addFilter($filter);
$filterFields = [
    'dispatch_status', 'ordersid',
    'operations_task', 'service_date_from',
    'orders_eweight', 'estimated_hours',
    'service_provider_notes', 'origin_city',
    'origin_state', 'destination_city',
    'destination_state', 'total_estimated_personnel',
    'assigned_employee', 'total_estimated_vehicles',
    'assigned_vehicles', 'disp_assignedstart',
    'disp_assigneddate', 'date_spread',
    'multiservice_date', 'assigned_user_id'
];

$i = 0;
foreach($filterFields as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $OrdersTask);
    if(!$field) {
        $field = Vtiger_Field::getInstance($fieldName, $Orders);
        if(!$field){
            continue;
        }
    }
    $filter->addField($field, $i);
    $i++;
}
//removing [Administrator] from new filter name in drop down.
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_customview` SET status = 0 WHERE cvid = $filter->id");
