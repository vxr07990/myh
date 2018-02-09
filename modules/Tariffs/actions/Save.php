<?php

class Tariffs_Save_Action extends Vtiger_Save_Action {
	public function process(Vtiger_Request $request) {
        $recordModel = $this->saveRecord($request);

        if($request->get('isDuplicate')) {
            $dupRecord = $request->get('oldRecord');
            $record = $recordModel->getID();
            $otherParams = [];
            $result = $this->duplicate('EffectiveDates', 'related_tariff', $dupRecord, $record);
            if(!$result['success']) {
                throw new Exception('Error duplicating Effective Dates: '.$result['message']);
            }
            $otherParams[] = $result['EffectiveDates'];
            $result = $this->duplicate('TariffSections', 'related_tariff', $dupRecord, $record);
            if(!$result['success']) {
                throw new Exception('Error duplicating Tariff Sections: '.$result['message']);
            }
            $otherParams[] = $result['TariffSections'];
            $result = $this->duplicate('TariffReportSections', 'tariff_orders_tariff', $dupRecord, $record);
            if(!$result['success']) {
                throw new Exception('Error duplicating Tariff Sections: '.$result['message']);
            }
            $result = $this->duplicate('TariffServices', 'related_tariff', $dupRecord, $record, NULL, NULL, $otherParams);
            if(!$result['success']) {
                throw new Exception('Error duplicating Tariff Services: '.$result['message']);
            }
        }

        if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
        header('Location: '.$loadUrl);
	}

    /* Duplicate rows in specified table by reference ID, and add new CF entry (if true).
     * $module = module to duplicate rows in
     * $refRow = row to reference with $refId
     * $refId = reference ID to find rows to duplicate
     * $newId = ID for new rows to reference
     * $table = manually send table name, needed if table is named differently than module.
     *
     * return = array with status (success (true), failed (false)), and a message if failed.
     */
    protected function duplicate($module, $refRow, $refId, $newId, $idRow = null, $table = null, $otherParams = null) {
        global $adb;

        $result = ['success' => true];
        // Basic error handling to avoid HTTP 500 responses.
        if(empty($module) || empty($refRow) || empty($refId) || empty($newId)) {
            $result = [
                'success' => false,
                'message' => 'Sent empty variable.'
            ];
            return $result;
        }
        if(empty($table)) {
            $table = 'vtiger_'.strtolower($module);
        }
        if(empty($idRow)) {
            $idRow = strtolower($module).'id';
        }
        $query = "SELECT * FROM $table JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $table.$idRow WHERE $refRow=? AND vtiger_crmentity.deleted != 1";

        if($module == 'EffectiveDates' || $module == 'TariffSections') {
            $query .= ' AND related_tariff IS NOT NULL';
        }

        $selection = $adb->pquery($query, [$refId]);
        if(!$selection) {
            $result = [
                'success' => false,
                'message' => 'Error while gathering information to duplicate.'
            ];
            return $result;
        }

        while($row = $adb->getNextRow($selection)) {
            if($module == 'TariffServices') {
                $recordModel = Vtiger_Record_Model::getInstanceById($row['tariffservicesid'],$module);
                if (!$recordModel) {
                    //Skip records that do not exist. in case they don't exist somehow because you are bad.
                    continue;
                }

                if (!$recordModel->get('effective_date') || !$otherParams[0][$row['effective_date']]) {
                    //Skip records that do not have effective_date.
                    continue;
                }

                // For later use
                $tableSuffix = preg_replace('/\s+/', '',strtolower($recordModel->get('rate_type')));
                // special case switch, because names != tablesuffix everytime.
                switch($tableSuffix) {
                    case 'baseplustrans.':
                        $tableSuffix = 'baseplus';
                        break;
                    case 'breakpointtrans.':
                        $tableSuffix = 'breakpoint';
                        break;
                }
                $entries = $recordModel->getEntries($tableSuffix);

                $effDate = null;
                try {
                    $effDate = Vtiger_Record_Model::getInstanceById($otherParams[0][$row['effective_date']], 'EffectiveDates');
                }catch(Exception $e) {
                    file_put_contents("logs/localTariffDuplicationErrors.log","(".date("Y-m-d H:i:s").") Error getting Effective Dates: ".$e->getMessage()."\n", FILE_APPEND);
                    return [
                        'success' => 'false',
                        'message' => $e->getMessage()
                    ];
                }

                $tariffSection = null;
                try {
                    $tariffSection = Vtiger_Record_Model::getInstanceById($otherParams[1][$row['tariff_section']], 'TariffSections');
                }catch(Exception $e) {
                    file_put_contents("logs/localTariffDuplicationErrors.log","(".date("Y-m-d H:i:s").") Error getting Tariff sections: ".$e->getMessage()."\n", FILE_APPEND);
                    return [
                        'success' => 'false',
                        'message' => $e->getMessage()
                    ];
                }
                if(empty($tariffSection) || empty($effDate)) {
                    file_put_contents("logs/localTariffDuplicationErrors.log","(".date("Y-m-d H:i:s").") Tariff Section or Effective Date is missing for ".$recordModel->getID()." (".$recordModel->get('service_name').".) Continuing on.\n", FILE_APPEND);
                    continue;
                }

                $recordModel->setID(null);
                $recordModel->set($refRow,$newId);
                $recordModel->set('record_id',null);
                $recordModel->set('effective_date',$effDate->getID());
                $recordModel->set('tariff_section',$tariffSection->getID());
            } else {
                $recordModel = Vtiger_Record_Model::getInstanceById($row[0],$module);
                $recordModel->setID(null);
                $recordModel->set($refRow,$newId);
                $recordModel->set('record_id',null);
            }
            // This is a ridiculous thing and it's breaking everything because TariffSections are silently not saving.
            if($_REQUEST['repeat']) {
                $_REQUEST['repeat'] = false;
            }

            $recordModel->save();
            if($module == 'EffectiveDates') {
                $result[$module][$row['effectivedatesid']] = $recordModel->getId();
            } elseif ($module == 'TariffSections') {
                $result[$module][$row['tariffsectionsid']] = $recordModel->getId();
            } elseif ($module == 'TariffServices') {
                // Unfortunately necessary custom logic in order to correctly save local packing items on dup.
                if($tableSuffix == 'packingitems') {
                    $recordModel->wipeEntries($tableSuffix, 'serviceid', $recordModel->getId());
                }
                foreach($entries as $entry) {
                    //Gross, but will eliminate the extra fields that are there for some reason
                    foreach($entry as $key=>$val){
                        if(is_int($key)) { unset($entry[$key]); }
                    }
                    $entry['serviceid'] = $recordModel->getId();
                    $entry['line_item_id'] = NULL;
                    $recordModel->setEntry($tableSuffix,$entry);
                }
            }
        }

        return $result;
    }
}

?>
