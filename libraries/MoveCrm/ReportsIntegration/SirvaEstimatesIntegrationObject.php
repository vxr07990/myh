<?php

/**
 * Created by PhpStorm.
 * blah
 */

namespace MoveCrm\ReportsIntegration;

require_once ('libraries/MoveCrm/ReportsIntegration/EstimatesIntegrationObject.php');

class SirvaEstimatesIntegrationObject extends EstimatesIntegrationObject {

    /**
     * function to get the vanline ID for a report FOR sirva
     *
     * @param \Vtiger_Record_Model $estimatesRecordModel
     *
     * @return int
     */
    public function getReportVanlineId(\Vtiger_Record_Model &$estimatesRecordModel) {
        //@NOTE: this is added in for testing or overriding an instances normal id.
        if ($this->get('vanlineOverride')) {
            return $this->get('vanlineOverride');
        }

        return 18;
    }

    /**
     * Function to pull the custom reports password, sirva specific.
     *
     * @param \Vtiger_Record_Model $estimateRecordModel
     *
     * @return string|void
     */
    public function getReportCustomPassword(\Vtiger_Record_Model &$estimateRecordModel) {
        if (!$estimateRecordModel) {
            return;
        }
        $vanLineId = \Estimates_Record_Model::getVanlineIdStatic($estimateRecordModel->getId());
        if ($vanLineId === 1) {
            return 'qlabavl';
        } elseif ($vanLineId === 9) {
            return 'qlabnavl';
        }
    }
}
