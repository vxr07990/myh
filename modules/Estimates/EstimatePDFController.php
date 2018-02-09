<?php
/*********************************************************************************
 * @author 			Louis Robinson
 * @file 			EstimatePDFCpmtrpller.php
 * @function 		Estimates Module for moveCRM
 * @company 		IGC SOftware
 * @description 	QuotePDFController.php for the Estimates class
 * @contact 		lrobinson@igcsoftware.com
 *
 *********************************************************************************/

require_once 'include/InventoryPDFController.php';
require_once 'modules/Quotes/QuotePDFController.php';

class Vtiger_EstimatePDFController extends Vtiger_QuotePDFController
{
    public function buildHeaderModelTitle()
    {
        $singularModuleNameKey = 'SINGLE_'.$this->moduleName;
        $translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
        if ($translatedSingularModuleLabel == $singularModuleNameKey) {
            $translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
        }
        return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('quote_no'));
    }

    public function getWatermarkContent()
    {
        return $this->focusColumnValue('quotestatus');
    }

    public function buildHeaderModelColumnRight()
    {
        $issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
        $validDateLabel = getTranslatedString('Valid Date', $this->moduleName);
        $billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
        $shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

        $modelColumn2 = array(
                'dates' => array(
                    $issueDateLabel  => $this->formatDate(date("Y-m-d")),
                    $validDateLabel => $this->formatDate($this->focusColumnValue('validtill')),
                ),
                $billingAddressLabel  => $this->buildHeaderBillingAddress(),
                $shippingAddressLabel => $this->buildHeaderShippingAddress()
            );
        return $modelColumn2;
    }
}
