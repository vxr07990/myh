<?php
/**
 * blah
 */

namespace MoveCrm\ReportsIntegration;

interface IReportsIntegration {

    /**
     * Return the available reports.
     *
     * @return mixed
     */
    public function getAvailableReports(\Vtiger_Request &$request);

    /**
     * Return the report data.
     *
     * @return mixed
     */
    public function getReport(\Vtiger_Request &$request);

    /**
     * Return if there has been an error.
     *
     * @return bool
     */
    public function checkError();

    /**
     * Return an array of
     *
    'error'        => bool
    'errorCode'    => int
    'errorMessage' => string
     *
     * @return array
     */
    public function getLastError();
}
