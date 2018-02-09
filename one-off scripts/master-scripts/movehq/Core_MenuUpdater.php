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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('MenuCreator');

if (!$moduleInstance) {
    echo "<h2>MenuCreator DOES NOT exists </h2><br>";
    return;
}

global $current_user;
$current_user= CRMEntity::getInstance("Users");
$current_user->retrieve_entity_info("1", "Users");

    // Check if DEFAULT_MENU is existed
    $CreatorId = $adb->pquery("SELECT * FROM `vtiger_crmentity` WHERE `setype`='MenuCreator' AND `description`=? LIMIT 1", ["DEFAULT_MENU"]);
    if ($CreatorId && ($adb->num_rows($CreatorId) > 0)) {
        $row           = $CreatorId->fetchRow();
        $MenuCreatorId = $row['crmid'];
    } else {
        /// Create default menu
        $MenuCreatorRecordModel = Vtiger_Record_Model::getCleanInstance("MenuCreator");
        $MenuCreatorRecordModel->set('mode', '');
        //WHAT THE FUCK
        //$menucreatorrecordmodel->set('agentid', '16');
        $menucreatorrecordmodel->set('agentid', '');
        $MenuCreatorRecordModel->set('description', 'DEFAULT_MENU');
        $MenuCreatorRecordModel->save();
        $MenuCreatorId = $MenuCreatorRecordModel->getId();
    }

    $allOrderedTabs = [
        'Sales & Marketing'               => [
            'Campaigns',
            'Leads',
            'Opportunities',
            'Surveys',
            'Estimates'
        ],
        'Move Management Services'        => [
            'Orders',
            'LocalDispatch',
            'LongDistanceDispatch',
            'Trips',
            'Accounts',
            'Contacts',
            'MovePolicies',
            'Project'
        ],
        'Common Services'                 => [
            'Contacts',
            'Documents',
            'LongDistanceDispatch',
            'Reports',
            'Calendar',
            'HelpDesk'
        ],
        'Accounting & Financial Services' => [
            'Actuals',
            'Storage',
            'Claims'
        ],
        'System Admin'                    => [
            'AgentManager',
            'VanlineManager',
            'MailManager',
            'TariffManager'
        ],
        'Tools'                           => [
            'EmailTemplates',
            'SMSNotifier',
            'AdvancedReports',
            'PDFMaker'
        ],
        'Company Admin' => [
            'Employees',
            'Vehicles'
        ]
    ];

    $notMenuShortCut = [];
    $count           = 1;

    foreach ($allOrderedTabs as $groupName => $groupModules) {
        $MenuGroup = MenuGroups_Record_Model::getInstanceByRelatedIdForEdit($MenuCreatorId, $groupName);
        $MenuGroup->set('group_sequence', $count++);
        $MenuGroup->set('group_module', implode(' |##| ', $groupModules));
        $MenuGroup->save();
        foreach ($groupModules as $module) {
            $notMenuShortCut[] = $module;
        }
    }

    $stmt              = "SELECT * FROM `vtiger_tab` WHERE `presence` IN (?,?) AND `name` NOT IN ( ".generateQuestionMarks($notMenuShortCut).")";
    $rsMenuShort       = $adb->pquery($stmt, [0, 2, $notMenuShortCut]);
    $arrayMenuShortcut = [];
    if ($adb->num_rows($rsMenuShort)) {
        while ($row = $adb->fetchByAssoc($rsMenuShort)) {
            $arrayMenuShortcut[] = $row['name'];
        }
    }
    //@NOTE: This adds back ALL missing modules to the Menu Shortcuts group.  Which people won't want because they've deleted some on purpose
//    $menugroupids = $adb->pquery("SELECT `menugroupsid` FROM `vtiger_menugroups` WHERE group_name = 'Menu Shortcuts';",[]);
//    $menugroupids = $menugroupids->getRows();
//    $mgi = Vtiger_Module_Model::getInstance('MenuGroups');
//    foreach($menugroupids as $menugrouparray) {
//      $menugroup = Vtiger_Record_Model::getInstanceById($menugrouparray['menugroupsid'],$mgi);
//      $menugroup->set('mode','edit');
//      $menugroup->set('group_sequence', "0");
//      $menugroup->set('group_module', implode(' |##| ', $arrayMenuShortcut));
//      $menugroup->save();
//
//      // $adb->pquery("UPDATE `vtiger_menugroups` SET `group_module` = ? WHERE menugroupsid = ?",[implode(' |##| ', $arrayMenuShortcut),$menugrouparray['menugroupsid']]);
//    }

     $MenuGroups1 = MenuGroups_Record_Model::getInstanceByRelatedIdForEdit($MenuCreatorId, 'Menu Shortcuts');
     $MenuGroups1->set('group_sequence', "0");
     $MenuGroups1->set('group_module', implode(' |##| ', $arrayMenuShortcut));
     $MenuGroups1->save();
print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
