<?php
if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;

global $adb, $current_user;

$notificationsModuleName = 'Notifications';
$notificationsModuleLabel = 'Notifications';
$moduleInstance = Vtiger_Module::getInstance($notificationsModuleName);

if ($moduleInstance) {
    echo "<h2>{$notificationsModuleName} already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $notificationsModuleName;
    $moduleInstance->label = $notificationsModuleLabel;
    $moduleInstance->save();

}

// Check module
$rs = $adb->pquery("SELECT * FROM `vtiger_ws_entity` WHERE `name` = ?", array($notificationsModuleName));
if ($adb->num_rows($rs) == 0) {
    $adb->pquery("INSERT INTO `vtiger_ws_entity` (`name`, `handler_path`, `handler_class`, `ismodule`)
            VALUES (?, 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');", array($notificationsModuleName));
}

// create handle
$path = "modules/Notifications/NotificationsHandler.php";
$className = "NotificationsHandler";
$em = new VTEventsManager($adb);
$em->registerHandler('vtiger.entity.aftersave', $path, $className);

if(!Vtiger_Utils::CheckTable('vtiger_notifications')) {
    $stmt = "CREATE TABLE `vtiger_notifications` (
                    `notificationid` INT(19) NOT NULL,
                    `notificationno` VARCHAR(255) NOT NULL,
                    `related_to` INT(19) NOT NULL DEFAULT '0',
                    `notification_status` VARCHAR(200) NULL,							
                    PRIMARY KEY (`notificationid`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
    $adb->pquery($stmt);
    echo "Created table vtiger_notifications success <br>";
}else{
    echo 'vtiger_notifications already exists'.PHP_EOL.'<br>';
}

if(!Vtiger_Utils::CheckTable('vtiger_notificationscf')) {
    $stmt = "CREATE TABLE `vtiger_notificationscf` (
                    `notificationid` INT(19) NOT NULL,													
                    PRIMARY KEY (`notificationid`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
    $adb->pquery($stmt);
    echo "Created table vtiger_notificationscf success <br>";
}else{
    echo 'vtiger_notificationscf already exists'.PHP_EOL.'<br>';
}

if(!Vtiger_Utils::CheckTable('vte_modules')) {
    $stmt = 'CREATE TABLE `vte_modules` (
                `module`  varchar(50) NOT NULL ,
                `valid`  int(1) NULL ,
                PRIMARY KEY (`module`));';
    $adb->pquery($stmt);
    echo "Created table vte_modules success <br>";

    $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array($notificationsModuleName));
    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array($notificationsModuleName,'1'));
}else{
    echo 'vte_modules already exists'.PHP_EOL.'<br>';
}

if(!Vtiger_Utils::CheckTable('notifications_settings')) {
    $stmt = "CREATE TABLE `notifications_settings` (
                    `enable`  int(3) NULL DEFAULT NULL
                    )";
    $adb->pquery($stmt);
    echo "Created table notifications_settings success <br>";

    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array($notificationsModuleName,'1'));
}else{
    echo 'notifications_settings already exists'.PHP_EOL.'<br>';
}

$blockInstance1 = Vtiger_Block::getInstance('LBL_DETAIL_INFORMATION', $moduleInstance);
if ($blockInstance1) {
    echo "<li>The LBL_DETAIL_INFORMATION block already exists</li><br>";
} else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_DETAIL_INFORMATION';
    $moduleInstance->addBlock($blockInstance1);
}

$field1block1 = Vtiger_Field::getInstance('notificationno', $moduleInstance);
if ($field1block1) {
    echo "<li>The 'notificationno' field already exists</li><br>";
} else {
    $field1block1 = new Vtiger_Field();
    $field1block1->label = 'Notification Number';
    $field1block1->name = 'notificationno';
    $field1block1->table = 'vtiger_notifications';
    $field1block1->column = 'notificationno';
    $field1block1->columntype = 'VARCHAR(100)';
    $field1block1->uitype = 4;
    $field1block1->typeofdata = 'V~O';
    $field1block1->sequence = 0;

    $blockInstance1->addField($field1block1);
    $moduleInstance->setEntityIdentifier($field1block1);
    echo "<li>The 'notificationno' field created done</li><br>";
}

$field2block1 = Vtiger_Field::getInstance('related_to', $moduleInstance);
if ($field2block1) {
    echo "<li>The 'related_to' field already exists</li><br>";
} else {
    $field2block1 = new Vtiger_Field();
    $field2block1->label = 'Related To';
    $field2block1->name = 'related_to';
    $field2block1->table = 'vtiger_notifications';
    $field2block1->column = 'related_to';
    $field2block1->columntype = 'VARCHAR(100)';
    $field2block1->uitype = 10;
    $field2block1->typeofdata = 'V~O';
    $field2block1->quickcreate = 0;
    $field2block1->summaryfield = 1;
    $field2block1->sequence = 1;

    $blockInstance1->addField($field2block1);
//    $moduleNames = array('Leads', 'Contacts', 'Accounts', 'HelpDesk', 'Potentials', 'Quotes', 'Invoice', 'SalesOrder',
//        'Calendar', 'Project', 'ProjectTask', 'Events', 'Campaigns', 'ProjectMilestone', 'PurchaseOrder', 'Products', 'Vendors');
//
//    foreach ($moduleNames as $moduleName) {
//        $relatedModuleInstance = Vtiger_Module::getInstance($moduleName);
//        $relatedModuleInstance->setRelatedList($moduleInstance, $notificationsModuleLabel, array('ADD'), 'get_dependents_list');
//    }
//    $field2block1->setRelatedModules($moduleNames);

    echo "<li>The 'related_to' field created done</li><br>";
}

