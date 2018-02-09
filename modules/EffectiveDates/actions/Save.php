<?php

class EffectiveDates_Save_Action extends Vtiger_Save_Action {
	public function process(Vtiger_Request $request) {
        $recordModel = $this->saveRecord($request);

        if($request->get('isDuplicate')) {
            $dupRecord = $request->get('oldRecord');
            $record = $recordModel->getID();
            $otherParams = [];

            $otherParams[] = $record;
            $result = $this->duplicate('TariffServices', 'effective_date', $dupRecord, $record);
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
    protected function duplicate($module, $refRow, $refId, $newId, $table = null, $otherParams = null) {
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

        $query = "SELECT * FROM $table WHERE $refRow=?";
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
                $recordModel = Vtiger_Record_Model::getInstanceById($row[0],$module);
                // For later use
                $tableSuffix = preg_replace('/\s+/', '',strtolower($recordModel->get('rate_type')));
                // special case switch, because names =/= tablesuffix everytime.
                switch($tableSuffix) {
                    case 'baseplustrans.':
                        $tableSuffix = 'baseplus';
                        break;
                    case 'breakpointtrans.':
                        $tableSuffix = 'breakpoint';
                        break;
                }
                $entries = $recordModel->getEntries($tableSuffix);

                $recordModel->setID(null);
                $recordModel->set($refRow,$newId);
                $recordModel->set('record_id',null);
            } else {
                $recordModel = Vtiger_Record_Model::getInstanceById($row[0],$module);
                $recordModel->setID(null);
                $recordModel->set($refRow,$newId);
                $recordModel->set('record_id',null);
            }

            $recordModel->save();
            if($module == 'EffectiveDates' || $module == 'TariffSections') {
                $result[$module] = $recordModel->getId();
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
