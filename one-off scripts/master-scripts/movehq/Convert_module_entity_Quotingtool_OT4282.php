<?php
if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb, $current_user;
$current_user = new Users();
$current_user->getActiveAdminUser();

$moduleName = 'QuotingTool';
// Refer to QuotingTool (Document Designer)
require_once 'modules/QuotingTool/QuotingTool.php';
$quotingTool = new QuotingTool();
$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!Vtiger_Utils::CheckTable('vtiger_quotingtoolcf')) {
    $stmt = 'CREATE TABLE `vtiger_quotingtoolcf` (
                    `id`  int(11) NULL DEFAULT NULL)';
    $adb->pquery($stmt);
    echo "Created table vtiger_quotingtoolcf success <br>";
}else{
    echo 'vtiger_quotingtoolcf already exists'.PHP_EOL.'<br>';
}

$blockInstance1 = Vtiger_Block::getInstance('LBL_DETAIL_INFORMATION', $moduleInstance);
if ($blockInstance1) {
    echo "<li>The LBL_DETAIL_INFORMATION block already exists</li><br>";
} else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_DETAIL_INFORMATION';
    $moduleInstance->addBlock($blockInstance1);
}

$field2block1 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field2block1) {
    $field2block1 = new Vtiger_Field();
    $field2block1->label = 'Owner';
    $field2block1->name = 'agentid';
    $field2block1->table = 'vtiger_crmentity';
    $field2block1->column = 'agentid';
    $field2block1->uitype = 1002;
    $field2block1->typeofdata = 'I~M';
    $field2block1->sequence = 0;
    $field4block1->summaryfield = 1;
    $blockInstance1->addField($field2block1);
    echo "<li>The 'agentid' field created done</li><br>";
}

$field3block1 = Vtiger_Field::getInstance('filename', $moduleInstance);
if ($field3block1) {
    echo "<li>The 'notificationno' field already exists</li><br>";
} else {
    $field3block1 = new Vtiger_Field();
    $field3block1->label = 'File name';
    $field3block1->name = 'filename';
    $field3block1->table = 'vtiger_quotingtool';
    $field3block1->column = 'filename';
    $field3block1->columntype = 'VARCHAR(100)';
    $field3block1->typeofdata = 'V~O';
    $field3block1->sequence = 1;
    $field4block1->summaryfield = 1;

    $blockInstance1->addField($field3block1);
    echo "<li>The 'filename' field created done</li><br>";
}

$field4block1 = Vtiger_Field::getInstance('module', $moduleInstance);
if ($field4block1) {
    echo "<li>The 'module' field already exists</li><br>";
} else {
    $field4block1 = new Vtiger_Field();
    $field4block1->label = 'Module';
    $field4block1->name = 'module';
    $field4block1->table = 'vtiger_quotingtool';
    $field4block1->column = 'module';
    $field4block1->columntype = 'VARCHAR(100)';
    $field4block1->uitype = 16;
    $field4block1->summaryfield = 1;
    $field4block1->sequence = 2;

    $blockInstance1->addField($field4block1);

    echo "<li>The 'module' field created done</li><br>";
}
$adb->pquery("DELETE FROM `vtiger_picklist` WHERE (`name`='module')");

// picklist values
//    $enableModules = array('Quotes', 'HelpDesk', 'Potentials', 'Contacts', 'Leads', 'Accounts', 'Invoice', 'PurchaseOrder',
//        'SalesOrder', 'ServiceContracts', 'Project', 'ProjectTask', 'ProjectMilestone', 'Estimates', 'Opportunities');
$picklist4 = $quotingTool->enableModules;
$picklist_table = 'vtiger_' . $field4block1->name;
$picklistidCol = $field4block1->name . 'id';
$sql = "SELECT * FROM {$picklist_table}";
$params = array();
$result = $adb->pquery($sql, $params);
$dbPicklist4 = array();

$picklist4ToAdd = array();
$picklist4ToRemove = array();
$picklist4IdToRemove = array();
$firstPicklistId = 0;

// All db picklist
if ($adb->num_rows($result)) {
    while ($row = $adb->fetch_array($result)) {
        $dbPicklist4[$row[$picklistidCol]] = $row[$field4block1->name];
    }
}

// Picklist to add
foreach ($picklist4 as $item) {
    if (!in_array($item, $dbPicklist4)) {
        $picklist4ToAdd[] = $item;
    }
}

// Picklist to remove
foreach ($dbPicklist4 as $id => $item) {
    if (!in_array($item, $picklist4)) {
        $picklist4ToRemove[] = $item;
        $picklist4IdToRemove[] = $id;
    }

    if (!$firstPicklistId && in_array($item, $picklist4)) {
        $firstPicklistId = $id;
    }
}

