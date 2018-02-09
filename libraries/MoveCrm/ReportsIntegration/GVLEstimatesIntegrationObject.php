<?php
/**
 * Created by PhpStorm.
 * Blah
 */

namespace MoveCrm\ReportsIntegration;

require_once ('libraries/MoveCrm/ReportsIntegration/EstimatesIntegrationObject.php');

class GVLEstimatesIntegrationObject extends EstimatesIntegrationObject {

    /**
     * Function to pull the custom reports password, graebel specific.
     *
     * @param \Vtiger_Record_Model $estimateRecordModel
     *
     * @return string|void
     */
    public function getReportCustomPassword(\Vtiger_Record_Model &$estimateRecordModel) {
        if (!$estimateRecordModel) {
            return;
        }

        $customTariffType = $this->getCustomTariffType($estimateRecordModel->getId());
        switch ($customTariffType) {
            case '400N Base':
                return 'gvl400n104g';
            default:
                return;
        }
    }

    /**
     * Get the custom tariff type from the tariff record ID
     *
     * @param int $tariffID
     *
     * @return string
     */
    private function getCustomTariffType($tariffID) {
        if (!$tariffID) {
            return '';
        }
        try {
            $effectiveTariffRecordModel = \Vtiger_Record_Model::getInstanceById($tariffID);
            return $effectiveTariffRecordModel->get('custom_tariff_type');
        } catch (\Exception $ex) {
            //@NOTE: No throw is needed because we just don't have a custom_tariff_type.
        }
        return '';
    }
}
