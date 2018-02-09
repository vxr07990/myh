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

include_once 'vtlib/Vtiger/Module.php';
include_once 'include/Webservices/Utils.php';

$moduleInstance = Vtiger_Module::getInstance('Opportunities');

$sales_stage = Vtiger_Field::getInstance('sales_stage', $moduleInstance);

if ($sales_stage) {
    if ($sales_stage->uitype == '16' || $sales_stage->uitype == '3333') {
        $addNew = true;
        $picklist_table = 'vtiger_sales_stage';
        if (Vtiger_Utils::CheckTable($picklist_table)) {
            $db = &PearDatabase::getInstance();
            $stmt = 'SELECT sales_stage_id,presence,sortorderid FROM '.$picklist_table.' WHERE sales_stage = ? LIMIT 1';
            $result = $db->pquery($stmt, ['Survey Completed']);
            if($result && ($row = $result->fetchRow())) {
                if ($row['presence'] == 0) {
                    $stmt = 'UPDATE '.$picklist_table.' SET presence = 1 WHERE sales_stage_id = ? LIMIT 1';
                    $db->pquery($stmt, [$row['sales_stage_id']]);
                }
                $addNew = false;
            }

            if ($addNew) {
                $sales_stage->setPicklistValues(['Survey Completed']);
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