/** @var Settings_Picklist_Module_Model $settingsPicklistModuleModel */
$settingsPicklistModuleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
// remove picklist
//$settingsPicklistModuleModel->remove($field4block1->name, $picklist4IdToRemove, $firstPicklistId, $moduleName);
$sql = "DELETE FROM {$picklist_table} WHERE {$field4block1->name} IN (" . generateQuestionMarks($picklist4ToRemove) . ")";
$params = array($picklist4ToRemove);
$result = $adb->pquery($sql, $params);

// add relations
$field4block1->setPicklistValues($picklist4ToAdd);


$field5block1 = Vtiger_Field::getInstance('description', $moduleInstance);
if ($field5block1) {
    echo "<li>The 'description' field already exists</li><br>";
} else {
    $field5block1 = new Vtiger_Field();
    $field5block1->label = 'Description';
    $field5block1->name = 'description';
    $field5block1->table = 'vtiger_quotingtool';
    $field5block1->column = 'description';
    $field5block1->columntype = 'VARCHAR(100)';
    $field5block1->typeofdata = 'V~O';
    $field5block1->sequence = 3;
    $field5block1->summaryfield = 1;

    $blockInstance1->addField($field5block1);
    echo "<li>The 'description' field created done</li><br>";
}

$field6block1 = Vtiger_Field::getInstance('created', $moduleInstance);
if ($field6block1) {
    echo "<li>The 'created' field already exists</li><br>";
} else {
    $field6block1 = new Vtiger_Field();
    $field6block1->label = 'Created';
    $field6block1->name = 'created';
    $field6block1->table = 'vtiger_quotingtool';
    $field6block1->column = 'created';
    $field6block1->uitype = '70';
    $field6block1->columntype = 'VARCHAR(100)';
    $field6block1->typeofdata = 'T~O';
    $field6block1->sequence = 4;
    $blockInstance1->addField($field6block1);
    echo "<li>The 'created' field created done</li><br>";
}

$field7block1 = Vtiger_Field::getInstance('body', $moduleInstance);
if ($field7block1) {
    echo "<li>The 'body' field already exists</li><br>";
} else {
    $field7block1 = new Vtiger_Field();
    $field7block1->label = 'Body';
    $field7block1->name = 'body';
    $field7block1->table = 'vtiger_quotingtool';
    $field7block1->column = 'body';
    $field7block1->columntype = 'VARCHAR(100)';
    $field7block1->typeofdata = 'V~O';
    $field7block1->sequence = 5;
    $blockInstance1->addField($field7block1);
    echo "<li>The 'body' field created done</li><br>";
}

$field8block1 = Vtiger_Field::getInstance('header', $moduleInstance);
if ($field8block1) {
    echo "<li>The 'header' field already exists</li><br>";
} else {
    $field8block1 = new Vtiger_Field();
    $field8block1->label = 'Header';
    $field8block1->name = 'header';
    $field8block1->table = 'vtiger_quotingtool';
    $field8block1->column = 'header';
    $field8block1->columntype = 'VARCHAR(100)';
    $field8block1->typeofdata = 'V~O';
    $field8block1->sequence = 6;
    $blockInstance1->addField($field8block1);
    echo "<li>The 'header' field created done</li><br>";
}

$field9block1 = Vtiger_Field::getInstance('content', $moduleInstance);
if ($field9block1) {
    echo "<li>The 'content' field already exists</li><br>";
} else {
    $field9block1 = new Vtiger_Field();
    $field9block1->label = 'Content';
    $field9block1->name = 'content';
    $field9block1->table = 'vtiger_quotingtool';
    $field9block1->column = 'content';
    $field9block1->columntype = 'VARCHAR(100)';
    $field9block1->typeofdata = 'V~O';
    $field9block1->sequence = 7;
    $blockInstance1->addField($field9block1);
    echo "<li>The 'content' field created done</li><br>";
}

$field10block1 = Vtiger_Field::getInstance('footer', $moduleInstance);
if ($field10block1) {
    echo "<li>The 'footer' field already exists</li><br>";
} else {
    $field10block1 = new Vtiger_Field();
    $field10block1->label = 'Footer';
    $field10block1->name = 'footer';
    $field10block1->table = 'vtiger_quotingtool';
    $field10block1->column = 'footer';
    $field10block1->columntype = 'VARCHAR(100)';
    $field10block1->typeofdata = 'V~O';
    $field10block1->sequence = 8;
    $blockInstance1->addField($field10block1);
    echo "<li>The 'footer' field created done</li><br>";
}

