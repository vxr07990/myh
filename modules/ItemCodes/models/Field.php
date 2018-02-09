<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class ItemCodes_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function to get all the available picklist values for the current field
     * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
     */
    public function getPicklistValues($dateStamp = null)
    {
        if($this->getName() == 'itemcodes_tariffservicecode') {
            return $this->getServiceCodes();
        } else {
            return parent::getPicklistValues($dateStamp);
        }
    }

    /**
     * Function to retrieve display value for a value
     *
     * @param  <String> $value - value which need to be converted to display value
     *
     * @return <String> - converted display value
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false) {
        if($this->getName() == 'itemcodes_tariffservicecode') {
            $serviceCodes = $this->getServiceCodes();

            return $serviceCodes[$value];
        } else {
            return parent::getDisplayValue($value, $record, $recordInstance);
        }
    }

    /**
     * Function to check if the cached values for IGC service codes need to be updated from rating
     *
     * @return <Boolean> - true if cached values are older than 4 hours
     */
    private function checkIfUpdateNeeded() {
        $db = PearDatabase::getInstance();
        $sql = "SELECT `update_time` FROM `vtiger_servicecodes_update` WHERE `update_time` > DATE_SUB(?, INTERVAL 4 HOUR)";
        $res = $db->pquery($sql, [date('Y-m-d H:i:s')]);
        if($db->num_rows($res)) {
            //Update from less than 4 hours ago found - no need to pull again
            return false;
        }
        return true;
    }

    /**
     * Function to pull all available IGC service codes from the database cache
     * Retrieves items from rating if the database cache return is empty
     *
     * @return <Array> List of IGC service codes with a description
     */
    private function getServiceCodes() {
        if($this->checkIfUpdateNeeded()) {
            $serviceCodes = $this->getNewServiceCodes();
        }
        if(count($serviceCodes)) {
            return $serviceCodes;
        }

        global $adb;
        $sql = "SELECT * FROM `vtiger_servicecodes` WHERE active=1";
        $result = $adb->query($sql);
        $serviceCodes = [];
        while($row =& $result->fetchRow()) {
            $serviceCodes[$row['code']] = $row['code'].' - '.$row['description'];
        }

        return $serviceCodes;
    }

    /**
     * Function to pull all available IGC service codes from rating
     * Uses description if available or rating item name otherwise
     *
     * @return <Array> List of IGC service codes with a description
     */
    private function getNewServiceCodes() {
        $soapURL = getenv('IGC_SERVICE_CODES_URL');
        if(!$soapURL) {
            return [];
        }
        try {
            $soapClient = new soapclient2($soapURL, 'wsdl');
            $soapClient->setDefaultRpcParams(true);
            $soapProxy = $soapClient->getProxy();
            if (method_exists($soapProxy, 'GetTariffRatingItems')) {
                //@TODO: this should be updated to support a user's vanline instead of defaulting to BASE
                $soapResult = $soapProxy->GetTariffRatingItems(['vanline' => 'BASE']);
            }
        } catch (Exception $ex) {
            file_put_contents('logs/soapFailLog.log', date('Y-m-d H:i:s - ').'Failure while attempting to use GetTariffRatingItems: '.$ex->getMessage()."\n", FILE_APPEND);
            return [];
        }

        $processArray = $soapResult['GetTariffRatingItemsResult']['TariffRatingItem'];
        $resultArray  = [];

        foreach ($processArray as $itemData) {
            $resultArray[$itemData['TariffRatingItemID']] = !empty($itemData['Description'])?$itemData['Description']:$itemData['RatingItem'];
        }

        $this->insertIntoCacheTable($resultArray);

        return $resultArray;
    }

    /**
     * Function to populate the cache table for IGC service codes. Also adjusts value to be <code> - <description>
     *
     * @param <Array> $resultArray - Array holding values returned from rating
     */
    private function insertIntoCacheTable(&$resultArray) {
        global $current_user, $adb;
        $ids = [];

        $sql = "INSERT INTO `vtiger_servicecodes` (code, description, active) VALUES (?,?,?) ON DUPLICATE KEY UPDATE description=VALUES(description), active=VALUES(active)";
        foreach($resultArray as $serviceCode => $description) {
            $adb->pquery($sql, [$serviceCode, $description, 1]);
            $ids[] = $serviceCode;
            $resultArray[$serviceCode] = $serviceCode.' - '.$description;
        }

        $sql = "UPDATE `vtiger_servicecodes` SET active=0 WHERE code NOT IN (".generateQuestionMarks($ids).")";
        $adb->pquery($sql, $ids);

        $sql = "INSERT INTO `vtiger_servicecodes_update` (userid, update_time) VALUES (?,?)";
        $adb->pquery($sql, [$current_user->id, date('Y-m-d H:i:s')]);
    }
}
