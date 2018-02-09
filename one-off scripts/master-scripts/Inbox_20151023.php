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



$moduleInstanceUsers = Vtiger_Module::getInstance('Users');
$blockInstanceUsers = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $moduleInstanceUsers);


$fieldu = Vtiger_Field::getInstance('user_alert_show_level', $moduleInstanceUsers);
$fieldu2 = Vtiger_Field::getInstance('is_admin', $moduleInstanceUsers);
if ($fieldu2 && $fieldu2->displaytype==1) {
    $fieldu2->displaytype=1;
}

if ($fieldu) {
    echo "<br> field user_alert_show_level already exists <br>";
} else {
    $fieldu = new Vtiger_Field();
    $fieldu->label = 'LBL_USERSHOWLEVEL';
    $fieldu->name = 'user_alert_show_level';
    $fieldu->table = 'vtiger_users';
    $fieldu->column = 'user_alert_show_level';
    $fieldu->columntype = 'VARCHAR(255)';
    $fieldu->uitype = 16;
    $fieldu->typeofdata = 'V~O';
    $fieldu->quickcreate = 0;
    $fieldu->summaryfield = 0;
    $fieldu->setPicklistValues(array('HIGH', 'MEDIUM', 'LOW'));
    $fieldu->defaultvalue='LOW';
    $blockInstanceUsers->addField($fieldu);
}

$moduleInstance = Vtiger_Module::getInstance('Inbox');
if ($moduleInstance) {
    echo "<br> Module Inbox already exists <br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Inbox';
    $moduleInstance->save();
    $moduleInstance->initTables();
    ModTracker::enableTrackingForModule($moduleInstance->id);
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

$blockInstance = Vtiger_Block::getInstance('LBL_INBOX_INFO', $moduleInstance);
if ($blockInstance) {
    echo "<br> block LBL_INBOX_INFO already exists <br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_INBOX_INFO';
    $moduleInstance->addBlock($blockInstance);
}


$field1 = Vtiger_Field::getInstance('inbox_from', $moduleInstance);
if ($field1) {
    echo "<br> field inbox_from already exists <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_INBOXFROM';
    $field1->name = 'inbox_from';
    $field1->table = 'vtiger_inbox';
    $field1->column = 'inbox_from';
    $field1->columntype = 'INT(11)';
    $field1->uitype = 10;
    $field1->readonly = 0;
    $field1->typeofdata = 'V~O';
    $field1->quickcreate = 0;
    $field1->summaryfield = 0;
    $blockInstance->addField($field1);
    $field1->setRelatedModules(array('Users'));
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field2) {
    echo "<br> field assigned_user_id already exists <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_INBOXTO';
    $field2->name = 'assigned_user_id';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'smownerid';
    $field2->uitype = 53;
    $field2->typeofdata = 'V~M';
    $field2->quickcreate = 0;
    $field2->summaryfield = 0;
    $blockInstance->addField($field2);
}

$field3 = Vtiger_Field::getInstance('inbox_announce', $moduleInstance);
if ($field3) {
    echo "<br> field inbox_announce already exists <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_INBOXANNOUNCE';
    $field3->name = 'inbox_announce';
    $field3->table = 'vtiger_inbox';
    $field3->column = 'inbox_announce';
    $field3->columntype = 'VARCHAR(3)';
    $field3->uitype = 56;
    $field3->typeofdata = 'V~O';
    $field3->quickcreate = 0;
    $field3->summaryfield = 0;
    $blockInstance->addField($field3);
}

$field3b = Vtiger_Field::getInstance('inbox_for_crmentity', $moduleInstance);
if ($field3b) {
    echo "<br> field inbox_for_crmentity already exists <br>";
} else {
    $field3b = new Vtiger_Field();
    $field3b->label = 'LBL_INBOXFORCRMENTITY';
    $field3b->name = 'inbox_for_crmentity';
    $field3b->table = 'vtiger_inbox';
    $field3b->column = 'inbox_for_crmentity';
    $field3b->columntype = 'VARCHAR(3)';
    $field3b->uitype = 7;
    $field3b->typeofdata = 'V~O';
    $field3b->quickcreate = 0;
    $field3b->summaryfield = 0;
    $blockInstance->addField($field3b);
}

$field4 = Vtiger_Field::getInstance('inbox_type', $moduleInstance);
if ($field4) {
    echo "<br> field inbox_type already exists <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_INBOXTYPE';
    $field4->name = 'inbox_type';
    $field4->table = 'vtiger_inbox';
    $field4->column = 'inbox_type';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->quickcreate = 0;
    $field4->summaryfield = 1;
    $field4->setPicklistValues(array('Participating Agent Request', 'Global Announcement', 'Message'));
    $blockInstance->addField($field4);
    $moduleInstance->setEntityIdentifier($field4);
}

$field5 = Vtiger_Field::getInstance('inbox_priority', $moduleInstance);
if ($field5) {
    echo "<br> field inbox_priority already exists <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_INBOXPRIORITY';
    $field5->name = 'inbox_priority';
    $field5->table = 'vtiger_inbox';
    $field5->column = 'inbox_priority';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~M';
    $field5->quickcreate = 0;
    $field5->summaryfield = 0;
    $field5->setPicklistValues(array('HIGH', 'MEDIUM', 'LOW'));
    $blockInstance->addField($field5);
}
if ($field6b) {
    echo "<br> field inbox_link already exists <br>";
} else {
    $field6b = new Vtiger_Field();
    $field6b->label = 'LBL_INBOXLINK';
    $field6b->name = 'inbox_link';
    $field6b->table = 'vtiger_inbox';
    $field6b->column = 'inbox_link';
    $field6b->columntype = 'VARCHAR(255)';
    $field6b->uitype = 1;
    $field6b->typeofdata = 'V~O';
    $field6b->quickcreate = 0;
    $field6b->summaryfield = 0;
    $field6b->defaultvalue = 0;
    $blockInstance->addField($field6b);
}
$field65 = Vtiger_Field::getInstance('createtime', $moduleInstance);
if ($field65) {
    echo "<br> field createtime already exists <br>";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_INBOXDATE';
    $field65->name = 'createtime';
    $field65->table = 'vtiger_crmentity';
    $field65->column = 'createdtime';
    $field65->columntype = 'TIMESTAMP';
    $field65->defaultvalue = 'CURRENT_TIMESTAMP';
    $field65->uitype = 70;
    $field65->typeofdata = 'DT~O';
    $field65->quickcreate = 0;
    $field65->summaryfield = 0;
    $blockInstance->addField($field65);
}

$field7 = Vtiger_Field::getInstance('inbox_message', $moduleInstance);
if ($field7) {
    echo "<br> field inbox_message already exists <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_INBOXMESSAGE';
    $field7->name = 'inbox_message';
    $field7->table = 'vtiger_inbox';
    $field7->column = 'inbox_message';
    $field7->columntype = 'TEXT';
    $field7->uitype = 19;
    $field7->typeofdata = 'V~M';
    $field7->quickcreate = 0;
    $field7->summaryfield = 0;
    $blockInstance->addField($field7);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";