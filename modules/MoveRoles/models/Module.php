<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/vtlib/Vtiger/Module.php');

/**
 * Vtiger Module Model Class
 */
class MoveRoles_Module_Model extends Vtiger_Module_Model
{
    public function getMoveRoles($recordId = false)
    {
        $rows = array();
        if($recordId) {
            $db     = PearDatabase::getInstance();
            $sql    = 'SELECT * FROM `vtiger_moveroles`
							INNER JOIN vtiger_crmentity ON vtiger_moveroles.moverolesid  = vtiger_crmentity.crmid
 							WHERE `moveroles_orders`=? AND vtiger_crmentity.deleted = 0';
            $result = $db->pquery($sql, [$recordId]);
            $db->convert2Sql($sql, [$recordId]);
            if ($db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }
    public function saveMoveRoles($request, $relId)
    {
        for ($index = 1; $index <= $request['numAgents']; $index++) {
            if (!$request['moverolesidId_'.$index]) {
                continue;
            }
            $deleted = $request['moverolesDelete_'.$index];
            $participantId = $request['moverolesidId_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                $recordModel->delete();
            } else {
                if ($participantId == 'none') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("MoveRoles");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                    $recordModel->set('id', $participantId);
                    $recordModel->set('mode', 'edit');
                }
                $recordModel->set('moveroles_role', $request['moveroles_role_'.$index]);
                $recordModel->set('moveroles_employees', $request['moveroles_employees_'.$index]);
                $recordModel->set('moveroles_orders', $relId);
                $recordModel->save();
            }
        }
    }


    public function isSummaryViewSupported()
    {
        return false;
    }
}
