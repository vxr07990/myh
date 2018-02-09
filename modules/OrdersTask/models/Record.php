<?php

class OrdersTask_Record_Model extends Vtiger_Record_Model
{
    protected $taskNonEditableStatus = array('Completed','Canceled');

    /**
     * Function to get the value for a given key
     * @param $key
     * @return Value for the given key
     */
    public function get($key)
    {
	if(($key == 'disp_assignedstart' || $key == 'disp_actualend') && $_REQUEST['view'] == 'NewLocalDispatch') {

	    if($this->valueMap[$key] == ''){
		return '';
	    }

	    $time = DateTimeField::convertToUserTimeZone($this->valueMap[$key]);
	    return $time->format('H:i:s');
	}

        return $this->valueMap[$key];
    }

    public function isTaskEditable()
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if (!$currentUserModel->isAdminUser() && in_array($this->get('orderstaskstatus'), $this->taskNonEditableStatus)) {
            return false;
        } else {
            return true;
        }
    }

    public function getVendors($vendorList, $local = true)
    {
        $vendorArray = array_filter(explode(' |##| ', $vendorList));
        if (is_array($vendorArray) && count($vendorArray) > 0) {
            $nameList = getEntityName('Vendors', $vendorArray);
        } elseif (!$local) {
            return  '';
        }

        if (!$local) {
            $nameList = implode(', ', array_values($nameList));
        }
        return $nameList;
    }

    public function getVehicles($vehicleList, $local = true)
    {
        $vehiclesArray = array_filter(explode(' |##| ', $vehicleList));
        if (is_array($vehiclesArray) && count($vehiclesArray) > 0) {
            $nameList = getEntityName('Vehicles', $vehiclesArray);
        } elseif (!$local) {
            return  '';
        }

        if (!$local) {
            $nameList = implode(', ', array_values($nameList));
        }
        return $nameList;
    }

    public function getLeadEmployee($taskId)
    {
        $db  = PearDatabase::getInstance();
        $leadEmployee = 0;
        $result = $db->pquery("SELECT employeeid FROM vtiger_orderstasksemprel WHERE lead = 1 AND taskid IN (".$taskId.")");
        if ($db->num_rows($result) > 0) {
            $leadEmployee = $db->query_result($result, 0, 'employeeid');
        }

        return $leadEmployee;
    }

    public function getEmployees($array, $local = true)
    {
        $employeeArray = array_filter(explode(' |##| ', $array));
        if (is_array($employeeArray) && count($employeeArray) > 0) {
            $nameList = $this->getEmployeesNames('Employees', $employeeArray); //we cant use getEntityName. Return just the first name
        } elseif (!$local) {
            return  '';
        }

        if (!$local) {
            $nameList = implode(', ', array_values($nameList));
        }
        return $nameList;
    }


    public function getEmployeesNames($module, $ids)
    {
        global $adb;

        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $entityDisplay = array();

        $columns = array('name', 'employee_lastname');

        $sql = sprintf('SELECT ' . implode(',', $columns) . ', employeesid AS id FROM vtiger_employees WHERE employeesid IN (%s)',   generateQuestionMarks($ids));

        $result = $adb->pquery($sql, $ids);

        while ($row = $adb->fetch_array($result)) {
            $entityDisplay[$row['id']] = strtoupper($row['name'][0]) . '. ' . $row['employee_lastname'];
        }


        return $entityDisplay;
    }

    public function getDispatchStatusValue()
    {
        global $adb;

        $result = $adb->pquery("SELECT * FROM vtiger_dispatch_status", array());

        while ($row = $adb->fetch_array($result)) {
            $values[$row['dispatch_statusid']] = $row['dispatch_status'];
        }

        return $values;
    }

    public function getVendorNiceView($vendorUrl)
    {
        $vendorUrl = str_replace("'", '"', $vendorUrl);
        preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $vendorUrl, $result);
        $href = (!empty($result)) ? $result[2][0] : '';

        preg_match('~>\K[^<>]*(?=<)~', $vendorUrl, $match);
        $text = (!empty($match)) ? $match[0] : '';

        $parts = parse_url($href);
        parse_str($parts['query'], $query);
        return array('<option value="'.$query['record'].'" selected>'.$text.'</option>');
    }
    function getDefaultValueForBlocks($blockLabel){
        $defaultData = [];
        $extraBlockConfig = $this->getExtraBlockConfig();
        $config =  $extraBlockConfig[$blockLabel];
        switch ($blockLabel){
            case 'LBL_CPU':
                $orderid = $this->get('ordersid');
                $defaultData = self::getPackingItems($orderid, $config);
                break;
            case 'LBL_PERSONNEL':
            case 'LBL_VEHICLES':
                foreach($config['fields'] as $fieldname){
                    if($fieldname =='personnel_type'){
                        $defaultData[1][$fieldname] = -1;
                    }elseif($fieldname =='vehicle_type'){
                        $defaultData[1][$fieldname] = 'Any Vehicle Type';
                    }else{
                        $defaultData[1][$fieldname] = '';
                    }
                }
                break;
            case 'LBL_EQUIPMENT':
                $orderid = $this->get('ordersid');
                if(!empty($orderid)){
                    $defaultData = $this->getEquipmentItems($orderid);
                }
            default:
                break;
        }
        return $defaultData;
    }

    static function  getExtraBlockConfig(){
        return [
            'LBL_PERSONNEL'=>[
                'isDynamicBlock'=>true,
				'fields'=>['num_of_personal','personnel_type'],
            ],
            'LBL_VEHICLES'=>[
                'isDynamicBlock'=>true,
                'fields'=>['num_of_vehicle','vehicle_type'],
            ],
//            'LBL_ADDRESSES'=>[],
            'LBL_CPU'=>[
                'isDynamicBlock'=>true,
                'fields'=>['carton_name','cartonqty','packingqty','unpackingqty'],
            ],
            'LBL_CPU_ACTUALS'=>[
                'isDynamicBlock'=>true,
                'fields'=>['carton_name','cartonqty','packingqty','unpackingqty'],
            ],
            'LBL_EQUIPMENT'=>[
                'isDynamicBlock'=>true,
                'fields'=>['equipment_name','equipmentqty'],
            ],
            'LBL_EQUIPMENT_ACTUALS'=>[
                'isDynamicBlock'=>true,
                'fields'=>['equipment_name','equipmentqty'],
            ],
        ];
    }
    function saveExtraTableBlocks($request){
        global $adb;
        $extraBlocksConfigs = $this->getExtraBlockConfig();
        $recordId = $this->getId();
        foreach ($extraBlocksConfigs as $blockLabel => $setting){
            $numRow = $request->get('numItem_'.$blockLabel);
            if($numRow && $numRow > 0){
                for($i = 1; $i<=$numRow;$i++){
                    foreach ($setting['fields'] as $fieldname){
                        $rsCheck = $adb->pquery("SELECT * FROM vtiger_orderstask_extra WHERE orderstaskid =? AND blocklabel =? AND fieldname = ? AND sequence =?",
                            [$recordId,$blockLabel,$fieldname,$i]);
                        $fieldvalue = $request->get($fieldname.'_'.$i);
                        if($adb->num_rows($rsCheck) >0){
                            $itemId = $adb->query_result($rsCheck,0,'extraid');
                            $deleteItem = $request->get('itemDelete_'.$blockLabel.'_'.$i);
                            if($deleteItem == 'deleted'){
                                $sql = "DELETE FROM vtiger_orderstask_extra WHERE extraid =?";
                                $params = [$itemId];
                            }else{
                                $sql = "UPDATE vtiger_orderstask_extra SET fieldvalue = ? WHERE extraid =?";
                                $params = [$fieldvalue,$itemId];
                            }
                            $adb->pquery($sql,$params);
                        }else{
							if($fieldvalue != ""){
								$sql = "INSERT INTO vtiger_orderstask_extra(`orderstaskid`,`blocklabel`,`sequence`,`fieldname`,`fieldvalue`) VALUES(?,?,?,?,?)";
                                $params = [$recordId,$blockLabel,$i,$fieldname,$fieldvalue];
                                $adb->pquery($sql,$params);
							}
						}

                    }
                }
            }
        }
    }

	function getActualsItems($blockLabel){//this function isnt used any more, OT3592 now Actual CPUs and Equipment are saved in vtiger_orderstask_extra
        global $adb;
        $recordId = $this->getId();
		$sql = ($blockLabel == "LBL_CPU") ? "SELECT jsonCPU FROM vtiger_orderstask_cpus " : "SELECT jsonEquipment FROM vtiger_orderstask_equipments ";
		$sql .= "WHERE orderstaskid = ? ORDER BY date DESC";

		$rs = $adb->pquery($sql,[$recordId]);
		$json = json_decode($adb->query_result($rs, 0));

		return $json;
	}

    function getExtraBlockFieldValues($blockLabel){
        global $adb;
        $data = [];
        $recordId = $this->getId();
        if($recordId && $blockLabel){
            $rs = $adb->pquery("SELECT * FROM vtiger_orderstask_extra WHERE orderstaskid =? AND blocklabel = ?",[$recordId,$blockLabel]);
            while ($row = $adb->fetchByAssoc($rs)){
                $data[$row['sequence']][$row['fieldname']] = $row['fieldvalue'];
            }
        }

        return $data;
    }
    public  static  function getEquipmentItems($orderId){
        global $adb;
        $data = [];
        $i = 1;
        if( $orderId && $orderId > 0 ){


            $rsRelated = $adb->pquery("SELECT quoteid FROM vtiger_quotes INNER JOIN vtiger_crmentity ON vtiger_quotes.quoteid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_quotes.orders_id = ? AND vtiger_quotes.is_primary = '1' AND pricing_mode = 'Estimate'",array($orderId));
            while ($row = $adb->fetchByAssoc($rsRelated)){
                $primaryEstimateId = $row['quoteid'];
                $estimateRecordModel = Vtiger_Record_Model::getInstanceById($primaryEstimateId, 'Estimates');

                if($estimateRecordModel->get('business_line_est') == 'Local'){
                    $tariffRecordModel = Tariffs_Record_Model::getInstanceById($estimateRecordModel->get('effective_tariff'), 'Tariffs');

                    if($tariffRecordModel){
                        $localTariff = $estimateRecordModel->get('effective_tariff');
                        $effective_date = $estimateRecordModel->get('effective_date');
                        $sql             = "SELECT effectivedatesid FROM `vtiger_effectivedates`
                                            INNER JOIN `vtiger_crmentity` ON (crmid=effectivedatesid)
                                            WHERE effective_date <= ? AND related_tariff = ? AND deleted=0
                                            ORDER BY `vtiger_effectivedates`.`effective_date` DESC LIMIT 1";
                        $result          = $adb->pquery($sql, [$effective_date, $localTariff]);
                        $row             = $result->fetchRow();
                        $effectiveDateId = $row['effectivedatesid'];


                        //Get the equipment related to the local tariff

                        $sqlEq  = "SELECT vtiger_quotes_perunit.qty1, vtiger_tariffservices.tariffservices_assigntorecord, vtiger_equipment.name as equiptmentname
                        FROM vtiger_tariffservices 
                        JOIN `vtiger_crmentity` ON tariffservicesid=crmid
                        JOIN vtiger_quotes_perunit ON vtiger_quotes_perunit.serviceid = vtiger_tariffservices.tariffservicesid
                        JOIN vtiger_equipment ON vtiger_tariffservices.tariffservices_assigntorecord=vtiger_equipment.`equipmentid`
                        JOIN vtiger_tariffsections ON vtiger_tariffservices.tariff_section = vtiger_tariffsections.tariffsectionsid
                        WHERE deleted=0  AND tariffservices_assigntorecord > 0 AND effective_date=? AND tariffservices_assigntomodule='Equipment' AND estimateid=?";

                        $resultEq         = $adb->pquery($sqlEq, [$effectiveDateId, $primaryEstimateId]);
                        if($resultEq && $adb->num_rows($resultEq) > 0){
                            while ($rowEq = $adb->fetchByAssoc($resultEq)){
                                    $data[$i]['equipment_name'] = $rowEq['tariffservices_assigntorecord'];
                                    $data[$i]['equipment_name_display']= $rowEq['equiptmentname'];
                                    $data[$i]['equipmentqty'] = intval($rowEq['qty1']);
                                    $i++;

                            }
                        }

                    }

                }else{
                    $detailLineItems = $estimateRecordModel->getDetailLineItems($primaryEstimateId);
                    if(array_key_exists('Equipment', $detailLineItems)){

                        $currentUserModel = Users_Record_Model::getCurrentUserModel();
                        $accesibleAgents = array_keys($currentUserModel->getAccessibleOwnersForUser('Equipment'));

                        foreach ($detailLineItems['Equipment'] as $equipmentInfo) {
                            //Could not find a better way. There is no ID in vtiger_detailed_lineitems table :/
                            $res = $adb->pquery('SELECT equipmentid FROM vtiger_equipment JOIN `vtiger_crmentity` ON vtiger_equipment.equipmentid=crmid 
                            WHERE name=? AND agentid IN ( ' . generateQuestionMarks($accesibleAgents) . ' )', [$equipmentInfo['ServiceDescription'], $accesibleAgents]);

                            if($res && $adb->num_rows($res) > 0){
                                $data[$i]['equipment_name'] = $adb->query_result($res,0,'equipmentid');
                                $data[$i]['equipment_name_display']= $equipmentInfo['ServiceDescription'];
                                $data[$i]['equipmentqty'] = intval($equipmentInfo['Quantity']);
                                $i++;
                            }


                        }
                    }


                }
            }
        }

        return $data;
    }
    public  static  function getPackingItems($orderId, $config){
        global $adb;
        $data = [];
        $seq = 0;

        if( $orderId && $orderId > 0 ){
            $rsRelated = $adb->pquery("SELECT quoteid FROM vtiger_quotes INNER JOIN vtiger_crmentity ON vtiger_quotes.quoteid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_quotes.orders_id = ? AND vtiger_quotes.is_primary = '1' AND pricing_mode = 'Estimate'",array($orderId));

            while ($row = $adb->fetchByAssoc($rsRelated)){

                $i = 1;

                //This is for local tariffs

                $sql    = "SELECT * FROM `vtiger_quotes_packing` WHERE `estimateid` =?";
                $result = $adb->pquery($sql, [$row['quoteid']]);
                if ($adb->num_rows($result) > 0) {
                    while ($rowPacks =& $result->fetchRow()) {
                        if($rowPacks['container_qty'] > 0 || $rowPacks['pack_qty'] > 0 || $rowPacks['unpack_qty'] > 0){
                            $data[$i]['carton_name'] = $rowPacks['name'];
                            $data[$i]['cartonqty'] = $rowPacks['container_qty'];
                            $data[$i]['packingqty'] = $rowPacks['pack_qty'];
                            $data[$i]['unpackingqty'] = $rowPacks['unpack_qty'];
                            $i++;
                        }

                    }
                }


                //this is for interstate. Why diff? no idea

                $estimateRecordModel = Vtiger_Record_Model::getInstanceById($row['quoteid'], 'Estimates');
                $packingItems = $estimateRecordModel->getPackingItems();

                foreach ($packingItems as $packingItem) {
                    if($packingItem['pack'] > 0 ){
                        $data[$i]['carton_name'] = $packingItem['label'];
                        $data[$i]['packingqty'] = $packingItem['pack'];
                        $i++;
                    }
                }

            }
        }

        return $data;

    }
}
