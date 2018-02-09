<?php
class VanlineManager_DeleteVanline_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $objectId = $request->get('objectId');
        
        include_once 'include/Webservices/Delete.php';
        include_once 'modules/Users/Users.php';
        
        //delete the vanline
        try {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $wsid = vtws_getWebserviceEntityId('VanlineManager', $objectId); // Module_Webservice_ID x CRM_ID
            vtws_delete($wsid, $current_user);
        } catch (WebServiceException $ex) {
            echo "\n<br> Deleting Vanline Error: ".$ex->getMessage();
        }
        
        //delete all the agents of the vanline
        $usersArray = array();
        $db = PearDatabase::getInstance();
        $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id=?";
        $result = $db->pquery($sql, array($objectId));
        while ($row =& $result->fetchRow()) {
            //gets all the users for the agents before deleting them.
            $sql = "SELECT userid FROM `vtiger_user2agency` WHERE agency_code=?";
            $agentResult = $db->pquery($sql, array($row[0]));
            while ($agentRow =& $agentResult->fetchRow()) {
                $usersArray[] = $agentRow[0];
            }
            file_put_contents('logs/VanlineUser.log', "\n \$row[0]: ".print_r($row[0], true), FILE_APPEND);
            try {
                $user = new Users();
                $wsid = vtws_getWebserviceEntityId('AgentManager', $row[0]); // Module_Webservice_ID x CRM_ID
                vtws_delete($wsid, $current_user);
            } catch (WebServiceException $ex) {
                echo "\n<br> Deleting Agent Error: ".$ex->getMessage();
            }
        }
        
        $sql = "SELECT userid FROM `vtiger_users2vanline` WHERE vanlineid=?";
        $result = $db->pquery($sql, array($objectId));
        while ($row =& $result->fetchRow()) {
            $usersArray[] = $row[0];
        }
        
        //Delete all the users of the vanline
        $temp = false;
        $tempRoles = array();
        $roles = array();
        foreach ($usersArray as $userId) {
            if ($temp != $userId) {
                //get all the roles associated with these users so we can delete those too.
                $sql = "SELECT roleid FROM `vtiger_user2role` WHERE userid=?";
                $result = $db->pquery($sql, array($userId));
                while ($row =& $result->fetchRow()) {
                    $sql = "SELECT parentrole FROM `vtiger_role` where roleid=?";
                    $parentRolesResult = $db->pquery($sql, array($row[0]));
                    $parentRoles = $parentRolesResult->fetchRow();
                    $tempRoles = explode('::', $parentRoles[0]);
                    foreach ($tempRoles as $tempRole) {
                        if ($tempRole != 'H1' && $tempRole!='H2') {
                            $roles[] = $tempRole;
                        }
                    }
                }
                $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
                $result = $db->pquery($sql, array($userId));
                //if the user is a group delete that group as well.
                while ($row =& $result->fetchRow()) {
                    try {
                        $user = new Users();
                        $wsid = vtws_getWebserviceEntityId('Groups', $row[0]); // Module_Webservice_ID x CRM_ID
                        vtws_delete($wsid, $current_user);
                    } catch (WebServiceException $ex) {
                        echo "\n<br> Deleting Groups Error: ".$ex->getMessage();
                    }
                }
                try {
                    $user = new Users();
                    $wsid = vtws_getWebserviceEntityId('Users', $userId); // Module_Webservice_ID x CRM_ID
                    vtws_delete($wsid, $current_user);
                } catch (WebServiceException $ex) {
                    echo "\n<br> Deleting Users Error: ".$ex->getMessage();
                }
                //clear out all the tables that use userid
                $sql = "DELETE FROM `vtiger_users2group` WHERE userid=?";
                $db->pquery($sql, array($userId));
                $sql = "DELETE FROM `vtiger_users2vanline` WHERE userid=?";
                $db->pquery($sql, array($userId));
                $sql = "DELETE FROM `vtiger_user2agency` WHERE userid=?";
                $db->pquery($sql, array($userId));
                $sql = "DELETE FROM `vtiger_user2role` WHERE userid=?";
                $db->pquery($sql, array($userId));
                $sql = "DELETE FROM `vtiger_calendar_user_activitytypes` WHERE userid=?";
                $db->pquery($sql, array($userId));
                $sql = "DELETE FROM `vtiger_users` WHERE id=?";
                $db->pquery($sql, array($userId));
                
                $temp = $userId;
            }
        }
        file_put_contents('logs/VanlineUser.log', "\n \$roles: ".print_r($roles, true), FILE_APPEND);
        $tempRole = false;
        foreach ($roles as $role) {
            if ($role != $tempRole) {
                //delete everything that relates to the roles that we want to get rid of.
                $sql = "DELETE FROM `vtiger_group2role` WHERE roleid=?";
                $db->pquery($sql, array($role));
                $sql = "DELETE FROM `vtiger_role` WHERE roleid=?";
                $db->pquery($sql, array($role));
                $sql = "DELETE FROM `vtiger_role2picklist` WHERE roleid=?";
                $db->pquery($sql, array($role));
                $sql = "DELETE FROM `vtiger_role2profile` WHERE roleid=?";
                $db->pquery($sql, array($role));
                $tempRole = $role;
            }
        }
        $cvId = $request->get('viewname');
        $response = new Vtiger_Response();
        $response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
        $response->emit();
    }
    
    public function curlPOST($post_string, $webserviceURL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);
            
        return $curlResult;
    }
}
