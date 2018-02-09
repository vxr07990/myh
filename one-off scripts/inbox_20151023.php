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


$fieldu = Vtiger_Field::getInstance('user_alert_show_level');
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
    $fieldu->summaryfield = 1;
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
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET notification_enabled=1 WHERE tabid=".$moduleInstance->id);
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
    $field1->typeofdata = 'V~O';
    $field1->quickcreate = 0;
    $field1->summaryfield = 1;
    $field1->setRelatedModules(array('Users'));
    $blockInstance->addField($field1);
}

$field2 = Vtiger_Field::getInstance('inbox_to', $moduleInstance);
if ($field2) {
    echo "<br> field inbox_to already exists <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_INBOXTO';
    $field2->name = 'inbox_to';
    $field2->table = 'vtiger_inbox';
    $field2->column = 'inbox_to';
    $field2->columntype = 'INT(11)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->quickcreate = 0;
    $field2->summaryfield = 1;
    $field2->setRelatedModules(array('Users', 'Groups'));
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
    $field3->summaryfield = 1;
    $blockInstance->addField($field3);
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
    $field4->setPicklistValues(array('OA Request', 'Global Announcement'));
    $blockInstance->addField($field4);
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
    $field5->summaryfield = 1;
    $field5->setPicklistValues(array('HIGH', 'MEDIUM', 'LOW'));
    $blockInstance->addField($field5);
}

$field6 = Vtiger_Field::getInstance('inbox_read', $moduleInstance);
if ($field6) {
    echo "<br> field inbox_read already exists <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_INBOXREAD';
    $field6->name = 'inbox_read';
    $field6->table = 'vtiger_inbox';
    $field6->column = 'inbox_read';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'V~M';
    $field6->quickcreate = 0;
    $field6->summaryfield = 1;
    $blockInstance->addField($field6);
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
    $field7->summaryfield = 1;
    $blockInstance->addField($field7);
}