// related modules
//$relatedmodules1 = array('Leads', 'Contacts', 'Accounts', 'HelpDesk', 'Potentials', 'Quotes', 'Invoice', 'SalesOrder',
//    'Calendar', 'Project', 'ProjectTask', 'Events', 'Campaigns', 'ProjectMilestone', 'PurchaseOrder', 'Products', 'Vendors');
$relatedmodules1 = Settings_LayoutEditor_Module_Model::getEntityModulesList();

$sql = "SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=?";
$params = array($field2block1->id, $field2block1->getModuleName());
$result = $adb->pquery($sql, $params);

$dbRelatedmodules1 = array();
$relatedmodules1ToAdd = array();
$relatedmodules1ToRemove = array();

if ($adb->num_rows($result)) {
    while ($row = $adb->fetch_array($result)) {
        $dbRelatedmodules1[] = $row['relmodule'];
    }
}

// add relations
foreach ($relatedmodules1 as $item) {
    if (!in_array($item, $dbRelatedmodules1)) {
        $relatedmodules1ToAdd[] = $item;
    }
}

$field2block1->setRelatedModules($relatedmodules1ToAdd);

// remove relations
foreach ($dbRelatedmodules1 as $item) {
    if (!in_array($item, $relatedmodules1)) {
        $relatedmodules1ToRemove[] = $item;
    }
}

$field2block1->unsetRelatedModules($relatedmodules1ToRemove);

$field3block1 = Vtiger_Field::getInstance('description', $moduleInstance);
if ($field3block1) {
    echo "<li>The 'description' field already exists</li><br>";
} else {
    $field3block1 = new Vtiger_Field();
    $field3block1->label = 'Description';
    $field3block1->name = 'description';
    $field3block1->table = 'vtiger_crmentity';
    $field3block1->column = 'description';
    $field3block1->columntype = 'VARCHAR(100)';
    $field3block1->uitype = 19;
    $field3block1->typeofdata = 'V~O';
    $field3block1->sequence = 2;
    $field3block1->quickcreate = 0;
    $field3block1->quicksequence = 2;
    $field3block1->summaryfield = 1;

    $blockInstance1->addField($field3block1);
    echo "<li>The 'description' field created done</li><br>";
}

$field4block1 = Vtiger_Field::getInstance('notification_status', $moduleInstance);
if ($field4block1) {
    echo "<li>The 'notification_status' field already exists</li><br>";
} else {
    $field4block1 = new Vtiger_Field();
    $field4block1->label = 'Status';
    $field4block1->name = 'notification_status';
    $field4block1->table = 'vtiger_notifications';
    $field4block1->column = 'notification_status';
    $field4block1->columntype = 'VARCHAR(100)';
    $field4block1->uitype = 16;
    $field4block1->typeofdata = 'V~O';
    $field4block1->sequence = 3;
    $field4block1->quickcreate = 0;
    $field4block1->quicksequence = 3;
    $field4block1->summaryfield = 1;

    $blockInstance1->addField($field4block1);
    $field4block1->setPicklistValues(array('No', 'OK'));

    echo "<li>The 'notification_status' field created done</li><br>";
}

$field5block1 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field5block1) {
    echo "<li>The 'assigned_user_id' field already exists</li><br>";
} else {
    $field5block1 = new Vtiger_Field();
    $field5block1->label = 'Assigned To';
    $field5block1->name = 'assigned_user_id';
    $field5block1->table = 'vtiger_crmentity';
    $field5block1->column = 'smownerid';
    $field5block1->columntype = 'VARCHAR(100)';
    $field5block1->uitype = 53;
    $field5block1->typeofdata = 'V~O';
    $field5block1->sequence = 4;
    $field5block1->quickcreate = 0;
    $field5block1->quicksequence = 4;
    $field5block1->summaryfield = 1;

    $blockInstance1->addField($field5block1);

    echo "<li>The 'assigned_user_id' field created done</li><br>";
}

$field6block1 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field6block1) {
    echo "<li>The 'createdtime' field already exists</li><br>";
} else {
    $field6block1 = new Vtiger_Field();
    $field6block1->label = 'Created Time';
    $field6block1->name = 'createdtime';
    $field6block1->table = 'vtiger_crmentity';
    $field6block1->column = 'createdtime';
    $field6block1->uitype = 70;
    $field6block1->typeofdata = 'T~O';
    $field6block1->sequence = 5;
    $field6block1->quickcreate = 1;
    $field6block1->displaytype = 2;
    $field6block1->summaryfield = 1;

    $blockInstance1->addField($field6block1);

    echo "<li>The 'createdtime' field created done</li><br>";
}

$field7block1 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field7block1) {
    echo "<li>The 'modifiedtime' field already exists</li><br>";
} else {
    $field7block1 = new Vtiger_Field();
    $field7block1->label = 'Modified Time';
    $field7block1->name = 'modifiedtime';
    $field7block1->table = 'vtiger_crmentity';
    $field7block1->column = 'modifiedtime';
    $field7block1->uitype = 70;
    $field7block1->typeofdata = 'T~O';
    $field7block1->sequence = 6;
    $field7block1->quickcreate = 1;
    $field7block1->displaytype = 2;
    $field7block1->summaryfield = 1;

    $blockInstance1->addField($field7block1);

    echo "<li>The 'modifiedtime' field created done</li><br>";
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<li>The LBL_CUSTOM_INFORMATION block already exists</li><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

// Create default custom filter (mandatory)
$filter1 = Vtiger_Filter::getInstance('All',$moduleInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1block1)
        ->addField($field2block1, 1)
        ->addField($field3block1, 2)
        ->addField($field4block1, 3)
        ->addField($field5block1, 4);
}