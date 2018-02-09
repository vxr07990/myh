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



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$db = PearDatabase::getInstance();
include_once 'vtlib/Vtiger/Profile.php';

//Adding the profiles
$roleList = array(
    'Admin User' => 'Admin User Profile',
    'Parent Van Line User' => 'Parent Vanline User',
    'Child Van Line User' => 'Child Vanline User',
    'Sales Manager' => 'Agency Admin',
    'Support Manager' => 'Agent Sales Manager',
    'Coordinator' => 'Agent User',
    'Salesperson' => 'Agent Sales Person',
    'Read-Only User' => 'Read-Only User',
);

$noOwnerAgentModules = array(

    'VanlineManager',
    'AgentManager',
    'Surveys',
    'Calendar',
    'Events',
    'Cubesheets',
    'TariffManager',
    'Agents',
    'Vanlines',
    'Services',
    'EffectiveDates',
    'TariffSections',
    'TariffServices',
);

foreach ($roleList as $roleName => $description) {
    if (!igc_getRole($roleName)) {
        $roleData = array();

        switch ($roleName) {
            case 'Admin User':
                createRole($roleName, 1, 'H1');
                break;
            case 'Parent Van Line User':
                createRole($roleName, 2, igc_getRole('Admin User'));
                break;
            case 'Child Van Line User':
                createRole($roleName, 3, igc_getRole('Parent Van Line User'));
                break;
            case 'Sales Manager':
                createRole($roleName, 4, igc_getRole('Child Van Line User'));
                break;
            case 'Support Manager':
                createRole($roleName, 5, igc_getRole('Sales Manager'));
                break;
            case 'Coordinator':
                createRole($roleName, 6, igc_getRole('Support Manager'));
                break;
            case 'Salesperson':
                createRole($roleName, 7, igc_getRole('Coordinator'));
                break;
            case 'Read-Only User':
                createRole($roleName, 8, igc_getRole('Salesperson'));
                break;

            default:
                break;
        }
    }
}

//Set all entity module to private
/*
$sql = 'UPDATE vtiger_def_org_share, vtiger_tab SET permission = 3 WHERE vtiger_def_org_share.tabid = vtiger_tab.tabid AND vtiger_tab.isentitytype = 1';
$db->pquery($sql, array());


//Recalculating Sharing access
//case matters please be careful with this.
include_once 'Modules/Settings/SharingAccess/models/Module.php';
//Settings_SharingAccess_Module_Model::recalculateSharingRules();
*/
//Add the new UI Types

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES ('1002', 'agentpicklist');");
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES ('1003', 'agentmultipicklist');");

//Adding the new fields in Users

$module = Vtiger_Module::getInstance('Users');

$field = Vtiger_Field::getInstance('agent_ids', $module);

if (!$field) {
    $block1 = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $module);

    $field8 = new Vtiger_Field();
    $field8->label = 'Accesible Agents';
    $field8->name = 'agent_ids';
    $field8->table = 'vtiger_users';
    $field8->column = 'agent_ids';
    $field8->columntype = 'varchar(255)';
    $field8->uitype = 1003;
    $field8->typeofdata = 'V~O';

    $block1->addField($field8);

    $block1->save($module);
}

//Adding the new field to entity Modules

