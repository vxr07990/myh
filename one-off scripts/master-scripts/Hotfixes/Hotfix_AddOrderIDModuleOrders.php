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

$moduleInstance = Vtiger_Module::getInstance('Cubesheets');

if ($moduleInstance) {
    $block = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $moduleInstance);
    if ($block) {
        // Field Setup
    $field1 = Vtiger_Field::getInstance('cubesheets_orderid', $moduleInstance);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_CUBESHEETS_ORDERID';
            $field1->name = 'cubesheets_orderid';
            $field1->table = 'vtiger_cubesheets';
            $field1->column = 'cubesheets_orderid';
            $field1->columntype = 'INT(19)';
            $field1->uitype = 10;
            $field1->summaryfield = 1;
            $field1->typeofdata = 'V~O';
            $block->addField($field1);

            $field1->setRelatedModules(array('Orders'));
        }
    
        $field2 = Vtiger_Field::getInstance('potential_id', $moduleInstance);
        if ($field2 && $field2->typeofdata == 'V~M') {
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~O' WHERE fieldid = $field2->id");
        }
    
        $orderInstance = Vtiger_Module::getInstance('Orders');
        $surveysInstance = Vtiger_Module::getInstance('Surveys');
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_relatedlists SET name = 'get_dependents_list', label = 'Survey Appointment' WHERE tabid=$orderInstance->id AND related_tabid=$surveysInstance->id");
    
    //Relate Cubesheets to Orders
    $db = PearDatabase::getInstance();
        $cubesheetsInstance = Vtiger_Module::getInstance('Cubesheets');
        $result = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", [$orderInstance->id, $cubesheetsInstance->id]);
        if ($result && $db->num_rows($result) == 0) {
            $orderInstance->setRelatedList($cubesheetsInstance, 'Surveys', array('ADD'), 'get_cubesheets');
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";