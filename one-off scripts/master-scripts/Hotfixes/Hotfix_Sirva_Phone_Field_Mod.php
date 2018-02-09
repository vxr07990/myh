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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br>Begin  modifications to contacts fields<br>";

$db = PearDatabase::getInstance();

$contactsModule = Vtiger_Module::getInstance('Contacts');
if ($contactsModule) {
    $block = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $contactsModule);

    //ext field for primary phone
    $primaryPhoneExt = Vtiger_Field::getInstance('primary_phone_ext', $contactsModule);
    if ($primaryPhoneExt) {
        echo "<br> Field 'primary_phone_ext' is already present. <br>";
    } else {
        echo "<br> Field 'primary_phone_ext' not present. Creating it now<br>";
        $primaryPhoneExt = new Vtiger_Field();
        $primaryPhoneExt->label = 'LBL_CONTACTS_PRIMARYPHONEEXT';
        $primaryPhoneExt->name = 'primary_phone_ext';
        $primaryPhoneExt->table = 'vtiger_contactdetails';
        $primaryPhoneExt->column = 'primary_phone_ext';
        $primaryPhoneExt->columntype = 'VARCHAR(255)';
        $primaryPhoneExt->uitype = 1;
        $primaryPhoneExt->typeofdata = 'V~O';
        $primaryPhoneExt->quickcreate = 0;

        $block->addField($primaryPhoneExt);

        echo "<br> Field 'primary_phone_ext' added.<br>";
    }



    //phone type field for primary phone type
    $phoneTypePrimary = Vtiger_Field::getInstance('primary_phone_type', $contactsModule);
    if ($phoneTypePrimary) {
        echo "<br> Field 'primary_phone_type' is already present. <br>";
    } else {
        echo "<br> Field 'primary_phone_type' not present. Creating it now<br>";
        $phoneTypePrimary = new Vtiger_Field();
        $phoneTypePrimary->label = 'LBL_CONTACTS_PRIMARYPHONETYPE';
        $phoneTypePrimary->name = 'primary_phone_type';
        $phoneTypePrimary->table = 'vtiger_contactdetails';
        $phoneTypePrimary->column = 'primary_phone_type';
        $phoneTypePrimary->columntype = 'VARCHAR(255)';
        $phoneTypePrimary->uitype = 16;
        $phoneTypePrimary->typeofdata = 'V~O';
        $phoneTypePrimary->quickcreate = 0;

        $block->addField($phoneTypePrimary);

        //$phoneTypePrimary->setPicklistValues(Array('Home', 'Work', 'Cell'));
        echo "<br> Field 'primary_phone_type' added.<br>";
    }
} else {
    echo "<br><h1>Contacts MODULE NOT FOUND</h1><br>";
}

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `displaytype` = 3 WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` LIKE 'Contacts') AND (`fieldname` = 'homephone' OR `fieldname` = 'otherphone' OR `fieldname` = 'mobile')");

$moduleContacts = Vtiger_Module::getInstance('Contacts');

$block4 = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $moduleContacts);
$field67 = Vtiger_Field::getInstance('firstname', $moduleContacts);
$field2410 = Vtiger_Field::getInstance('primary_phone_type', $moduleContacts);
$field74 = Vtiger_Field::getInstance('leadsource', $moduleContacts);
$field78 = Vtiger_Field::getInstance('department', $moduleContacts);
$field80 = Vtiger_Field::getInstance('email', $moduleContacts);
$field82 = Vtiger_Field::getInstance('assistant', $moduleContacts);
$field84 = Vtiger_Field::getInstance('assistantphone', $moduleContacts);
$field86 = Vtiger_Field::getInstance('emailoptout', $moduleContacts);
$field88 = Vtiger_Field::getInstance('reference', $moduleContacts);
$field90 = Vtiger_Field::getInstance('createdtime', $moduleContacts);
$field91 = Vtiger_Field::getInstance('modifiedtime', $moduleContacts);
$field725 = Vtiger_Field::getInstance('created_user_id', $moduleContacts);
$field2403 = Vtiger_Field::getInstance('primary_phone_ext', $moduleContacts);
$field70 = Vtiger_Field::getInstance('lastname', $moduleContacts);
$field68 = Vtiger_Field::getInstance('contact_no', $moduleContacts);
$field69 = Vtiger_Field::getInstance('phone', $moduleContacts);
$field76 = Vtiger_Field::getInstance('title', $moduleContacts);
$field77 = Vtiger_Field::getInstance('fax', $moduleContacts);
$field79 = Vtiger_Field::getInstance('birthday', $moduleContacts);
$field81 = Vtiger_Field::getInstance('contact_id', $moduleContacts);
$field83 = Vtiger_Field::getInstance('secondaryemail', $moduleContacts);
$field85 = Vtiger_Field::getInstance('donotcall', $moduleContacts);
$field87 = Vtiger_Field::getInstance('assigned_user_id', $moduleContacts);
$field89 = Vtiger_Field::getInstance('notify_owner', $moduleContacts);
$field701 = Vtiger_Field::getInstance('isconvertedfromlead', $moduleContacts);
$field1893 = Vtiger_Field::getInstance('agentid', $moduleContacts);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field67->id." THEN 1 WHEN fieldid=".$field2410->id." THEN 3 WHEN fieldid=".$field74->id." THEN 5 WHEN fieldid=".$field78->id." THEN 7 WHEN fieldid=".$field80->id." THEN 9 WHEN fieldid=".$field82->id." THEN 11 WHEN fieldid=".$field84->id." THEN 13 WHEN fieldid=".$field86->id." THEN 15 WHEN fieldid=".$field88->id." THEN 17 WHEN fieldid=".$field90->id." THEN 19 WHEN fieldid=".$field91->id." THEN 21 WHEN fieldid=".$field725->id." THEN 23 WHEN fieldid=".$field2403->id." THEN 25 WHEN fieldid=".$field70->id." THEN 2 WHEN fieldid=".$field68->id." THEN 4 WHEN fieldid=".$field69->id." THEN 6 WHEN fieldid=".$field76->id." THEN 8 WHEN fieldid=".$field77->id." THEN 10 WHEN fieldid=".$field79->id." THEN 12 WHEN fieldid=".$field81->id." THEN 14 WHEN fieldid=".$field83->id." THEN 16 WHEN fieldid=".$field85->id." THEN 18 WHEN fieldid=".$field87->id." THEN 20 WHEN fieldid=".$field89->id." THEN 22 WHEN fieldid=".$field701->id." THEN 24 WHEN fieldid=".$field1893->id." THEN 26 END, block=CASE WHEN fieldid=".$field67->id." THEN ".$block4->id." WHEN fieldid=".$field2410->id." THEN ".$block4->id." WHEN fieldid=".$field74->id." THEN ".$block4->id." WHEN fieldid=".$field78->id." THEN ".$block4->id." WHEN fieldid=".$field80->id." THEN ".$block4->id." WHEN fieldid=".$field82->id." THEN ".$block4->id." WHEN fieldid=".$field84->id." THEN ".$block4->id." WHEN fieldid=".$field86->id." THEN ".$block4->id." WHEN fieldid=".$field88->id." THEN ".$block4->id." WHEN fieldid=".$field90->id." THEN ".$block4->id." WHEN fieldid=".$field91->id." THEN ".$block4->id." WHEN fieldid=".$field725->id." THEN ".$block4->id." WHEN fieldid=".$field2403->id." THEN ".$block4->id." WHEN fieldid=".$field70->id." THEN ".$block4->id." WHEN fieldid=".$field68->id." THEN ".$block4->id." WHEN fieldid=".$field69->id." THEN ".$block4->id." WHEN fieldid=".$field76->id." THEN ".$block4->id." WHEN fieldid=".$field77->id." THEN ".$block4->id." WHEN fieldid=".$field79->id." THEN ".$block4->id." WHEN fieldid=".$field81->id." THEN ".$block4->id." WHEN fieldid=".$field83->id." THEN ".$block4->id." WHEN fieldid=".$field85->id." THEN ".$block4->id." WHEN fieldid=".$field87->id." THEN ".$block4->id." WHEN fieldid=".$field89->id." THEN ".$block4->id." WHEN fieldid=".$field701->id." THEN ".$block4->id." WHEN fieldid=".$field1893->id." THEN ".$block4->id." END WHERE fieldid IN (".$field67->id.",".$field2410->id.",".$field74->id.",".$field78->id.",".$field80->id.",".$field82->id.",".$field84->id.",".$field86->id.",".$field88->id.",".$field90->id.",".$field91->id.",".$field725->id.",".$field2403->id.",".$field70->id.",".$field68->id.",".$field69->id.",".$field76->id.",".$field77->id.",".$field79->id.",".$field81->id.",".$field83->id.",".$field85->id.",".$field87->id.",".$field89->id.",".$field701->id.",".$field1893->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";