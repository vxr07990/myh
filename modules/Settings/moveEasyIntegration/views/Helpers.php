<?php

class MEZHelpers
{
    public static function checkSubscription($agentID)
    {
        $db = PearDatabase::getInstance();
        $query = $db->pquery("SELECT isSubscribed FROM vtiger_moveeasy_integration WHERE agentID=?", array($agentID));
        $result = $query->fetchRow();
        return $result['isSubscribed'];
    }

    public function getOrganizationDetails()
    {
        $db = PearDatabase::getInstance();
        $query = $db->pquery("SELECT * FROM vtiger_organizationdetails", array());
        $result = $query->fetchRow();

        $return['website'] = $result['website'];
        $return['company_name'] = $result['organizationname'];
        $return['result'] = true;

        if($return['company_name'] == 'IGC Software')
        {
            $return['result'] = false;
        }

        return $return;
    }

    public static function checkForMultipleAgents($current_user)
    {
        $db = PearDatabase::getInstance();
        if(strpos($current_user->agent_ids, ' |##| ') !== false)
        {
            $query = $db->pquery("SELECT * FROM vtiger_vanlinemanager", array());
            $result = $query->fetchRow();
            $agents = explode(' |##| ', $current_user->agent_ids);

            while($result)
            {
                $compare[] = $result['vanlinemanagerid'];
                $result = $query->fetchRow();
            }

            $agents = array_diff($agents, $compare);

            if(empty($agents))
            {
                return false;
            }
            return $agents;
        }
        else
        {
            $query = $db->pquery("SELECT * FROM vtiger_vanlinemanager", array());
            $result = $query->fetchRow();
            $return = $current_user->agent_ids;
            while($result) {
                if ($return == $result['vanlinemanagerid']) {
                    return false;
                }
                $result = $query->fetchRow();
            }
        }
        return $return;
    }

    public static function changeAgentIDSToSomethingReadable($agents){
        $db = PearDatabase::getInstance();
        $x = 0;
        $return = array();
        foreach($agents as $agent)
        {
            $query = $db->pquery("SELECT agency_name FROM vtiger_agentmanager WHERE agentmanagerid=?", array($agent));
            $result = $query->fetchRow();
            $return[$x] = new stdClass();
            $return[$x]->name = $result['agency_name'];
            $return[$x]->id = $agent;
            $x++;
        }
        return $return;
    }

    public static function getAgencyDetails($agentID, $agentLevel)
    {
        $db = PearDatabase::getInstance();

        if($agentLevel)
        {
            $moduleName = "Agent";
            $query= $db->pquery("SELECT * FROM vtiger_agentmanager WHERE agentmanagerid=?", array($agentID));
            $queryResult = $query->fetchRow();
            $return['email'] = $queryResult['email'];
            $return['company_name'] = $queryResult['agency_name'];
        }
        else
        {
            $moduleName = "Vanline";
            $query = $db->pquery("SELECT * FROM vtiger_vanlinemanager", array());
            $queryResult = $query->fetchRow();
            $return['company_name'] = $queryResult['vanline_name'];
        }

        $return['website'] = $queryResult['website'];
        $return['phone'] = $queryResult['phone1'];
        $return['result'] = true;

        if ($return['company_name'] == '') {
            $return['result'] = false;
            $return['reason'] = $moduleName.' name not saved in '.$moduleName.' manager';
        }

        if ($return['website'] == '') {
            $return['result'] = false;
            $return['reason'] = $moduleName.' website not saved in '.$moduleName.' manager';
        }

        if ($return['phone'] == '') {
            $return['result'] = false;
            $return['reason'] = $moduleName.' phone not saved in '.$moduleName.' manager';
        }

        if ($agentLevel && $return['email'] == '') {
            $return['result'] = false;
            $return['reason'] = $moduleName.' email not saved in '.$moduleName.' manager';
        }
        return $return;
    }
}
