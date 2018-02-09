<?php

//Hotfix_AddFields_OrdersTask.php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$ordersTasksInstance = Vtiger_Module::getInstance("OrdersTask");
$block = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $ordersTasksInstance);

print "<h2>Deleting est_hours_personnel AND est_hours_vehicle fields from OrdersTask module. </h2>\n";

$estimatedHoursP = Vtiger_Field::getInstance('est_hours_personnel', $ordersTasksInstance);
if ($estimatedHoursP) {
	$estimatedHoursP->delete();
}
$estimatedHoursV = Vtiger_Field::getInstance('est_hours_vehicle', $ordersTasksInstance);
if ($estimatedHoursV) {
	$estimatedHoursV->delete();
}

print "<h2>START add total_estimated_personnel/total_estimated_vehicles to OrdersTask module. </h2>\n";

$field_e = Vtiger_Field::getInstance('total_estimated_personnel', $ordersTasksInstance);
if (!$field_e) {
    $field0 = new Vtiger_Field();
    $field0->label        = 'LBL_TOTAL_ESTIMATED_PERSONNEL';
    $field0->name         = 'total_estimated_personnel';
    $field0->table        = 'vtiger_orderstask';
    $field0->column       = 'total_estimated_personnel';
    $field0->columntype = 'INT(19)';
    $field0->uitype = 1;
    $field0->typeofdata = 'I~O';
	
    $block->addField($field0);
}
    
$field_v = Vtiger_Field::getInstance('total_estimated_vehicles', $ordersTasksInstance);
if (!$field_v) {
    $field1 = new Vtiger_Field();
    $field1->label        = 'LBL_TOTAL_ESTIMATED_VEHICLES';
    $field1->name         = 'total_estimated_vehicles';
    $field1->table        = 'vtiger_orderstask';
    $field1->column       = 'total_estimated_vehicles';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 1;
    $field1->typeofdata = 'I~O';

    $block->addField($field1);
}

print "<h2>END add total_estimated_personnel/total_estimated_vehicles to OrdersTask module. </h2>\n";
//Reorganize Operative Task Information block :)

$field1 = Vtiger_Field::getInstance('estimated_hours', $ordersTasksInstance);
$field2 = Vtiger_Field::getInstance('specialrequest', $ordersTasksInstance);

function updateSequence($moduleInstance, $block, $field, $fieldToUpdate, $doNotTouchArr){
	$db = PearDatabase::getInstance();
	
	$newSeq = intval($field->sequence) + 2;
	$db->pquery("UPDATE vtiger_field SET sequence=? WHERE block=? AND fieldname=?",array($newSeq,$block->id,$fieldToUpdate));
	
	$result = $db->pquery("SELECT * FROM vtiger_field WHERE block=? AND sequence >= ? ORDER BY sequence", array($block->id, $newSeq));
	$i = 1;
	if ($db->num_rows($result) > 0) {
		while($row = $db->fetch_row($result)){
			if(!in_array($row['fieldname'], $doNotTouchArr)){
				$oField = Vtiger_Field::getInstance($row['fieldname'], $moduleInstance);
				if(($oField->sequence + $i) == $newSeq){
					$i++;
				}
				$sequence = intval($oField->sequence) + $i;
                $db->pquery("UPDATE vtiger_field SET sequence=? WHERE block=? AND fieldname=?",array($sequence,$block->id,$row['fieldname']));
			}
		}
	}
}

function cmp($a, $b){
    return strcmp($a->sequence, $b->sequence);
}

$arr = array("total_estimated_vehicles","total_estimated_personnel");
$auxArr = array($field1->column => "total_estimated_personnel", $field2->column => "total_estimated_vehicles");
$fieldsToUpdateArray = array($field1,$field2);

usort($fieldsToUpdateArray, "cmp");


foreach($fieldsToUpdateArray as $fieldArray){
	updateSequence($ordersTasksInstance, $block, $fieldArray, $auxArr[$fieldArray->column], $arr);
}
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
