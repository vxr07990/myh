<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

// OT5247 - Order Status Picklist

$db = PearDatabase::getInstance();
$moduleName = 'Orders';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    echo "Module $moduleName not found.";
    return;
}
$fieldname = 'ordersstatus';
$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if (!$field) {
    echo "<br> Field ordersstatus not found <br>";
    return;
}
$valueArray = ['Booked','Cancelled','Will Advise'];

//Update picklist option name "On Hold, Will Advise" to "Will Advise"
$sql = "UPDATE vtiger_$fieldname SET ordersstatus='Will Advise' WHERE ordersstatus='On Hold, Will Advise'";
$result = $db->pquery($sql);

// Remove existing DELETED and RENAMED exceptions for special defaults
purgePicklistExceptions_5247($db, $field->id, ['Booked','Cancelled']);

// Remove default existing values
$sql = "DELETE FROM vtiger_$fieldname WHERE $fieldname NOT IN (". generateQuestionMarks($valueArray).")";
$result = $db->pquery($sql,$valueArray);

//set un deletable values in case they are not set
$field->setPicklistSpecialValues(['Booked','Cancelled']);

//set default value 'Booked'
$sql = "UPDATE vtiger_field SET defaultvalue = 'Booked' WHERE fieldid = ?";
$result = $db->pquery($sql,array($field->id));

//set ordersstatus = 'Booked' for records that has no ordersstatus value on db
$sql = "UPDATE vtiger_orders SET ordersstatus = 'Booked' WHERE ( ordersstatus IS NULL OR ordersstatus = '')";
$result = $db->pquery($sql);

//set ordersstatus = 'Will Advise' for records that has ordersstatus "On Hold, Will Advise"
$sql = "UPDATE vtiger_orders SET ordersstatus = 'Will Advise' WHERE ordersstatus = 'On Hold, Will Advise'";
$result = $db->pquery($sql);

// Add exceptions for any existing values that are being removed from this list if in use
addPicklistExceptions_5247($db, $field->id);

//fix order_reason label
$fieldname = 'order_reason';
$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($field) {
    $sql = "UPDATE vtiger_field SET fieldlabel='LBL_ORDER_REASON' WHERE fieldid=?";
    $result = $db->pquery($sql, array($field->id));
}

function purgePicklistExceptions_5247($db, $fieldid, $valueArray) {
    $valueids = [];
    foreach($valueArray as $value) {
        $result = $db->pquery("SELECT `ordersstatusid` FROM `vtiger_ordersstatus` WHERE `ordersstatus`=?", $value);
        $valueids[] = $result->fields['ordersstatusid'];
    }
    $sql = "DELETE FROM `vtiger_picklistexceptions` WHERE fieldid=? AND `type` IN ('DELETED', 'RENAMED') AND `old_val_id` IN (".generateQuestionMarks($valueids).")";
    $db->pquery($sql, $fieldid);
}

function addPicklistExceptions_5247($db, $fieldid) {
    $sql = "SELECT ordersstatus, agentid FROM `vtiger_orders` JOIN `vtiger_crmentity` ON `vtiger_orders`.ordersid=`vtiger_crmentity`.crmid WHERE ordersstatus NOT IN ('Booked', '', 'Cancelled', 'Will Advise') AND deleted=0 GROUP BY ordersstatus,agentid;";
    $result = $db->query($sql);
    $currentTime = date('Y-m-d H:i:s');
    while($row =& $result->fetchRow()) {
        $sql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
        $db->pquery($sql, [$row['agentid'], $fieldid, $row['ordersstatus'], 'ADDED', $currentTime, $currentTime, 1]);
    }
}
