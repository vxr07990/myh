<?php
/**
 * Created by PhpStorm.
 * BLAH
 */

namespace MoveCrm\ReportsIntegration;

interface IReportsIntegrationObject {

    /**
     * function to retrieve the estimate record model from a record's id.
     *
     * This allows you to pass in an order or opportunity's record id and get the primary estimate.
     * Or you pass in the estimate id and get that record.
     *
     * @param int $recordId
     *
     * @return bool|\Vtiger_Record_Model
     */
    public function getRecordModel($recordId);

    /**
     * Retrieve the application ID for this object.
     *
     * @param \Vtiger_Request $request
     *
     * @return int|null
     */
    function getApplicationId(\Vtiger_Request &$request);

    /**
     * function to get the vanline ID for a report from the estimates record model.
     *
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return int
     */
    function getReportVanlineId(\Vtiger_Record_Model &$recordModel);

    /**
     * function returns if it's Interstate or local_tariff type of report.
     *
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return string
     */
    function getReportPricingMode(\Vtiger_Record_Model &$recordModel);

    /**
     * function returns the estimate type fo the record this is like Binding, Non Binding, etc
     *
     * @param \Vtiger_Request $request
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return string
     */
    function getReportEstimateType(\Vtiger_Request &$request, \Vtiger_Record_Model $recordModel);

    /**
     * function to return whether the report isIntrastate or not based on whether the states
     * match.  This is the criteria provided by reports for isIntra to be true.
     *
     * @param \Vtiger_Request $request
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return bool
     */
    function getReportIsIntra(\Vtiger_Request &$request, \Vtiger_Record_Model $recordModel);

    /**
     * Function to pull the custom reports password, it could be agent specific.  (or gvl/sirva specific)
     *
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return string|void
     */
    function getReportCustomPassword (\Vtiger_Record_Model &$recordModel);

    /**
     * Function to get the query mode for reports.
     * @NOTE: Nobody know what this is, but maybe you will in the future.
     *
     * @param \Vtiger_Record_Model $recordModel
     *
     * @return mixed
     */
    function getReportQueryMode (\Vtiger_Record_Model &$recordModel);

    /**
     * @param int $recordId
     *
     * @return string
     */
    public function getReportCustomerData($recordId);
}