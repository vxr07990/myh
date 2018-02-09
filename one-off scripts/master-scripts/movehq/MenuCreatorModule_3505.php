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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('MenuCreator');

if ($moduleInstance) {
    echo "<h2>MenuCreator module already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'MenuCreator';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();


}
$blockInstance = Vtiger_Block::getInstance('LBL_MENUCREATOR_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The Basic Information block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_MENUCREATOR_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}
//Owner Field
$fieldOwner = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($fieldOwner) {
    echo "<br> The agentid field already exists <br>";
} else {
    $fieldOwner = new Vtiger_Field();
    $fieldOwner->label = 'Owner';
    $fieldOwner->name = 'agentid';
    $fieldOwner->table = 'vtiger_crmentity';
    $fieldOwner->column = 'agentid';
    $fieldOwner->columntype = 'INT(10)';
    $fieldOwner->uitype = 1002;
    $fieldOwner->typeofdata = 'I~M';
    $fieldOwner->quickcreate = 0;
    $fieldOwner->summaryfield = 1;

    $blockInstance->addField($fieldOwner);
    $moduleInstance->setEntityIdentifier($fieldOwner);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
//create MenuGroup Module
$moduleGroups = Vtiger_Module::getInstance('MenuGroups');
if ($moduleGroups) {
    echo "<h2>MenuGroups module already exists </h2><br>";
} else {
    $moduleGroups = new Vtiger_Module();
    $moduleGroups->name = 'MenuGroups';
    $moduleGroups->save();
    $moduleGroups->initTables();
    $moduleGroups->setDefaultSharing();
    $moduleGroups->initWebservice();
}

$blockGroups = Vtiger_Block::getInstance('LBL_MENUGROUPS_INFORMATION', $moduleGroups);
if ($blockGroups) {
    echo "<h3>The Basic Information block already exists</h3><br> \n";
} else {
    $blockGroups = new Vtiger_Block();
    $blockGroups->label = 'LBL_MENUGROUPS_INFORMATION';
    $moduleGroups->addBlock($blockGroups);
}

//Name Field
$fieldgroupname = Vtiger_Field::getInstance('group_name', $moduleGroups);
if ($fieldgroupname) {
    echo "<br> The Group Name field already exists <br>";
} else {
    $fieldgroupname = new Vtiger_Field();
    $fieldgroupname->label = 'LBL_GROUP_NAME';
    $fieldgroupname->name = 'group_name';
    $fieldgroupname->table = 'vtiger_menugroups';
    $fieldgroupname->column = 'group_name';
    $fieldgroupname->columntype = 'varchar(100)';
    $fieldgroupname->uitype = 1;
    $fieldgroupname->typeofdata = 'V~M';
    $fieldgroupname->quickcreate = 0;
    $fieldgroupname->summaryfield = 1;

    $blockGroups->addField($fieldgroupname);
    $moduleGroups->setEntityIdentifier($fieldgroupname);
}

//sequence
$fieldgroupsequence = Vtiger_Field::getInstance('group_sequence', $moduleGroups);
if ($fieldgroupsequence) {
    echo "<br> The group_sequence field already exists <br>";
} else {
    $fieldgroupsequence = new Vtiger_Field();
    $fieldgroupsequence->label = 'LBL_GROUP_SEQUENCE';
    $fieldgroupsequence->name = 'group_sequence';
    $fieldgroupsequence->table = 'vtiger_menugroups';
    $fieldgroupsequence->column = 'group_sequence';
    $fieldgroupsequence->columntype = 'INT(2)';
    $fieldgroupsequence->uitype = 7;
    $fieldgroupsequence->typeofdata = 'I~M';
    $fieldgroupsequence->quickcreate = 0;
    $fieldgroupsequence->summaryfield = 1;

    $blockGroups->addField($fieldgroupsequence);
}

// list module
$fieldgroupmodule = Vtiger_Field::getInstance('group_module', $moduleGroups);
if ($fieldgroupmodule) {
    echo "<br> The group_sequence field already exists <br>";
} else {
    $fieldgroupmodule = new Vtiger_Field();
    $fieldgroupmodule->label = 'LBL_GROUP_MODULE';
    $fieldgroupmodule->name = 'group_module';
    $fieldgroupmodule->table = 'vtiger_menugroups';
    $fieldgroupmodule->column = 'group_module';
    $fieldgroupmodule->columntype = 'TEXT';
    $fieldgroupmodule->uitype = 33;
    $fieldgroupmodule->typeofdata = 'V~O';
    $fieldgroupmodule->quickcreate = 0;
    $fieldgroupmodule->summaryfield = 1;
    $fieldgroupmodule->setPicklistValues(array('Module1', 'Module2', 'Module3'));
    $blockGroups->addField($fieldgroupmodule);
}


$field12 = Vtiger_Field::getInstance('menucreator_id', $moduleGroups);
if ($field12) {
    echo "<br> The menucreator_id field already exists <br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_GROUP_MODULE';
    $field12->name = 'menucreator_id';
    $field12->table = 'vtiger_menugroups';
    $field12->column = 'menucreator_id';
    $field12->columntype = 'TEXT';
    $field12->uitype = 10;
    $field12->typeofdata = 'V~O';
    $field12->quickcreate = 0;
    $field12->summaryfield = 1;
    $blockGroups->addField($field12);
    $field12->setRelatedModules(array('MenuCreator'));
}
// Update entity name
$adb->pquery("UPDATE `vtiger_entityname` 
              SET `tablename`='vtiger_crmentity', `fieldname`='agentid', `entityidfield`='crmid', `entityidcolumn`='crmid' 
              WHERE (`tabid`=?)", array($moduleInstance->id));

$adb->pquery("UPDATE `vtiger_tab` SET `parent`='' WHERE (`tabid`=?)", array($moduleInstance->id));
$adb->pquery("UPDATE `vtiger_tab` SET `parent`='' WHERE (`tabid`=?)", array($moduleGroups->id));



// Add new "description" field to Menu Creator module
//description Field
$fieldOwner = Vtiger_Field::getInstance('description', $moduleInstance);
if ($fieldOwner) {
    echo "<br> The description field already exists <br>";
} else {
    $fieldOwner = new Vtiger_Field();
    $fieldOwner->label = 'Description';
    $fieldOwner->name = 'description';
    $fieldOwner->table = 'vtiger_crmentity';
    $fieldOwner->column = 'description';
    $fieldOwner->columntype = 'INT(10)';
    $fieldOwner->uitype = 19;
    $fieldOwner->typeofdata = 'V~O';
    $fieldOwner->quickcreate = 0;
    $fieldOwner->displaytype = 3;
    $fieldOwner->summaryfield = 1;

    $blockInstance->addField($fieldOwner);
}

// Check if DEFAULT_MENU is existed
$CreatorId = $adb->pquery("SELECT * FROM `vtiger_crmentity` WHERE `description` =?",array("DEFAULT_MENU"));
if ($adb->num_rows($CreatorId) >0){
    echo "<br> The DEFAULT_MENU record already exists <br>";
}
else
{
    global $current_user;
    $current_user= CRMEntity::getInstance("Users");
    $current_user->retrieve_entity_info("1", "Users");
/// Create default menu
    $MenuCreatorRecordModel = Vtiger_Record_Model::getCleanInstance("MenuCreator");
    $MenuCreatorRecordModel->set('mode','');
    $MenuCreatorRecordModel->set('agentid', '16');
    $MenuCreatorRecordModel->set('description', 'DEFAULT_MENU');

    $MenuCreatorRecordModel->save();
    $MenuCreatorId = $MenuCreatorRecordModel->getId();


    $order = array(
        //Sales and Marketing
        'Campaigns',
        'Leads',
        'Opportunities',
        'Surveys',
        'Estimates',

        //Opperations
        'Orders',
        'LocalDispatch',
        'LongDistanceDispatch',
        'Trips',
        'Accounts',
        'Contacts',
        'MovePolicies',
        'Project',

        //Common Services
        'Contacts',
        'Documents',
        'LongDistanceDispatch',
        'Reports',
        'Calendar',
        'HelpDesk',
        // Accounting & Financial Services
        'Actuals',
        'Storage',
        'Claims',
        //System Admin
        'AgentManager',
        'VanlineManager',
        'MailManager',
        'TariffManager',

        //tools
        'EmailTemplates',
        'SMSNotifier',
        'AdvancedReports',
        'PDFMaker'
    );

// Menu Shortcut
    $rsMenuShort = $adb->pquery("SELECT * FROM `vtiger_tab` 
WHERE `presence` IN (?,?) AND `name` NOT IN ( " . generateQuestionMarks($order) . ")", array(0, 2, $order));
    $arrayMenuShortcut = [];
    if ($adb->num_rows($rsMenuShort)) {
        while ($row = $adb->fetchByAssoc($rsMenuShort)) {
            $arrayMenuShortcut[] = $row['name'];
        }
    }

    $MenuGroups1 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups1->set('mode','');
    $MenuGroups1->set('group_name', "Menu Shortcuts");
    $MenuGroups1->set('group_sequence', "0");
    $MenuGroups1->set('group_module', implode(' |##| ', $arrayMenuShortcut));
    $MenuGroups1->set('menucreator_id', $MenuCreatorId);
    $MenuGroups1->save();

// SALES_MARKETING_TAB
    $arraySalesMarketing = array(
        'Campaigns',
        'Leads',
        'Opportunities',
        'Surveys',
        'Estimates'
    );
    $MenuGroups2 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups2->set('mode','');
    $MenuGroups2->set('group_name', "Sales & Marketing");
    $MenuGroups2->set('group_sequence', "1");
    $MenuGroups2->set('group_module', implode(' |##| ', $arraySalesMarketing));
    $MenuGroups2->set('menucreator_id', $MenuCreatorId);
    $MenuGroups2->save();

//Move Management Services
    $arrayMoveManagementServices = array(
        'Orders',
        'LocalDispatch',
        'LongDistanceDispatch',
        'Trips',
        'Accounts',
        'Contacts',
        'MovePolicies',
        'Project'
    );
    $MenuGroups3 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups3->set('mode','');
    $MenuGroups3->set('group_name', "Move Management Services");
    $MenuGroups3->set('group_sequence', "2");
    $MenuGroups3->set('group_module', implode(' |##| ', $arrayMoveManagementServices));
    $MenuGroups3->set('menucreator_id', $MenuCreatorId);
    $MenuGroups3->save();


//Common Services
    $arrayCommonServices = array(
        'Contacts',
        'Documents',
        'LongDistanceDispatch',
        'Reports',
        'Calendar',
        'HelpDesk'
    );
    $MenuGroups4 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups4->set('mode','');
    $MenuGroups4->set('group_name', "Common Services");
    $MenuGroups4->set('group_sequence', "3");
    $MenuGroups4->set('group_module', implode(' |##| ', $arrayCommonServices));
    $MenuGroups4->set('menucreator_id', $MenuCreatorId);
    $MenuGroups4->save();


//Accounting & Financial Services
    $arrayAccounting = array(
        'Actuals',
        'Storage',
        'Claims'
    );
    $MenuGroups5 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups5->set('mode','');
    $MenuGroups5->set('group_name', "Accounting & Financial Services");
    $MenuGroups5->set('group_sequence', "4");
    $MenuGroups5->set('group_module', implode(' |##| ', $arrayAccounting));
    $MenuGroups5->set('menucreator_id', $MenuCreatorId);
    $MenuGroups5->save();


//System Admin
    $arraySystemAdmin = array(
        'AgentManager',
        'VanlineManager',
        'MailManager',
        'TariffManager'
    );
    $MenuGroups6 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups6->set('mode','');
    $MenuGroups6->set('group_name', "System Admin");
    $MenuGroups6->set('group_sequence', "5");
    $MenuGroups6->set('group_module', implode(' |##| ', $arraySystemAdmin));
    $MenuGroups6->set('menucreator_id', $MenuCreatorId);
    $MenuGroups6->save();


//Tools
    $arrayTools = array(
        'EmailTemplates',
        'SMSNotifier',
        'AdvancedReports',
        'PDFMaker'
    );
    $MenuGroups7 = Vtiger_Record_Model::getCleanInstance('MenuGroups');
    $MenuGroups7->set('mode','');
    $MenuGroups7->set('group_name', "Tools");
    $MenuGroups7->set('group_sequence', "6");
    $MenuGroups7->set('group_module', implode(' |##| ', $arrayTools));
    $MenuGroups7->set('menucreator_id', $MenuCreatorId);
    $MenuGroups7->save();

}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";