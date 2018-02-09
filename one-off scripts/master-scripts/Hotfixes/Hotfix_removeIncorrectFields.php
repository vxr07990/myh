<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
  *	This hotfix file is to remove various files defined in OT Defects 12997-99 and 13042.
  * The base creation script files have also been modified to remove the fields, so this
  * hotfix file is to correct the fields in versioned databases.
  */
//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

//OT Defect 12997
$leadModule = Vtiger_Module::getInstance('Leads');

$field1 = Vtiger_Field::getInstance('comm_res', $leadModule);
if ($field1) {
    echo "Field comm_res exists in Leads - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field1->id);
}

$field2 = Vtiger_Field::getInstance('include_packing', $leadModule);
if ($field2) {
    echo "Field include_packing exists in Leads - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field2->id);
}

//OT Defect 12998
$oppModule = Vtiger_Module::getInstance('Opportunities');
$potModule = Vtiger_Module::getInstance('Potentials');

$field3 = Vtiger_Field::getInstance('estimate_type', $oppModule);
if ($field3) {
    echo "Field estimate_type exists in Opportunities - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field3->id);
}

$field4 = Vtiger_Field::getInstance('estimate_type', $potModule);
if ($field4) {
    echo "Field estimate_type exists in Potentials - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field4->id);
}

$field5 = Vtiger_Field::getInstance('pricing_type', $oppModule);
if ($field5) {
    echo "Field pricing_type exists in Opportunities - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field5->id);
}

$field6 = Vtiger_Field::getInstance('pricing_type', $potModule);
if ($field6) {
    echo "Field pricing_type exists in Potentials - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field6->id);
}

//OT Defect 13042
$estModule = Vtiger_Module::getInstance('Estimates');
$quoModule = Vtiger_Module::getInstance('Quotes');

$field7 = Vtiger_Field::getInstance('shipping', $estModule);
if ($field7) {
    echo "Field shipping exists in Estimates - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field7->id);
}

$field8 = Vtiger_Field::getInstance('shipping', $quoModule);
if ($field8) {
    echo "Field shipping exists in Quotes - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field8->id);
}

//OT Defect 12999
$ordModule = Vtiger_Module::getInstance('Orders');

$field9 = Vtiger_Field::getInstance('orderspriority', $ordModule);
if ($field9) {
    echo "Field orderspriority exists in Orders - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field9->id);
}

$field10 = Vtiger_Field::getInstance('estimate_type', $ordModule);
if ($field10) {
    echo "Field estimate_type exists in Orders - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field10->id);
}

$field11 = Vtiger_Field::getInstance('orders_commodity', $ordModule);
if ($field11) {
    echo "Field orders_commodity exists in Orders - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field11->id);
}

$field12 = Vtiger_Field::getInstance('orders_etype', $ordModule);
if ($field12) {
    echo "Field orders_etype exists in Orders - removing<br />";
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE fieldid=".$field12->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";