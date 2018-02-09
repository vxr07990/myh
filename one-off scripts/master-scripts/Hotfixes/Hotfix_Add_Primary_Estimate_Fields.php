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


$db = PearDatabase::getInstance();
$Estimates = Vtiger_Module::getInstance('Estimates');

$sql = "SELECT fieldname  FROM `vtiger_field` WHERE fieldlabel in ('LBL_QUOTES_SUBJECT','LBL_QUOTES_POTENTIALNAME','LBL_QUOTES_QUOTENUMBER','LBL_QUOTES_QUOTESTAGE','LBL_QUOTES_ACCOUNTNAME','LBL_QUOTES_ASSIGNEDTO','LBL_QUOTES_HDNGRANDTOTAL','LBL_QUOTES_ISPRIMARY')";
$result  = $db->pquery($sql);

while ($row = $result->fetchRow()) {
    $fieldName = $row['fieldname'];
    $field1 = Vtiger_Field::getInstance($fieldName, $Estimates);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield = '1' WHERE fieldid = ".$field1->id." AND summaryfield != '1'");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";