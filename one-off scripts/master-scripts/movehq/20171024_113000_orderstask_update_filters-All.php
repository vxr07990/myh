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

// OT19541 BUG: OrdersTask - View = List - Update Default filter / remove filter options tied to Local Dispatch

$ordersTaskModule = Vtiger_Module::getInstance('OrdersTask');
if($ordersTaskModule){
    $filter = Vtiger_Filter::getInstance('All', $ordersTaskModule);

    if($filter){

        //Remove all existing columns
        $db = PearDatabase::getInstance();
        $db->pquery('DELETE FROM vtiger_cvcolumnlist WHERE cvid=?', [$filter->id]);
    
        //Add the new columns
        $fields = array('dispatch_status', 'operations_task', 'service_date_from', 'estimated_hours', 'total_estimated_personnel', 'total_estimated_vehicles', 'participating_agent', 'agentid');
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
        

        $db->pquery("UPDATE vtiger_customview SET view='List' WHERE cvid=?", [$filter->id]);
    
    }

    $filter = Vtiger_Filter::getInstance('Local Dispatch Actuals', $ordersTaskModule);

    if($filter){
        $db->pquery("UPDATE vtiger_customview SET view='NewLocalDispatchActuals' WHERE cvid=?", [$filter->id]);
    }


}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";