$db = PearDatabase::getInstance();
$result = $db->pquery('SELECT name, blocklabel
                            FROM vtiger_tab INNER JOIN vtiger_blocks ON vtiger_tab.tabid = vtiger_blocks.tabid
                            WHERE isentitytype=1 AND display_status=1
                            GROUP BY name
                            ', array());

if ($result && $db->num_rows($result)>0) {
    while ($row = $db->fetch_row($result)) {
        $moduleName = $row['name'];
        $blockLabel = $row['blocklabel'];

        if (in_array($moduleName, $noOwnerAgentModules)) {
            continue;
        }

        $module = Vtiger_Module::getInstance($moduleName);

        $field = Vtiger_Field::getInstance('agentid', $module);
        if (!$field) {
            $block = Vtiger_Block::getInstance($blockLabel, $module);
            $agentField = new Vtiger_Field();
            $agentField->label = 'Owner Agent';
            $agentField->name = 'agentid';
            $agentField->table = 'vtiger_crmentity';
            $agentField->column = 'agentid';
            $agentField->columntype = 'INT(10)';
            $agentField->uitype = 1002;
            $agentField->typeofdata = 'I~O';

            $block->addField($agentField);
            $block->save($module);
        }
    }
}


// Adding a new column to workflows table

Vtiger_Utils::ExecuteQuery("ALTER TABLE `com_vtiger_workflows` ADD `agents` VARCHAR(100)  NULL  DEFAULT NULL  AFTER `nexttrigger_time`;");

//Set system wide workflows

Vtiger_Utils::ExecuteQuery("UPDATE `com_vtiger_workflows` SET `agents` =0 WHERE `agents` IS NULL;");


function igc_getRole($roleName)
{
    $db = PearDatabase::getInstance();
    $result = $db->pquery('SELECT * FROM vtiger_role WHERE rolename=?', array($roleName));
    if ($result && $db->num_rows($result) > 0) {
        return $db->query_result($result, 'vtiger_role', 0);
    } else {
        return false;
    }
}

function createRole($roleName, $depth, $parentRole)
{
    $db = PearDatabase::getInstance();

    $roleIdNumber = $db->getUniqueId('vtiger_role');
    $roleId = 'H' . $roleIdNumber;
    $parentRoleIdString = igc_getRoleParents($parentRole) . '::' . $roleId;


    $sql = 'INSERT INTO vtiger_role(roleid, rolename, parentrole, depth, allowassignedrecordsto) VALUES (?,?,?,?,?)';
    $params = array($roleId, $roleName, $parentRoleIdString, $depth, 1);
    $db->pquery($sql, $params);
    $picklist2RoleSQL = "INSERT INTO vtiger_role2picklist SELECT '" . $roleId . "',picklistvalueid,picklistid,sortid
					FROM vtiger_role2picklist WHERE roleid = ?";
    $db->pquery($picklist2RoleSQL, array($parentRole));

    $profileId = checkProfile($roleName);

    if (!$profileId) {
        $profilesVtlib = new Vtiger_Profile();
        $profilesVtlib->name = $roleName;
        $profilesVtlib->desc = $roleName . ' associated profile';
        $profilesVtlib->save();
        $profileId = $profilesVtlib->id;

        $db->pquery('UDPATE vtiger_profile SET directly_related_to_role=1 WHERE profileid=?', array($profileId));
        
        //Adding Import/Export and convert lead to profiles

        $sql = "INSERT INTO vtiger_profile2standardpermissions (profileid, tabid, Operation, permissions) 
                            SELECT ?, tabid, actionid, 0 
                    FROM vtiger_actionmapping, vtiger_tab 
                            WHERE actionname IN ('Import', 'Export', 'Merge') AND isentitytype = 1";
        $db->pquery($sql, array($profileId));
        
        $sql = "INSERT INTO vtiger_profile2standardpermissions (profileid, tabid, Operation, permissions) 
                            SELECT ?, tabid, actionid, 0 
                    FROM vtiger_actionmapping, vtiger_tab 
                            WHERE actionname IN ('ConvertLead') AND isentitytype = 1 AND name = 'Leads'";
        $db->pquery($sql, array($profileId));
    }

    $db->pquery('DELETE FROM vtiger_role2profile WHERE roleid=?', array($roleId));
    $db->pquery('INSERT INTO vtiger_role2profile(roleid, profileid) VALUES (?,?)', array($roleId, $profileId));
    $db->pquery('INSERT INTO vtiger_profile2globalpermissions(profileid, globalactionid, globalactionpermission) VALUES (?, ?, ?)', array($profileId, 1, 1));
    $db->pquery('INSERT INTO vtiger_profile2globalpermissions(profileid, globalactionid, globalactionpermission) VALUES (?, ?, ?)', array($profileId, 2, 1));
}

function checkProfile($profileName)
{
    $db = PearDatabase::getInstance();
    $result = $db->pquery('SELECT * FROM vtiger_profile WHERE profilename =?', array($profileName));
    if ($result && $db->num_rows($result) > 0) {
        return $db->query_result($result, 0, 'profileid');
    } else {
        return false;
    }
}

function igc_getRoleParents($roleId)
{
    $db = PearDatabase::getInstance();
    $result = $db->pquery('SELECT parentrole FROM vtiger_role WHERE roleid=?', array($roleId));
    if ($result && $db->num_rows($result) > 0) {
        return $db->query_result($result, 'parentrole', 0);
    } else {
        return false;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";