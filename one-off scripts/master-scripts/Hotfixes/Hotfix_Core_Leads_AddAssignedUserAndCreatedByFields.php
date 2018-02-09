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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$moduleName = 'Leads';
$module = Vtiger_Module_Model::getInstance($moduleName);

if(!$module){
    print "Unable to find $moduleName. Exiting. <br />\n";
    return;
}

$infoBlockName = 'LBL_LEADS_INFORMATION';

$infoBlock = Vtiger_Block::getInstance($infoBlockName, $module);
if($infoBlock){
    $field1 = Vtiger_Field::getInstance('assigned_user_id', $module);
    if ($field1) {
        echo "The assigned_user_id field already exists<br>\n";
        //Make visible if not already
        $newPresence = 2;
        if ($field1->presence != $newPresence) {
            $db = PearDatabase::getInstance();
            print "Update presence for assigned_user_id in $infoBlockName to $newPresence.\n";
            $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ?';
            $db->pquery($sql, [$newPresence, $field1->id]);
        }
    } else {
        $field1             = new Vtiger_Field();
        $field1->label      = 'LBL_ASSIGNED_USER_ID';
        $field1->name       = 'assigned_user_id';
        $field1->table      = 'vtiger_crmentity';
        $field1->column     = 'smownerid';
        $field1->uitype     = 53;
        $field1->typeofdata = 'V~M';
        $infoBlock->addField($field1);
    }

} else {
    print "Unable to find block $infoBlockName <br />\n";
}


$recordBlockName = 'LBL_LEADS_RECORDUPDATE';
$recordBlock = Vtiger_Block::getInstance($recordBlockName, $module);
if($recordBlock){
    $field2 = Vtiger_Field::getInstance('created_user_id', $module);
    if ($field2) {
        echo "The createdby field already exists<br>\n";
    } else {
        $field2             = new Vtiger_Field();
        $field2->label      = 'LBL_CREATEDBY';
        $field2->name       = 'created_user_id';
        $field2->table      = 'vtiger_crmentity';
        $field2->column     = 'smcreatorid';
        $field2->uitype     = 52;
        $field2->typeofdata = 'V~O';
        $field2->displaytype = 2;
        $recordBlock->addField($field2);
    }
} else {
    print "Unable to find block $recordBlockName <br />\n";
}
