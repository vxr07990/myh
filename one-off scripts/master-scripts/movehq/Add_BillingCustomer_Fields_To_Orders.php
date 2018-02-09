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

$moduleAgents = Vtiger_Module::getInstance('Orders');

$block = Vtiger_Block::getInstance('LBL_ORDERS_INVOICE', $moduleAgents);


$db = PearDatabase::getInstance();



//Customer
$field = Vtiger_Field::getInstance('orders_billingcustomerid', $moduleAgents);
if ($field) {
    echo "<br> The agents_status field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_BILLING_CUSTOMER';
    $field->name = 'orders_billingcustomerid';
    $field->table = 'vtiger_orders';
    $field->column = 'orders_billingcustomerid';
    $field->columntype = 'INT(11)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->defaultvalue = 'Active';
    $field->sequence = 1;

    $block->addField($field);
    $field->setRelatedModules(['Contacts', 'Accounts']);
}

//Customer Number
$field = Vtiger_Field::getInstance('orders_custnum', $moduleAgents); // I keep the same field name as !1962 so it does not brake that feature but is not merged into Core yet.
if ($field) {
    echo "<br> The agents_status field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_BILLING_CUSTOMER_NO';
    $field->name = 'orders_custnum';
    $field->table = 'vtiger_orders';
    $field->column = 'orders_custnum';
    $field->columntype = 'VARCHAR(25)';
    $field->uitype     = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 2;

    $block->addField($field);
}


if (!function_exists(lFunctionUpdateBlockSequence)){
	function lFunctionUpdateBlockSequence($moduleInstance,$blockId,$fields){
		$db = PearDatabase::getInstance();
		$i = 1;
		foreach($fields as $field){
			if($field != ""){
				$auxfield = Vtiger_Field::getInstance($field, $moduleInstance);
				$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ? AND block = ?",array($i,$auxfield->id,$blockId));
			}
			$i++;
		}
	}
}


$fields = array(
                'orders_billingcustomerid','orders_custnum',
                'bill_addrdesc','bill_company',
                'bill_street','bill_pobox',
                'bill_city','bill_state',
                'bill_code','bill_country',
                'invoice_phone','invoice_email',
                'invoice_delivery_format','payment_type',
             );

lFunctionUpdateBlockSequence($moduleAgents,$block->id,$fields);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";