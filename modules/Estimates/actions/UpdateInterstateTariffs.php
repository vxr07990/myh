<?php

class Estimates_UpdateInterstateTariffs_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $assignedTo = $request->get('assigned_to');
        $lead_type  = $request->get('lead_type');
        $businessLine = $request->get('business_line');
        $owner = $request->get('owner');
        $billingType = $request->get('billing_type');

        $natBillingTypeAllowed = [
            '743'=>'400N Base Allied',//400N??
            '744'=>'400N/104G Allied',
            '745'=>"Intra - 400N Allied",//Intra -400N?
            '746'=>"Local/Intra Allied",//do they mean Local US tariffs?
            '742'=>"ALLV 2-A Allied",//is this AVL-2A??

            '752'=>"400N Base North American",
            '753'=>"400N/104G North American",
            '754'=>"Intra - 400N North American",
            '755'=>"Local/Intra North American",
            '751'=>"NAVL-12A North America",
        ];
        //file_put_contents('logs/devLog.log', "\n In UpdateLocalTariffs Action and assigned_to : ".$assignedTo, FILE_APPEND);
        $module = $request->getModule();
        $db     = PearDatabase::getInstance();
        //getting the groupname from the user
        //This won't work anymore thanks to new securities
        /*
        $sql    = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
        $result = $db->pquery($sql, [$assignedTo]);
        $row    = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n After first SQL : ".$row[0], FILE_APPEND);
        //getting the agentmanagerid from the groupname
        $sql    = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_name=?";
        $result = $db->pquery($sql, [$row[0]]);
        $row    = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n After second SQL : ".$row[0], FILE_APPEND);
        $userAgents[] = $row[0];
        */
        $assignedUserModel = Users_Record_Model::getInstanceById($assignedTo, 'Users');
        if ($owner) {
            $userAgents = [$owner=>''];
        } else {
            $userAgents        = $assignedUserModel->getAccessibleAgentsForUser();
        }
        $tariffs           = [];
        foreach ($userAgents as $agent => $agentName) {
            $sql    = "SELECT tariffid FROM `vtiger_tariff2agent` WHERE agentid = ?";
            $result = $db->pquery($sql, [$agent]);
            while ($row =& $result->fetchRow()) {
                //set the tariffid as the key so we can loop unique tariffs and not every found tariff.
                $tariffs[$row[0]] = 1;
            }
        }

        //$blah could be made into Active and where it's set to 1 we would check if there was some rule
        //that says if it's found to be off for one agent then it's off even if it's found to be on for another user.
        //but for now it's just a flag to make the array like set.
        foreach ($tariffs as $tariff => $blah) {
            $sql    = "SELECT tariffmanagername, tariff_type, custom_tariff_type, custom_javascript FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ?";
            $result = $db->pquery($sql, [$tariff]);
            $row    = $result->fetchRow();
            if ($billingType=='NAT'&&!isset($natBillingTypeAllowed[$tariff])) {
                continue;
                file_put_contents('logs/ianLog.log', "\n Section 1\n" . $tariff, FILE_APPEND);
            }
            if ($lead_type != 'National Account' && getenv('INSTANCE_NAME') == 'sirva') {
                if (
                    $row['custom_tariff_type'] == 'ALLV-2A' ||
                    $row['custom_tariff_type'] == 'NAVL-12A' ||
                    $row['custom_tariff_type'] == '400N Base' ||
                    $row['custom_tariff_type'] == '400N/104G' ||
                    $row['custom_tariff_type'] == '400NG' //||
//                    $row['custom_tariff_type'] == 'Intra - 400N'
                    //@TODO: what about Intra - 400N ?  that was everywhere else I've been
                ) {
                    continue;
                }
            }
            $tariffNames[$tariff] = $row['tariffmanagername'];
            if ($row['custom_tariff_type']) {
                $tariffTypes[$tariff] = $row['custom_tariff_type'];
            }
            if ($row['custom_javascript']) {
                $tariffJS[$tariff] = $row['custom_javascript'];
            }
            $tariffIntra[$tariff] = ($row['tariff_type'] == 'Intrastate') ? 'intraInterstate' : '';
        }

        if ($businessLine == 'Intrastate Move') {
            $maxTariffs = [];
            //$maxSql     = 'SELECT tariffsid, tariff_name FROM `vtiger_tariffs` WHERE tariff_type LIKE ? OR tariff_type LIKE ?';
            //ensure they aren't deleted, unfortunately requires a join.
            $maxSql = 'SELECT tariffsid, tariff_name FROM `vtiger_tariffs`
                        JOIN `vtiger_crmentity` ON (`vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`)
                        WHERE `vtiger_crmentity`.`deleted`=0
                        AND (tariff_type LIKE ? OR tariff_type LIKE ?)';
            //this was erroring out becuase it didnt have $db, this is a stop-gap to make sure estimates in trunk don't explode
            $db = PearDatabase::getInstance();
            $result = $db->pquery($maxSql, ['Max%3', 'Max%4']);
            while ($row =& $result->fetchRow()) {
                //$maxTariffs[$row[0]] = $row[1];
                $tariffNames[$row[0]] = $row['tariff_name'];
                $tariffTypes[$row[0]] = 'Base';
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $tariffJS[$row[0]] = 'Estimates_BaseSirva_Js';
                } else {
                    $tariffJS[$row[0]] = 'Estimates_BaseTariff_Js';
                }
            }
        }

        $info                  = [];
        $info['tariffs']       = $tariffNames;
        $info['tariffTypes']   = $tariffTypes;
        $info['tariffScripts'] = $tariffJS;
        $info['tariffIntra']   = $tariffIntra;
        //file_put_contents('logs/devLog.log', "\n UpdateInterstateTariff \$info : ".print_r($info,true), FILE_APPEND);
        //$info['currentTariffBlocks'] = $currentTariffBlocks;
        //$info['inactiveTariffBlocks'] = $blocksToHide;
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
