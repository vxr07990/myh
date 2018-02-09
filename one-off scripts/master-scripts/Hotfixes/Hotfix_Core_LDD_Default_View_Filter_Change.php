<?php
if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
$Orders = Vtiger_Module_Model::getInstance('Orders');
if ($Orders) {
    echo "<h2>Orders exists, updating filters</h2><br>\n";
} else {
    echo "<h2>Orders not present. Exiting.</h2><br>\n";
    return;
}


//Removing filters by request.
$oldFilterNames = ['LDD Assigned', 'LDD Un Assigned', 'Unplanned Long Distance Dispatch']; //We have 2 filters with the same name hence the confusion

foreach($oldFilterNames as $oldFilterName){
    $filtersCleaned = false;
    while(!$filtersCleaned) {
        $removingFilter = Vtiger_Filter::getInstance($oldFilterName, $Orders, true);
        if ($removingFilter) {
            $removingFilter->delete();
        } else {
            $filtersCleaned = true;
        }
    }
}

//Looking for name just deletes the first one. We need to delete all of them

$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT cvid FROM vtiger_customview WHERE viewname ='Unplanned Long Distance Dispatch'");
if($result && $db->num_rows($result) > 0){
    while ($row = $db->fetch_row($result)) {
        $removingFilter = Vtiger_Filter::getInstance($row['cvid'], $Orders, true);
        if ($removingFilter) {
            $removingFilter->delete();
        }
    }
}


$filter = new Vtiger_Filter();
$filter->name = 'Unplanned Long Distance Dispatch';
$filter->isdefault = false;
$filter->view = 'LDDList';
$Orders->addFilter($filter);
$filterFields = [
    'orders_no', 'orders_eweight',
    'orders_trip', 'orders_ldate',
    'orders_ltdate', 'origin_city',
    'origin_state', 'destination_city',
    'destination_state', 'orders_ddate',
    'orders_dtdate', 'orders_elinehaul',
    'business_line', 'agentid'
];


$i = 0;
foreach($filterFields as $fieldName)
{
    $field = Vtiger_Field_Model::getInstance($fieldName, $Orders);
    if(!$field) {
        continue;
    }
    $filter->addField($field, $i);
    if($fieldName == 'business_line'){
        $filter->addRule($field, 'EQUALS','Interstate', 0, 1);
        $firstRule = true;
    }
    $i++;
}

$otherStatus = Vtiger_Field_Model::getInstance('orders_otherstatus', $Orders);
if($otherStatus){
    $filter->addRule($otherStatus, 'NOT_EQUALS', 'Planned,Confirmed,Loaded,Delivered', 1, 1);
    $secondRule = true;
}

$commodity = Vtiger_Field_Model::getInstance('commodities', $Orders);
if($commodity){
    $filter->addRule($commodity, 'EQUALS', 'Household', 2, 1);
    $secondRule = true;
}



if ($firstRule && $secondRule) {
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` set groupid =1, group_condition = 'and', condition_expression = '0 and 1', cvid = $filter->id");
}

//removing [Administrator] from new filter name in drop down.

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_customview` SET status = 0 WHERE cvid = $filter->id");
