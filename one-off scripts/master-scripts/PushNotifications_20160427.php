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



$module = Vtiger_Module::getInstance('PushNotifications');
if ($module) {
    echo "<br />PushNotifications module already exists<br />";
    $isNew = false;
} else {
    echo "<br />Creating new module - PushNotifications<br />";
    $module = new Vtiger_Module();
    $module->name = 'PushNotifications';
    $module->save();

    $module->initTables();
    $isNew = true;
}

$baseBlock = Vtiger_Block::getInstance('LBL_PUSHNOTIFICATIONS_INFORMATION', $module);
if ($baseBlock) {
    echo "<br />LBL_PUSHNOTIFICATIONS_INFORMATION block already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding block LBL_PUSHNOTIFICATIONS_INFORMATION to ".$module->label." module<br />";
    $baseBlock = new Vtiger_Block();
    $baseBlock->label = 'LBL_PUSHNOTIFICATIONS_INFORMATION';
    $module->addBlock($baseBlock);
}

$cfBlock = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);
if ($cfBlock) {
    echo "<br />LBL_CUSTOM_INFORMATION block already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding block LBL_CUSTOM_INFORMATION to ".$module->label." module<br />";
    $cfBlock = new Vtiger_Block();
    $cfBlock->label = 'LBL_CUSTOM_INFORMATION';
    $module->addBlock($cfBlock);
}

$adminBlock = Vtiger_Block::getInstance('LBL_PUSHNOTIFICATIONS_ADMIN', $module);
if ($adminBlock) {
    echo "<br />LBL_PUSHNOTIFICATIONS_ADMIN block already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding block LBL_PUSHNOTIFICATIONS_ADMIN to ".$module->label." module<br />";
    $adminBlock = new Vtiger_Block();
    $adminBlock->label = 'LBL_PUSHNOTIFICATIONS_ADMIN';
    $module->addBlock($adminBlock);
}

$entityField = Vtiger_Field::getInstance('notification_no', $module);
if ($entityField) {
    echo "<br />notification_no field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field notification_no to ".$module->label." module<br />";
    $entityField               = new Vtiger_Field();
    $entityField->label        = 'LBL_PUSHNOTIFICATIONS_NOTIFICATIONNUMBER';
    $entityField->name         = 'notification_no';
    $entityField->table        = 'vtiger_pushnotifications';
    $entityField->column       = 'notification_no';
    $entityField->columntype   = 'VARCHAR(100)';
    $entityField->uitype       = 4;
    $entityField->typeofdata   = 'V~M';
    $entityField->displaytype  = 1;
    $entityField->presence     = 2;
    $entityField->quickcreate  = 1;
    $entityField->summaryfield = 1;

    $adminBlock->addField($entityField);

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $module->name, 'PUSH', 1);

    $module->setEntityIdentifier($entityField);
}

$userField = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($userField) {
    echo "<br />assigned_user_id field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field assigned_user_id to ".$module->label." module<br />";
    $userField = new Vtiger_Field();
    $userField->label = 'LBL_PUSHNOTIFICATIONS_ASSIGNEDUSER';
    $userField->name = 'assigned_user_id';
    $userField->table = 'vtiger_crmentity';
    $userField->column = 'smownerid';
    $userField->uitype = 53;
    $userField->typeofdata = 'V~M';
    $userField->displaytype = 1;
    $userField->quickcreate = 0;
    $userField->presence = 2;
    $userField->summaryfield = 1;

    $adminBlock->addField($userField);
}

