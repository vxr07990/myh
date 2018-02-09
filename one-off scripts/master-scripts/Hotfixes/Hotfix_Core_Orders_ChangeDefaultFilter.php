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


$Orders = Vtiger_Module::getInstance('Orders');
if ($Orders) {
    echo "<h2>Orders exists, updating filter</h2><br>\n";
} else {
    echo "<h2>Orders not present. Exiting.</h2><br>\n";
    return;
}

$oldFilter = Vtiger_Filter::getInstance('All', $Orders);
if($oldFilter)
{
    $oldFilter->delete();
}

$filter = new Vtiger_Filter();
$filter->name = 'All';
$filter->isdefault = true;
$Orders->addFilter($filter);

$filterFields = ['orders_no', 'business_line2',
                 'orders_ldate', 'orders_ddate',
                 'orders_contacts', 'origin_city',
                 'origin_state', 'destination_city',
                 'destination_state', 'ordersstatus'];

$i = 0;
foreach($filterFields as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $Orders);
    if(!$field)
    {
        continue;
    }
    $filter->addField($field, $i);
    $i++;
}
