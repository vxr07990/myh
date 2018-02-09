<?php

//Hotfix_OrdersTask_NewActualFilter.php

if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$MODULENAMEORDERS = 'Orders';
    
$moduleInstanceOrders = Vtiger_Module::getInstance($MODULENAMEORDERS);
if ($moduleInstanceOrders) {
    $field1 = Vtiger_Field::getInstance('orders_contacts', $moduleInstanceOrders);
    $field2 = Vtiger_Field::getInstance('orders_no', $moduleInstanceOrders);
}

$MODULENAMEORDERSTASK = 'OrdersTask';

$moduleInstanceOrdersTask = Vtiger_Module::getInstance($MODULENAMEORDERSTASK);
if ($moduleInstanceOrdersTask) {
    $field3 = Vtiger_Field::getInstance('operations_task', $moduleInstanceOrdersTask);
    $field4 = Vtiger_Field::getInstance('dispatch_status', $moduleInstanceOrdersTask);
    $field5 = Vtiger_Field::getInstance('disp_assigneddate', $moduleInstanceOrdersTask);
    $field6 = Vtiger_Field::getInstance('disp_actualhours', $moduleInstanceOrdersTask);
    $field7 = Vtiger_Field::getInstance('actual_of_crew', $moduleInstanceOrdersTask);
    $field8 = Vtiger_Field::getInstance('actual_of_vehicles', $moduleInstanceOrdersTask);
    $mfield = Vtiger_Field::getInstance('assigned_user_id', $moduleInstanceOrdersTask);
}

if (!$moduleInstanceOrdersTask) {
    echo "\$moduleInstanceOrdersTask does not exist <br />";
} else {
    print_r($moduleInstanceOrdersTask);
}
if (!$field1) {
    echo "\$field1 does not exist <br />";
}
if (!$field2) {
    echo "\$field2 does not exist <br />";
}
if (!$field3) {
    echo "\$field3 does not exist <br />";
}
if (!$field4) {
    echo "\$field4 does not exist <br />";
}
if (!$field5) {
    echo "\$field5 does not exist <br />";
}
if (!$field6) {
    echo "\$field6 does not exist <br />";
}
if (!$field7) {
    echo "\$field7 does not exist <br />";
}
if (!$field8) {
    echo "\$field8 does not exist <br />";
}
if (!$mfield) {
    echo "\$mfield does not exist <br />";
}

$FILTERNAME = 'Local Dispatch Actuals';
$filter1 = Vtiger_Filter::getInstance($FILTERNAME, $moduleInstanceOrdersTask);
if ($filter1) {
    echo "<br> Filter exists <br>";
} else {
    echo "<br> Adding Filter : $FILTERNAME <br>";
    $filter1 = new Vtiger_Filter();
    $filter1->name = $FILTERNAME;
    $filter1->isdefault = true;
    if(property_exists('Vtiger_Filter', 'view')){
        $filter1->view = 'NewLocalDispatchActuals'; //Not sure if this has been merge in yet
    }
    $moduleInstanceOrdersTask->addFilter($filter1);
    
    echo "addFilter call completed <br />";

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6)->addField($field8, 7)->addField($mfield, 8);
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance($FILTERNAME, $moduleInstanceOrdersTask);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', 0");
    echo "<br> Added Filter : $FILTERNAME <br>";
}

// actual_of_crew and actual_of_vehicles fields so they show (set presence to 2)
$db = PearDatabase::getInstance();
$result = $db->pquery('SELECT MAX(sequence) as maxsequence FROM vtiger_field WHERE block = ?',array($field7->getBlockId()));
if($db->num_rows($result) > 0){
    $maxSequence = $db->query_result($result, 0, 'maxsequence' );
    $result1 = $db->pquery("UPDATE vtiger_field SET presence = 2, sequence = ? WHERE fieldid = ?", array($maxSequence+1,$field7->id));
    $result2 = $db->pquery("UPDATE vtiger_field SET presence = 2, sequence = ? WHERE fieldid = ?", array($maxSequence+2,$field8->id));
}