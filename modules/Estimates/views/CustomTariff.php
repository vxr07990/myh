<?php

class Estimates_CustomTariff_View extends Estimates_Edit_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showTPG');
        $this->exposeMethod('showBase');
        $this->exposeMethod('getTariffServices');
        $this->exposeMethod('getTariffParkingServices');
        $this->exposeMethod('getTariffCratingServices');
    }

    public function process(Vtiger_Request $request)
    {
        //If it isn't an Ajax request bounce the user back to home.
        //if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){header('Location: index.php?module=Home&view=DashBoard');}
		$mode = $request->getMode();
        echo $this->invokeExposedMethod($mode, $request);

        return;
    }

    public function showTPG(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        //file_put_contents('logs/devLog.log', "\n request in showTPG : ".print_r($request, true), FILE_APPEND);
        parent::assignVars($viewer, $request);
        //$recordModel = Vtiger_Record_Model::getInstanceById($request->get('record'));
        //$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $type        = $request->get('type');
        $record      = $request->get('record');
        $moduleName  = $request->getModule();
        $tariff_type = $request->get('tariff_type');
        $viewer->assign('MODULE', 'Estimates');
        $viewer->assign('MODULE_NAME', 'Estimates');
        $viewer->assign('TARIFF_TYPE', $tariff_type);
        if (!empty($record)) {
            $viewer->assign('CUSTOM_RATES', Vtiger_DetailView_Model::getInstance($moduleName, $record)->getRecord()->getApplyCustomRates());
//            $recordModel = Vtiger_Record_Model::getInstanceById($record);
//            $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        } else {
            $viewer->assign('CUSTOM_RATES', ['apply_custom_sit_rate_override' => '0', 'apply_custom_pack_rate_override' => '0',
                'apply_custom_sit_rate_override_dest'=>'0', 'tpg_custom_crate_rate' => '0']);
        }

        $customTariffTypeForUseCustomRates = ['TPG GRR', 'TPG', 'Pricelock GRR', 'Pricelock', 'Blue Express', 'Allied Express'];
        $viewer->assign('CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES', $customTariffTypeForUseCustomRates);

        if ($type === 'edit') {
            $viewer->view('TPGPricelockEdit.tpl', 'Estimates');
        } elseif ($type === 'detail') {
            $viewer->view('TPGPricelockDetail.tpl', $moduleName);
        }
    }

    public function showBase(Vtiger_Request$request)
    {
        $viewer = $this->getViewer($request);
        $type   = $request->get('type');
        parent::assignVars($viewer, $request);
        $record = $request->get('record');
        if ($record) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        }
        if ($type === 'edit') {
            $viewer->assign('UNHIDE', true);
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('RateEstimateEdit.tpl', 'Estimates');
        } elseif ($type === 'detail') {
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('RateEstimateDetail.tpl', 'Estimates');
        }
    }

    public function getTariffServices(Vtiger_Request $request)
    {
        $agentId = $request->get('agent_id');
        $data = array();
        if (!empty($agentId)) {
            global $adb;
            $arrRateType = array('SIT Cartage','SIT First Day Rate','SIT Additional Day Rate');
            $query = "SELECT DISTINCT T.*,S.* FROM vtiger_tariffservices as S
                        INNER JOIN vtiger_effectivedates as D  ON S.effective_date = D.effectivedatesid
                        INNER JOIN vtiger_tariffs as T ON T.tariffsid = D.related_tariff
                        INNER JOIN vtiger_crmentity as C ON C.crmid = T.tariffsid
                        WHERE C.agentid = ? AND S.rate_type IN (".generateQuestionMarks($arrRateType).")";
            $rs = $adb->pquery($query, array($agentId, $arrRateType));
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetchByAssoc($rs)) {
                    $data[$row['tariffsid']]['tariff_services'][$row['tariffservicesid']] = $row;
                    $data[$row['tariffsid']]['tariff_name'] =$row['tariff_name'];
                    if ($row['rate_type'] == 'SIT Cartage') {
                        $data[$row['tariffsid']]['tariff_services'][$row['tariffservicesid']]['cwt_by_weight'] = $this->getTariffcwtByWeight($row['tariffservicesid']);
                    }
                }
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('TARIFF_SERVICES', $data);
            $viewer->view('ListTariffServices.tpl', 'Estimates');
        }
    }

    /**
     * @param Vtiger_Request $request
     */
    public function getTariffParkingServices(Vtiger_Request $request)
    {
        // Gather effective date.
        $currentUser = Users_Record_Model::getCurrentUserModel();

        //@NOTE: Best way I can think to do with safely and quickly, dates are never huge so O(n) is fine.
        //@NOTE: May break this out into a utility function, given we probably have to do this on multiple occasions.
        $dateFormat = str_split($currentUser->get('date_format'));
        $correctedFormat = ''; $uniqueFlag = false;
        for($i = 0; $i < sizeof($dateFormat); $i++) {
            if(!$uniqueFlag) {
                if($dateFormat[$i] == 'y') {
                    $correctedFormat .= 'Y';
                }else{
                    $correctedFormat .= $dateFormat[$i];
                }
                $uniqueFlag = true;
            }elseif($dateFormat[$i] == '-'){
                $correctedFormat .= '-';
                $uniqueFlag = false;
            }
        }
        $dateFormat = $correctedFormat;

        $effectiveDate = DateTime::createFromFormat($dateFormat,$request->get('effective_date'));

        $agentId = $request->get('agent_id');
        $data = array();
        if (!empty($agentId)) {
            global $adb;
            $arrRateType = array('Packing Items');
            $query = "SELECT DISTINCT T.*,S.* FROM vtiger_tariffservices as S
                        INNER JOIN vtiger_effectivedates as D  ON S.effective_date = D.effectivedatesid
                        INNER JOIN vtiger_tariffs as T ON T.tariffsid = D.related_tariff
                        INNER JOIN vtiger_crmentity as C ON C.crmid = T.tariffsid
                        WHERE C.agentid = ? AND S.rate_type IN (".generateQuestionMarks($arrRateType).")
                        AND D.effective_date < ?
                        AND `deleted` = 0 ORDER BY D.effective_date DESC";
            $rs = $adb->pquery($query, array($agentId, $arrRateType, $effectiveDate->format('Y-m-d')));
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetchByAssoc($rs)) {
                    if($data[$row['tariffsid']]) {
                        continue;
                    }
                    $data[$row['tariffsid']]['tariff_services'][$row['tariffservicesid']] = $row;
                    $data[$row['tariffsid']]['tariff_name'] =$row['tariff_name'];
                    $data[$row['tariffsid']]['tariff_services'][$row['tariffservicesid']]['tariffpackingitems'] = $this->getTariffPackingItemsByServiceId($row['tariffservicesid']);
                }
            }

            $viewer = $this->getViewer($request);
            $viewer->assign('TARIFF_SERVICES', $data);
            $viewer->view('ListTariffServices.tpl', 'Estimates');
        }
    }

    public function getTariffCratingServices(Vtiger_Request $request)
    {
        $agentId = $request->get('agent_id');
        $data = array();
        if (!empty($agentId)) {
            global $adb;
            $arrRateType = array('Crating Item');
            $query = "SELECT DISTINCT T.*,S.* FROM vtiger_tariffservices as S
                        INNER JOIN vtiger_effectivedates as D  ON S.effective_date = D.effectivedatesid
                        INNER JOIN vtiger_tariffs as T ON T.tariffsid = D.related_tariff
                        INNER JOIN vtiger_crmentity as C ON C.crmid = T.tariffsid
                        WHERE C.agentid = ? AND S.rate_type IN (".generateQuestionMarks($arrRateType).")";
            $rs = $adb->pquery($query, array($agentId, $arrRateType));
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetchByAssoc($rs)) {
                    $data[$row['tariffsid']]['tariff_services'][$row['tariffservicesid']] = $row;
                    $data[$row['tariffsid']]['tariff_name'] =$row['tariff_name'];
                }
            }

            $viewer = $this->getViewer($request);
            $viewer->assign('TARIFF_SERVICES', $data);
            $viewer->view('ListTariffServices.tpl', 'Estimates');
        }
    }

    public function getTariffcwtByWeight($id)
    {
        global $adb;
        $result = array();
        $rs = $adb->pquery("SELECT * FROM vtiger_tariffcwtbyweight WHERE serviceid = ?", array($id));
        while ($row = $adb->fetchByAssoc($rs)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getTariffPackingItemsByServiceId($id)
    {
        global $adb;

        $result = array();
        $rs = $adb->pquery("SELECT * FROM vtiger_tariffpackingitems WHERE serviceid = ? GROUP BY serviceid, pack_item_id",
            array($id));

        while ($row = $adb->fetchByAssoc($rs)) {
            $result[$row['pack_item_id']] = $row;
        }

        return $result;
    }
}
