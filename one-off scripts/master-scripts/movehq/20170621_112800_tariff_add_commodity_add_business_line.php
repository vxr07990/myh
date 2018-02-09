<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
		print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

if (!function_exists(lFunctionUpdateBlockSequence)){
	function lFunctionUpdateBlockSequence($moduleInstance,$blockID,$fields){
		$db = PearDatabase::getInstance();
		$i = 1;
		foreach($fields as $field){
			if($field != ""){
				$auxfield = Vtiger_Field::getInstance($field, $moduleInstance);
				if($auxfield){
					$db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ? AND block = ?",array($i,$auxfield->id,$blockID));
				}
			}
			$i++;
		}
	}
}

$db = PearDatabase::getInstance();

//OT4881

$moduleInstance = Vtiger_Module::getInstance('Tariffs');
$block = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $moduleInstance); //Tariff information


$field3 = Vtiger_Field::getInstance('business_line', $moduleInstance);
if(!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TARIFF_BUSINESS_LINE';
    $field3->name = 'business_line';
    $field3->table = 'vtiger_tariffs';
    $field3->column ='business_line';
    $field3->columntype = 'text';
    $field3->uitype = 3333;
    $field3->typeofdata = 'V~M';
	$field3->defaultvalue = 'All';
	
    $block->addField($field3);


}


$field2 = Vtiger_Field::getInstance('commodities', $moduleInstance);
if (!$field2) {
	$field2 = new Vtiger_Field();
	$field2->label = 'LBL_TARIFF_COMMODITIES';
	$field2->name = 'commodities';
	$field2->table = 'vtiger_tariffs';
	$field2->column = 'commodities';
	$field2->columntype = 'VARCHAR(255)';
	$field2->uitype = 3333;
	$field2->typeofdata = 'V~M';
	$field2->defaultvalue = 'All';

	$block->addField($field2);
}else{
	//Increase column size, the list of commodities do not fit in.
	$db->pquery("ALTER TABLE `vtiger_tariffs` CHANGE `commodities` `commodities` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL");
}

$fields = array("tariff_name","tariff_state","agentid","tariff_status","business_line","commodities");

lFunctionUpdateBlockSequence($moduleInstance,$block->id,$fields);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";