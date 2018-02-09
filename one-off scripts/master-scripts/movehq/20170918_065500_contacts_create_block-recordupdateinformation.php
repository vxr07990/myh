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


//OT5274 Contacts - Add Record Update Information Block

$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('Contacts');
$blockInstance = Vtiger_Block::getInstance('LBL_RECORDUPDATEINFORMATION',$moduleInstance);

if($blockInstance){
    echo "<h3>The LBL_RECORDUPDATEINFORMATION block already exists in Contacts</h3><br> \n";
}else{
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_RECORDUPDATEINFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Date Created
$field1 = Vtiger_Field::getInstance('createdtime',$moduleInstance);
if($field1) {
    echo "<li>The createdtime field already exists in Contacts </li><br> \n";
	$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", array($blockInstance->id,$field1->id));
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_DATECREATED';
    $field1->name = 'createdtime';
    $field1->table = 'vtiger_crmentity';
    $field1->column = 'createdtime';
    $field1->uitype = 70;
    $field1->typeofdata = 'T~O';
    $field1->displaytype = 2;

    $blockInstance->addField($field1);
}

//Date Modified
$field2 = Vtiger_Field::getInstance('modifiedtime',$moduleInstance);
if($field2) {
    echo "<li>The modifiedtime field already exists in Contacts </li><br> \n";
	$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", array($blockInstance->id,$field2->id));
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_DATEMODIFIED';
    $field2->name = 'modifiedtime';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'modifiedtime';
    $field2->uitype = 70;
    $field2->typeofdata = 'T~O';
    $field2->displaytype = 2;

    $blockInstance->addField($field2);
}

//Created By
$field3 = Vtiger_Field::getInstance('createdby',$moduleInstance);
if($field3) {
    echo "<li>The createdby field already exists in Contacts </li><br> \n";
	$db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", array($blockInstance->id,$field3->id));
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CREATEDBY';
    $field3->name = 'createdby';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smcreatorid';
    $field3->uitype = 52;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 2;

    $blockInstance->addField($field3);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";