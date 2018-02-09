<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('Opportunities');
{
    $blockInstance = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_REFERRAL', $moduleInstance);
    if ($blockInstance) {
        echo "<h3>The REFERRAL Information block already exists</h3><br> \n";
    } else {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_OPPORTUNITIES_REFERRAL';
        $moduleInstance->addBlock($blockInstance);
    }
}
$field1 = Vtiger_Field::getInstance('name_referral', $moduleInstance);
if ($field1) {
    echo "<li>The name field already exists - Removing</li><br> \n";
    $adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?", [$field1->id]);
} else {
    //DON'T ADD IT AS IT'S AN INCORRECT FIELD
//    $field1 = new Vtiger_Field();
//    $field1->label = 'LBL_OPPORTUNITIES_NAME';
//    $field1->name = 'name_referral';
//    $field1->table = 'vtiger_potential';
//    $field1->column = 'name_referral';
//    $field1->uitype = 1;
//    $field1->typeofdata = 'V~O';
//    $field1->columntype = 'VARCHAR(100)';
//    $blockInstance->addField($field1);
}

$field2 = Vtiger_Field::getInstance('address_referral', $moduleInstance);
if ($field2) {
    echo "<li>The address field already exists - Removing</li><br> \n";
    $adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?", [$field2->id]);
} else {
    //DON'T ADD IT AS IT'S AN INCORRECT FIELD
//    $field2 = new Vtiger_Field();
//    $field2->label = 'LBL_OPPORTUNITIES_ADDRESS';
//    $field2->name = 'address_referral';
//    $field2->table = 'vtiger_potential';
//    $field2->column = 'address_referral';
//    $field2->uitype = 1;
//    $field2->typeofdata = 'V~O';
//    $field2->columntype = 'text';
//    $blockInstance->addField($field2);
}

$field3 = Vtiger_Field::getInstance('phone_referral', $moduleInstance);
if ($field3) {
    echo "<li>The phone_referral field already exists - Removing</li><br> \n";
    $adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?", [$field3->id]);
} else {
    //DON'T ADD IT AS IT'S AN INCORRECT FIELD
//    $field3 = new Vtiger_Field();
//    $field3->label = 'LBL_OPPORTUNITIES_PHONE';
//    $field3->name = 'phone_referral';
//    $field3->table = 'vtiger_potential';
//    $field3->column = 'phone_referral';
//    $field3->uitype = 11;
//    $field3->typeofdata = 'V~O';
//    $field3->columntype = 'VARCHAR(30)';
//    $blockInstance->addField($field3);
}

$field4 = Vtiger_Field::getInstance('email_referral', $moduleInstance);
if ($field4) {
    echo "<li>The email_referral field already exists - Removing</li><br> \n";
    $adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?", [$field4->id]);
} else {
    //DON'T ADD IT AS IT'S AN INCORRECT FIELD
//    $field4 = new Vtiger_Field();
//    $field4->label = 'LBL_OPPORTUNITIES_EMAIL';
//    $field4->name = 'email_referral';
//    $field4->table = 'vtiger_potential';
//    $field4->column = 'email_referral';
//    $field4->uitype = 13;
//    $field4->typeofdata = 'V~O';
//    $field4->columntype = 'VARCHAR(100)';
//    $blockInstance->addField($field4);
}

$field5 = Vtiger_Field::getInstance('referral_contact', $moduleInstance);
if($field5) {
    echo "<li>The referral_contact field already exists</li><br />\n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OPPORTUNITIES_REFERRAL_CONTACT';
    $field5->name = 'referral_contact';
    $field5->table = 'vtiger_potential';
    $field5->column = 'referral_contact';
    $field5->columntype = 'INT(19)';
    $field5->uitype = 10;
    $field5->typeofdata = 'V~O';
    $blockInstance->addField($field5);
    $field5->setRelatedModules(['Contacts']);
}

$field6 = Vtiger_Field::getInstance('referral_send_thanks', $moduleInstance);
if($field6) {
    echo "<li>The referral_send_thanks field already eixists</li><br />\n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_OPPORTUNITIES_REFERRAL_THANKS';
    $field6->name = 'referral_send_thanks';
    $field6->table = 'vtiger_potential';
    $field6->column = 'referral_send_thanks';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'C~O';
    $blockInstance->addField($field6);
}

$field7 = Vtiger_Field::getInstance('referral_date_sent', $moduleInstance);
if($field7) {
    echo "<li>The referral_date_sent field already exists</li><br />\n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_OPPORTUNITIES_REFERRAL_DATE';
    $field7->name = 'referral_date_sent';
    $field7->table = 'vtiger_potential';
    $field7->column = 'referral_date_sent';
    $field7->columntype = 'DATE';
    $field7->uitype = 5;
    $field7->typeofdata = 'D~O';
    $blockInstance->addField($field7);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