$field11block1 = Vtiger_Field::getInstance('anwidget', $moduleInstance);
if ($field11block1) {
    echo "<li>The 'anwidget' field already exists</li><br>";
} else {
    $field11block1 = new Vtiger_Field();
    $field11block1->label = 'Anwidget';
    $field11block1->name = 'anwidget';
    $field11block1->table = 'vtiger_quotingtool';
    $field11block1->column = 'anwidget';
    $field11block1->columntype = 'VARCHAR(100)';
    $field11block1->typeofdata = 'V~O';
    $field11block1->sequence = 9;
    $blockInstance1->addField($field11block1);
    echo "<li>The     'anwidget' field created done</li><br>";
}

$field12block1 = Vtiger_Field::getInstance('deleted', $moduleInstance);
if ($field12block1) {
    echo "<li>The 'deleted' field already exists</li><br>";
} else {
    $field12block1 = new Vtiger_Field();
    $field12block1->label = 'Deleted';
    $field12block1->name = 'deleted';
    $field12block1->table = 'vtiger_quotingtool';
    $field12block1->column = 'deleted';
    $field12block1->columntype = 'VARCHAR(100)';
    $field12block1->typeofdata = 'V~O';
    $field12block1->sequence = 10;
    $blockInstance1->addField($field12block1);
    echo "<li>The 'deleted' field created done</li><br>";
}

$field13block1 = Vtiger_Field::getInstance('updated', $moduleInstance);
if ($field13block1) {
    echo "<li>The 'updated' field already exists</li><br>";
} else {
    $field13block1 = new Vtiger_Field();
    $field13block1->label = 'Updated';
    $field13block1->name = 'updated';
    $field13block1->table = 'vtiger_quotingtool';
    $field13block1->column = 'updated';
    $field13block1->columntype = 'VARCHAR(100)';
    $field13block1->typeofdata = 'V~O';
    $field13block1->sequence = 11;
    $blockInstance1->addField($field13block1);
    echo "<li>The 'updated' field created done</li><br>";
}

$field14block1 = Vtiger_Field::getInstance('email_subject', $moduleInstance);
if ($field14block1) {
    echo "<li>The 'email_subject' field already exists</li><br>";
} else {
    $field14block1 = new Vtiger_Field();
    $field14block1->label = 'Email Subject';
    $field14block1->name = 'email_subject';
    $field14block1->table = 'vtiger_quotingtool';
    $field14block1->column = 'email_subject';
    $field14block1->columntype = 'VARCHAR(100)';
    $field14block1->typeofdata = 'V~O';
    $field14block1->sequence = 12;
    $blockInstance1->addField($field14block1);
    echo "<li>The 'email_subject' field created done</li><br>";
}

$field15block1 = Vtiger_Field::getInstance('email_content', $moduleInstance);
if ($field15block1) {
    echo "<li>The 'email_content' field already exists</li><br>";
} else {
    $field15block1 = new Vtiger_Field();
    $field15block1->label = 'Email Content';
    $field15block1->name = 'email_content';
    $field15block1->table = 'vtiger_quotingtool';
    $field15block1->column = 'email_content';
    $field15block1->columntype = 'VARCHAR(100)';
    $field15block1->typeofdata = 'V~O';
    $field15block1->sequence = 13;
    $blockInstance1->addField($field15block1);
    echo "<li>The 'email_content' field created done</li><br>";
}

$field16block1 = Vtiger_Field::getInstance('mapping_fields', $moduleInstance);
if ($field16block1) {
    echo "<li>The 'mapping_fields' field already exists</li><br>";
} else {
    $field16block1 = new Vtiger_Field();
    $field16block1->label = 'Mapping Fields';
    $field16block1->name = 'mapping_fields';
    $field16block1->table = 'vtiger_quotingtool';
    $field16block1->column = 'mapping_fields';
    $field16block1->columntype = 'VARCHAR(100)';
    $field16block1->typeofdata = 'V~O';
    $field16block1->sequence = 14;
    $blockInstance1->addField($field16block1);
    echo "<li>The 'mapping_fields' field created done</li><br>";
}

$field17block1 = Vtiger_Field::getInstance('attachments', $moduleInstance);
if ($field17block1) {
    echo "<li>The 'attachments' field already exists</li><br>";
} else {
    $field17block1 = new Vtiger_Field();
    $field17block1->label = 'Attachments';
    $field17block1->name = 'attachments';
    $field17block1->table = 'vtiger_quotingtool';
    $field17block1->column = 'attachments';
    $field17block1->columntype = 'VARCHAR(100)';
    $field17block1->typeofdata = 'V~O';
    $field17block1->sequence = 15;
    $blockInstance1->addField($field17block1);
    echo "<li>The 'attachments' field created done</li><br>";
}

// Create default custom filter (mandatory)
$filter1 = Vtiger_Filter::getInstance('All',$moduleInstance);
if(!$filter1) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field2block1, 0)->addField($field3block1, 1)->addField($field4block1, 2)->addField($field5block1, 3);
}








print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";