$createdTimeField = Vtiger_Field::getInstance('createdtime', $module);
if ($createdTimeField) {
    echo "<br />createdtime field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field createdtime to ".$module->label." module<br />";
    $createdTimeField = new Vtiger_Field();
    $createdTimeField->label = 'LBL_PUSHNOTIFICATIONS_CREATEDTIME';
    $createdTimeField->name = 'createdtime';
    $createdTimeField->table = 'vtiger_crmentity';
    $createdTimeField->column = 'createdtime';
    $createdTimeField->uitype = 70;
    $createdTimeField->typeofdata = 'DT~O';
    $createdTimeField->displaytype = 2;
    $createdTimeField->presence = 2;
    $createdTimeField->quickcreate = 1;
    $createdTimeField->summaryfield = 1;

    $adminBlock->addfield($createdTimeField);
}

$modifiedTimeField = Vtiger_Field::getInstance('modifiedtime', $module);
if ($modifiedTimeField) {
    echo "<br />modifiedtime field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field modifiedtime to ".$module->label." module<br />";
    $modifiedTimeField = new Vtiger_Field();
    $modifiedTimeField->label = 'LBL_PUSHNOTIFICATIONS_MODIFIEDTIME';
    $modifiedTimeField->name = 'modifiedtime';
    $modifiedTimeField->table = 'vtiger_crmentity';
    $modifiedTimeField->column = 'modifiedtime';
    $modifiedTimeField->uitype = 70;
    $modifiedTimeField->typeofdata = 'DT~O';
    $modifiedTimeField->displaytype = 2;
    $modifiedTimeField->presence = 2;
    $modifiedTimeField->quickcreate = 1;
    $modifiedTimeField->summaryfield = 1;

    $adminBlock->addField($modifiedTimeField);
}

$ownerField = Vtiger_Field::getInstance('agentid', $module);
if ($ownerField) {
    echo "<br />agentid field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field agentid to ".$module->label." module<br />";
    $ownerField = new Vtiger_Field();
    $ownerField->label = 'LBL_PUSHNOTIFICATIONS_OWNER';
    $ownerField->name = 'agentid';
    $ownerField->table = 'vtiger_crmentity';
    $ownerField->column = 'agentid';
    $ownerField->uitype = 1002;
    $ownerField->typeofdata = 'I~M';
    $ownerField->displaytype = 1;
    $ownerField->quickcreate = 1;
    $ownerField->summaryfield = 1;

    $adminBlock->addField($ownerField);
}

$textField = Vtiger_Field::getInstance('message', $module);
if ($textField) {
    echo "<br />message field already exists in ".$module->label." module<br />";
} else {
    echo "<br />Adding field message to ".$module->label." module<br />";
    $textField = new Vtiger_Field();
    $textField->label = 'LBL_PUSHNOTIFICATIONS_MESSAGE';
    $textField->name = 'message';
    $textField->table = 'vtiger_pushnotifications';
    $textField->column = 'message';
    $textField->columntype = 'VARCHAR(200)';
    $textField->uitype = 20;
    $textField->typeofdata = 'V~M';
    $textField->displaytype = 1;
    $textField->quickcreate = 0;
    $textField->summaryfield = 1;

    $baseBlock->addField($textField);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module->addFilter($filter1);

    $filter1->addField($entityField)->addField($userField, 1)->addField($ownerField, 2)->addField($createdTimeField, 3)->addField($modifiedTimeField, 4)->addField($field9, 5);

    $module->initWebservice();
    $module->setDefaultSharing();

    // Adds the Updates link to the vertical navigation menu on the right.
    ModTracker::enableTrackingForModule($module->id);

    $vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');

    $vanlineManagerInstance->setRelatedList($module, 'Push Notifications', array('add'), 'get_push_notifications');
} else {
    $vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');

    $sql = "SELECT * FROM `vtiger_relatedlists` WHERE tabid=? AND related_tabid=?";
    $result = $db->pquery($sql, [$vanlineManagerInstance->id, $module->id]);
    if ($db->num_rows($result) < 1) {
        //We didn't get past the filter due to previous defect
        $module->initWebservice();
        $module->setDefaultSharing();
        $vanlineManagerInstance->setRelatedList($module, 'Push Notifications', ['add'], 'get_push_notifications');
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";