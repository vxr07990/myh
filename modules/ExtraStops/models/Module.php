<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class ExtraStops_Module_Model extends Vtiger_Module_Model
{
    public function setViewerForStops(&$viewer, $recordId = false)
    {
        //set vars and remove rel_crmid for block view
        $viewer->assign('EXTRASTOPS', true);
        $viewer->assign('EXTRASTOPS_BLOCK_LABEL', 'LBL_EXTRASTOPS_INFORMATION');
        $stopsFields = $this->getFields('LBL_EXTRASTOPS_INFORMATION');
        if (getenv('INSTANCE_NAME') == 'sirva') {
            unset($stopsFields['extrastops_weight']);
            unset($stopsFields['extrastops_isprimary']);
        }
        foreach ($stopsFields as $key => $stopField) {
            $fieldName = $stopField->get('name');
            if ($fieldName == 'extrastops_relcrmid' || $fieldName == 'assigned_user_id' || $fieldName == 'agentid') {
                unset($stopsFields[$key]);
            }
            if (/*getenv('INSTANCE_NAME') != 'sirva' &&*/ $fieldName == 'extrastops_sirvastoptype') {
                unset($stopsFields[$key]);
            }
        }
        if ($recordId) {
            $viewer->assign('EXTRASTOPS_LIST', $this->getStops($recordId));
            $viewer->assign('DEFAULT_PACKING_ITEMS', $this->getDefaultPackingItems($recordId));
        }
        //file_put_contents('logs/devLog.log', "\n StopsFields : ".print_r($stopsFields, true), FILE_APPEND);
        $viewer->assign('EXTRASTOPS_BLOCK_LABEL', 'LBL_EXTRASTOPS_INFORMATION');
        $viewer->assign('EXTRASTOPS_BLOCK_FIELDS', $stopsFields);
    }

    public function saveStops(Vtiger_Request $request, $rel_crmid = false)
    {
        //file_put_contents('logs/devLog.log', "\n it do", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n DP Request : ".print_r($request, true), FILE_APPEND);
        $db = PearDatabase::getInstance();
        //get total # of stops to be saved
        $totalStops = $request->get('numStops');
        //get block fields
        $stopsFields = $this->getFields('LBL_EXTRASTOPS_INFORMATION');
        $user = Users_Record_Model::getCurrentUserModel();
        //file_put_contents('logs/devLog.log', "\n EXSTOP REQUEST : ".print_r($_REQUEST, true), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n hits stop save \n total stops: $totalStops", FILE_APPEND);
        //get the related record id (Opp/Order ID)
        $relatedRecord = $request->get('record') ? $request->get('record') : $rel_crmid;
        for ($i = 1; $i <= $totalStops; $i++) {
            $stopId = $request->get('extrastops_id_'.$i);
            //file_put_contents('logs/devLog.log', "\n SID: $stopId", FILE_APPEND);
            //assemble element data for vtws save
            $element = [];
            foreach ($stopsFields as $fieldName => $stopField) {
                if ($fieldName == 'extrastops_relcrmid') {
                    $element[$fieldName] = vtws_getWebserviceEntityId($request->get('module'), $relatedRecord);
                } elseif ($fieldName == 'assigned_user_id') {
                    if ($request->get('assigned_user_id')) {
                        $element[$fieldName] = vtws_getWebserviceEntityId('Users', $request->get('assigned_user_id'));
                    } else {
                        $element[$fieldName] = vtws_getWebserviceEntityId('Users', $db->pquery('SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?', [$relatedRecord])->fetchRow()['smownerid']);
                    }
                } elseif ($fieldName == 'agentid') {
                    if ($request->get('agentid')) {
                        $element[$fieldName] = $request->get('agentid');
                    } else {
                        $element[$fieldName] = $db->pquery('SELECT agentid FROM `vtiger_crmentity` WHERE crmid = ?', [$relatedRecord])->fetchRow()['agentid'];
                    }
                } elseif ($stopField->get('uitype') == 10) {
                    if ($request->get($fieldName.'_'.$i)) {
                        $relatedModule       = $db->pquery("SELECT relmodule FROM `vtiger_fieldmodulerel` WHERE fieldid = ?", [$stopField->get('id')])->fetchRow()['relmodule'];
                        $element[$fieldName] = vtws_getWebserviceEntityId($relatedModule, $request->get($fieldName.'_'.$i));
                    }
                } elseif ($stopField->get('uitype') == 5 || $stopField->get('uitype') == 23 || $stopField->get('uitype') == 6) {
                    if ($request->get($fieldName.'_'.$i)) {
                        $element[$fieldName] = Vtiger_Date_UIType::getDBInsertValue($request->get($fieldName.'_'.$i));
                    }
                } else {
                    $element[$fieldName] = $request->get($fieldName.'_'.$i);
                }
            }
            //soft delete
            if ($request->get('extrastops_deleted_'.$i) == 'deleted') {
                //file_put_contents('logs/devLog.log', "\n DELETING STOP!!!", FILE_APPEND);
                $db->pquery("UPDATE `vtiger_crmentity` SET deleted = 1 WHERE crmid = ?", [$stopId]);
                continue;
            }
            //vtws create/revise
            if (!$stopId || $stopId == 'none') {
                //insert
                try {
                    vtws_create('ExtraStops', $element, $user);
                } catch (Exception $e) {
                    //file_put_contents('logs/devLog.log', "\n Exception : ".print_r($e, true), FILE_APPEND);
                    //continue loop if vtws fails
                }
            } else {
                //grab existing related record to check and make sure stops don't get hidden input banged
                $oldRelatedRecord = $db->pquery("SELECT extrastops_relcrmid FROM `vtiger_extrastops` WHERE extrastopsid = ?", [$request->get('extrastops_id_'.$i)])->fetchRow()['extrastops_relcrmid'];
                //file_put_contents('logs/devLog.log', "\n SELECT extrastops_relcrmid FROM `vtiger_extrastops` WHERE extrastopsid = ".$request->get('extrastops_id_'.$i), FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n OLD: $oldRelatedRecord \n NEW: $relatedRecord", FILE_APPEND);
                // file_put_contents('logs/devLog.log', "\n Element : ".print_r($element, true), FILE_APPEND);
                if ($relatedRecord == $oldRelatedRecord) {
                    //update
                    try {
                        //file_put_contents('logs/devLog.log', "\n REVISE Element : ".print_r($element, true), FILE_APPEND);
                        $element['id'] = vtws_getWebserviceEntityId('ExtraStops', $request->get('extrastops_id_'.$i));
                        $element['extrastopsid'] = $element['id'];
                        vtws_revise($element, $user);
                    } catch (Exception $e) {
                        //file_put_contents('logs/devLog.log', "\n Exception : ".print_r($e, true), FILE_APPEND);
                        //continue loop if vtws fails
                    }
                }
            }
        }
    }

    //get's stops for a recordID
    // param mixed $mode should be hash: {'type'=> ['origin'|'destination'], 'order' => [true|false]}
    // where order = true returns the elements ordered by sequence hoping for the best on that.
    public function getStops($recordId, $mode = false)
    {
        $extraStops = [];
        $db = PearDatabase::getInstance();

        $stmt = "SELECT * FROM `vtiger_extrastops` "
                . " INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = `vtiger_extrastops`.extrastopsid "
                . " WHERE "
                . " extrastops_relcrmid = ? "
                . " AND deleted = 0";
        $params = [$recordId];

        //smashing this override in so I didn't write another function that was nearly the same.
        if ($mode) {
            if ($mode['type']) {
                $extrastops_type = ['Destination', 'Extra Delivery'];
                if ($mode['type'] == 'origin') {
                    $extrastops_type = ['Origin', 'Extra Pickup'];
                }
                $extras = $this->getByExtrastopType($extrastops_type);
                $stmt .= $extras['stmt'];
                //think array_merge would work, but am concerned about overwrite behavior and feel this is fully fine.
                foreach ($extras['params'] as $val) {
                    $params[] = $val;
                }
            }
            
            if ($mode['order']) {
                $stmt .= ' ORDER BY `extrastops_sequence`';
            }
        }
        
        $result = $db->pquery($stmt, $params);
        $graebel = getenv('INSTANCE_NAME') == 'graebel';
        $core = getenv('IGC_MOVEHQ');
        while ($row =& $result->fetchRow()) {
            if ($graebel) {
                $defaultLabels     = self::getDefaultPackingItems($recordId);

                $packingItemRes = $db->pquery('SELECT * FROM `packing_items_extrastops` WHERE stopid='.$row['extrastopsid']);
                while ($packingItemRow =& $packingItemRes->fetchRow()) {
                    $row['packing_items'][$packingItemRow['itemid']] = ['label' => $packingItemRow['label'], 'pack' => $packingItemRow['pack_qty'], 'unpack' => $packingItemRow['unpack_qty'],
                                                                        'otpack' => $packingItemRow['ot_pack_qty'], 'otunpack' => $packingItemRow['ot_unpack_qty'], 'containers' =>
                                                                            $packingItemRow['containers']];
                }
                foreach ($defaultLabels as $itemId => $defaultLabel) {
                    if (!$row['packing_items'][$itemId]) {
                        $row['packing_items'][$itemId] = $defaultLabel;
                    }
                }
            } elseif ($core) {
                $defaultLabels     = Estimates_Record_Model::getPackingLabelsStatic();
                $packingItemRes = $db->pquery('SELECT * FROM `packing_items_extrastops` WHERE stopid='.$row['extrastopsid']);
                while ($packingItemRow =& $packingItemRes->fetchRow()) {
                    $row['packing_items'][$packingItemRow['itemid']] = ['label' => $packingItemRow['label'], 'pack' => $packingItemRow['pack_qty'], 'unpack' => $packingItemRow['unpack_qty'],
                                                                        'otpack' => $packingItemRow['ot_pack_qty'], 'otunpack' => $packingItemRow['ot_unpack_qty']];
                }
                foreach ($defaultLabels as $itemId => $defaultLabel) {
                    if (!$row['packing_items'][$itemId]) {
                        $row['packing_items'][$itemId] = ['label' => $defaultLabel, 'pack' => 0, 'unpack' => 0, 'customRate' => '0.00', 'otpack' => 0, 'otunpack' => 0];
                    }
                }
            }
            // if this is a relation operation where we want to copy guest blocks, make sure the id is set to none so that they save
            // to the new record
            if ($_REQUEST['sourceRecord'] && !$_REQUEST['record'] && $_REQUEST['relationOperation']) {
                $row[$this->idColumn] = 'none';
                unset($row[$this->linkColumn]);
            }
            $extraStops[] = $row;
        }
        return $extraStops;
    }

    public static function getDefaultPackingItems($recordId = null) {
	$res = [];
	$vanlineId = Estimates_Record_Model::getVanlineIdForNewRecord();
	$tariffId = Estimates_Record_Model::getCurrentAssignedTariffStatic($recordId);
	$tariffName = Estimates_Record_Model::getAssignedTariffName($tariffId);
	if ($vanlineId && $tariffName) {
	    $defaultLabels = Estimates_Record_Model::getPackingLabelsStatic($vanlineId, $tariffName);
	    //$defaultLabels     = Estimates_Record_Model::getPackingLabelsStatic();

	    foreach ($defaultLabels as $itemId => $defaultLabel) {
		$res[$itemId] = ['label' => $defaultLabel, 'pack' => 0, 'unpack' => 0, 'customRate' => '0.00', 'otpack' => 0, 'otunpack' => 0, 'containers' => 0];
	    }
	}

	return $res;
    }

    //this is going to just build a a string to append to the stmt and set to order.
    public function getByExtrastopType($extrastops_type)
    {
        $params = [];
        $stmt = '';

        foreach ($extrastops_type as $type) {
            if ($stmt) {
                $stmt .= ' OR ';
            }
            $stmt .= ' `extrastops_type` = ? ';
            $params[] = $type;
        }
        if ($stmt) {
            $stmt = ' AND (' . $stmt . ') ';
        }
        
        return [
            'params' => $params,
            'stmt' => $stmt
        ];
    }

    public function isSummaryViewSupported()
    {
        return false;
    }
}
