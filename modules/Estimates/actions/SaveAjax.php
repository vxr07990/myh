<?php

class Estimates_SaveAjax_Action extends Inventory_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Entering process function\n", FILE_APPEND);
        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
        $recordId = $request->get('record');
        $modName = $request->getModule();
        if ($request->get('field') == 'is_primary' && $request->get('value') == 'on') {
            $db = PearDatabase::getInstance();
            $sql = "SELECT `potentialid` FROM `vtiger_quotes` WHERE `quoteid`=?";
            $params[] = $request->get('record');

            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();
            $potentialid = $row[0];

            //file_put_contents('logs/primary_save.log', "$potentialid\n", FILE_APPEND);

            if ($potentialid != null) {
                $sql = "UPDATE `vtiger_quotes` SET `is_primary`=0 WHERE potentialid=?";
                $params[] = $potentialid;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        }
        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Prior to SELECT call\n", FILE_APPEND);
        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ').$recordId."\n", FILE_APPEND);
        $db = PearDatabase::getInstance();
        if (!isset($recordId) || empty($recordId)) {
            $sql = "SELECT id FROM `vtiger_crmentity_seq`";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();
            $expectedId = $row[0]+1;
        }

        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Prior to parent::process() call\n", FILE_APPEND);
        parent::process($request);
        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."After parent::process() call\n", FILE_APPEND);

        // Notify moveCRMSync service OT 2449
        Surveys_Module_Model::SendSurveyUpdateNotification($recordId, $request->get('assigned_user_id', null), $modName);

        $params = [];
        $rateInfo = $request->get('ratingReturn');
        if (isset($expectedId)) {
            //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Entering ratingReturn section\n", FILE_APPEND);
            $sql = "SELECT quoteid FROM `vtiger_quotes` ORDER BY quoteid DESC LIMIT 1";
            $result = $db->pquery($sql, array());

            $row = $result->fetchRow();
            if ($row[0] == $expectedId) {
                $sql = "UPDATE `vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
                $result = $db->pquery($sql, array($request->get('effective_tariff'), $expectedId));
                //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Entering expectedId section with rateJSON = ".print_r($rateInfo, true)."\n", FILE_APPEND);
                if (isset($rateInfo) && !empty($rateInfo)) {
                    //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Entering !empty(rateInfo) section\n", FILE_APPEND);
                    $sequence = 0;
                    foreach ($rateInfo['lineitemids'] as $lineItem => $itemId) {
                        //file_put_contents('logs/quickSave.log', date('Y-m-d H:i:s - ')."Processing $lineItem\n", FILE_APPEND);
                        $sequence++;
                        $sql = "INSERT INTO `vtiger_inventoryproductrel` (id, productid, sequence_no, quantity, listprice, tax1, tax2, tax3) VALUES (?,?,?,?,?,?,?,?)";
                        $params[] = $expectedId;
                        $params[] = $itemId;
                        $params[] = $sequence;
                        $params[] = 1;
                        $params[] = $rateInfo['lineitems'][$lineItem];
                        $params[] = 0;
                        $params[] = 0;
                        $params[] = 0;

                        $result = $db->pquery($sql, $params);
                        unset($params);
                    }

                    $sql = "UPDATE `vtiger_quotes` SET subtotal=?, total=?, pickup_date=?, valuation_deductible=?, valuation_amount=?, interstate_mileage=? WHERE quoteid=?";
                    $params[] = $rateInfo['rateEstimate'];
                    $params[] = $rateInfo['rateEstimate'];
                    $params[] = date('Y-m-d');
                    $params[] = 'Zero';
                    $params[] = $request->get('weight')*6;
                    $params[] = $rateInfo['mileage'];
                    $params[] = $expectedId;

                    $result = $db->pquery($sql, $params);
                    unset($params);
                }
            }
        }
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

    protected function getObjTypeId($modName)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }
}
