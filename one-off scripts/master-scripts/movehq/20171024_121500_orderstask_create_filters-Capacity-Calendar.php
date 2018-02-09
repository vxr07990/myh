<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//OT19542 BUG: Capacity Calendar Filters - Create Default Filter / Remove incorrect filter options

$ordersTaskModule = Vtiger_Module::getInstance('OrdersTask');
if($ordersTaskModule){+
	$filter = Vtiger_Filter::getInstance('Capacity Calendar', $ordersTaskModule, true);

	if($filter){

		$db = &PearDatabase::getInstance();
		$db->pquery("UPDATE `vtiger_customview` SET `setdefault` = '0' WHERE `view` = 'LocalDispatchCapacityCalendar' AND userid=1", array());
		$db->pquery("UPDATE `vtiger_customview` SET `status`=3, `setdefault` = '1' WHERE `view` = 'LocalDispatchCapacityCalendar' AND userid=1 AND name='Capacity Calendar'", array());
	}else{
		$filter = new Vtiger_Filter();
		$filter->name = "Capacity Calendar";
		$filter->userid = 1; //admin always id = 1
		$filter->view = "LocalDispatchCapacityCalendar";
		$filter->isdefault = true;
		$filter->save($ordersTaskModule);
		$fields = array('dispatch_status', 'ordersid', 'operations_task', 'task_start', 'total_estimated_personnel', 'total_estimated_vehicles', 'estimated_hours', 'notes_to_dispatcher', 'assigned_employee', 'assigned_vehicles', 'business_line');
		if($filter){
			$i = 0;
			foreach($fields as $fieldName){
				$field = Vtiger_Field::getInstance($fieldName,$ordersTaskModule);
				if($field){
					$filter->addField($field,$i);
					$i++;
				}
			}
		}
	}
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";