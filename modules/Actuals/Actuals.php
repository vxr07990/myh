<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('vendor/nesbot/carbon/src/Carbon/Carbon.php');
use Carbon\Carbon;

require_once('modules/Estimates/Estimates.php');
require_once('libraries/MoveCrm/GraebelAPI/invoiceHandler.php');
require_once('libraries/MoveCrm/GraebelAPI/revenueHandler.php');

class Actuals extends Estimates
{

    /**    Constructor which will set the column_fields in this object
     */
    public function Actuals()
    {
        $this->log           = LoggerManager::getLogger('quote');
        $this->db            = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Actuals');

        // Set stage to use actuals field
        $this->list_fields['Quote Stage'] = ['quotes' => 'actuals_stage'];
        $this->list_fields_name['Quote Stage'] = 'actuals_stage';
        $this->search_fields['Quote Stage'] = ['quotes' => 'actuals_stage'];
        $this->search_fields_name['Quote Stage'] = 'actuals_stage';
    }

    public function saveentity($module, $fileid = '')
    {
        $newSave = true;
        if ($_REQUEST['record']) {
            $newSave = false;
        }
        $originalDLI = $this->getDetailLineItemArray($module);
        parent::saveentity($module, $fileid);
        $currentDLI = $this->getDetailLineItemArray($module);

        $fieldList = array_merge($_REQUEST, $this->column_fields);
        if ($this->checkForInvoice($originalDLI, $currentDLI)) {
            $changedLineItems = [
                'removeReadyToInvoice' => $this->checkForDeletedInvoiceLines($originalDLI, $currentDLI)
            ];
            $invoiceResponse = $this->handleGVLInvoiceAPI($newSave, $fieldList['orders_id'], $changedLineItems);
        }
        if ($this->checkForRevenueInvoice($originalDLI, $currentDLI)) {
            $revenueResponse = $this->sendRevenueInvoice($newSave, $fieldList['orders_id']);
        }
        if ($this->checkForRevenue($originalDLI, $currentDLI)) {
            $changedLineItems = [
                'removeReadyToDistribute' => $this->checkForDeletedRevenueLines($originalDLI, $currentDLI)
            ];
            $revenueResponse = $this->handleGVLRevenueAPI($newSave, $fieldList['orders_id'], $changedLineItems);
        }
    }
    public function save_module($module)
    {
        //Address List save
        $addressListModule= Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->saveAddressList($_REQUEST, $this->id);
        }

        $EscrowsModel=Vtiger_Module_Model::getInstance('Escrows');
        if($EscrowsModel && $EscrowsModel->isActive()) {
            $EscrowsModel->saveEscrowsForActuals($_REQUEST, $this->id);
        }
    }
    public function handleGVLAPI($newSave, $fieldList, $changedLineItems = [])
    {
        //need to call the invoice API here.
        $orderNumber = $fieldList['orders_id'];
        $invoiceResponse = $this->handleGVLInvoiceAPI($newSave, $orderNumber, $changedLineItems);
        $revenueResponse = $this->handleGVLRevenueAPI($newSave, $orderNumber, $changedLineItems);

        return [
            'invoiceResponse' => $invoiceResponse,
            'revenueResponse' => $revenueResponse,
        ];
    }

    private function handleGVLInvoiceAPI($newSave, $orderNumber, $changedLineItems = [])
    {
        $invoiceResponse = false;
        try {
            $inputArray = [
                'orderNumber' => $orderNumber,
                'changedLines' => $changedLineItems
            ];
            $invoiceAPI  = new MoveCrm\GraebelAPI\invoiceHandler($inputArray);
            if ($newSave) {
                $invoiceResponse = $invoiceAPI->createInvoice();
            } else {
                $invoiceResponse = $invoiceAPI->updateInvoice();
            }
            file_put_contents('logs/api_debug.log', "\n INVOICE API RESPONSE (Actuals.php:".__LINE__.") invoiceResponse : ".print_r($invoiceResponse, true), FILE_APPEND);
        } catch (Exception $ex) {
            //@TODO: consider a fail message.
            file_put_contents('logs/api_debug.log', "\n INVOICE API EXCEPTION (Actuals.php:".__LINE__.") ex->getMessage() : ".print_r($ex->getMessage(), true), FILE_APPEND);
        }
        return $invoiceResponse;
    }

    private function handleGVLRevenueAPI($newSave, $orderNumber, $changedLineItems = [], $invoiceFlag = false)
    {
        $revenueResponse = false;
        try {
            $inputArray = [
                'orderNumber' => $orderNumber,
                'changedLines' => $changedLineItems
            ];
            $revenueAPI  = new MoveCrm\GraebelAPI\revenueHandler($inputArray);
            if ($newSave) {
                $revenueResponse = $revenueAPI->createRevenue($invoiceFlag);
            } else {
                $revenueResponse = $revenueAPI->updateRevenue($invoiceFlag);
            }
            file_put_contents('logs/api_debug.log', "\n REVENUE API RESPONSE (Actuals.php:".__LINE__.") revenueResponse : ".print_r($revenueResponse, true), FILE_APPEND);
        } catch (Exception $ex) {
            //@TODO: consider a fail message.
            file_put_contents('logs/api_debug.log', "\n REVENUE API EXCEPTION (Actuals.php:".__LINE__.") ex->getMessage() : ".print_r($ex->getMessage(), true), FILE_APPEND);
        }
        return $revenueResponse;
    }
    private function sendRevenueInvoice($newSave, $orderNumber)
    {
        return $this->handleGVLRevenueAPI($newSave, $orderNumber, [], true);
    }

    private function checkForInvoice($originalDLI, $currentDLI)
    {
        if (!$this->checkDetailLineItem($originalDLI, $currentDLI)) {
            return false;
        }
        return !$this->ignoreDetailLineItemChange($originalDLI, $currentDLI, 'Invoiced');
        //return $this->compareDetailLineItems($originalDLI, $currentDLI, 'Invoiced');
    }

    private function checkForRevenueInvoice($originalDLI, $currentDLI)
    {
        if (!$this->checkDetailLineItem($originalDLI, $currentDLI)) {
            return false;
        }
        return $this->compareDetailLineItems($originalDLI, $currentDLI, 'InvoiceNumber');
    }

    private function checkForRevenue($originalDLI, $currentDLI)
    {
        if (!$this->checkDetailLineItem($originalDLI, $currentDLI)) {
            return false;
        }
        return !$this->ignoreDetailLineItemChange($originalDLI, $currentDLI, 'Distributed');
        //return $this->compareDetailLineItems($originalDLI, $currentDLI, 'Distributed');
    }

    //function to check if detail line items are valid
    //false if there is no current detail line item.
    //true if there is a current detail line item
    private function checkDetailLineItem($originalDLI, $currentDLI)
    {
        if (!$currentDLI) {
            //no current dli?  then don't go
            return false;
        }

        //not really needed.
        if (!$originalDLI) {
            //no original detailed items then do it!
            return true;
        }
        return true;
    }

    //function to ignore particular detail line item field changes.
    //returns true (IGNORE) if:
    //search field for any detail line item id in current AND original is different. then IGNORE this save
    private function ignoreDetailLineItemChange($originalDLI, $currentDLI, $fieldname)
    {
        if (!$fieldname) {
            //no search definition so just do it.
            return false;
        }
        foreach ($currentDLI as $DetailLineItemId => $individualItem) {
            if (!isset($originalDLI[$DetailLineItemId])) {
                //Original doesn't have this line item so GOOOO
                return false;
            }
            if ($originalDLI[$DetailLineItemId][$fieldname] != $individualItem[$fieldname]) {
                //Original and new don't match for this item so we can ignore this save.
                return true;
            }
        }
        return false;
    }

    //Function to check if an item is different from the original in the detail line items.
    //returns true if:
    //1) there is no search field
    //2) there is no original detail line item with that id
    //2) original and current search field FOR each ID do not match
    private function compareDetailLineItems($originalDLI, $currentDLI, $fieldname)
    {
        if (!$fieldname) {
            //no search definition so just do it.
            return true;
        }
        foreach ($currentDLI as $DetailLineItemId => $individualItem) {
            if (!isset($originalDLI[$DetailLineItemId])) {
                //Original doesn't have this line item so GOOOO
                return true;
            }
            if ($originalDLI[$DetailLineItemId][$fieldname] != $individualItem[$fieldname]) {
                //Original and new don't match for this item so go
                return true;
            }
        }
        return false;
    }

    private function checkForDeletedInvoiceLines($originalDLI, $currentDLI)
    {
        return $this->findAllChangedLines($originalDLI, $currentDLI, 'ReadyToInvoice');
    }

    private function checkForDeletedRevenueLines($originalDLI, $currentDLI)
    {
        return $this->findAllChangedLines($originalDLI, $currentDLI, 'ReadyToDistribute');
    }

    private function findAllChangedLines($originalDLI, $currentDLI, $searchField)
    {
        if (!$searchField) {
            return [];
        }
        $changedLineItems = [];
        foreach ($currentDLI as $DetailLineItemId => $individualItem) {
            if (!isset($originalDLI[$DetailLineItemId])) {
                //Original doesn't have this line item so GOOOO
                continue;
            }
            if ($originalDLI[$DetailLineItemId][$searchField] != $individualItem[$searchField]) {
                //Original and new don't match for this item so go
                $changedLineItems[] = $DetailLineItemId;
            }
        }
        return $changedLineItems;
    }

    private function getDetailLineItemArray($module)
    {
        if (strtolower(getenv('INSTANCE_NAME')) != 'graebel') {
            //only for graebel
            return;
        }
        if (!getenv('GVL_API_ON')) {
            //only if API is on.
            return;
        }
        $fieldList = array_merge($_REQUEST, $this->column_fields);
        if ($fieldList['pseudoSave'] == '1') {
            //NOT FOR pseudoSave
            return;
        }
        //attempt to find the record id of this current record.
        if (empty($fieldList['record'])) {
            if (!empty($fieldList['currentid'])) {
                $fieldList['record'] = $fieldList['currentid'];
            } else {
                $fieldList['record'] = $this->id;
            }
        }

        //@TODO JG HERE -- It might be best to see if this id is set and use it instead of record from _REQUEST
        //if ($this->id) {
        //}

        if (!$fieldList['record']) {
            //no record then just go home.
            return;
        }
        $originalRecordModel = Vtiger_Record_Model::getInstanceById($fieldList['record'], $module);
        if (!$originalRecordModel) {
            //no record model? give up.
            return;
        }
        if (!method_exists($originalRecordModel, 'getDetailLineItems')) {
            //check for the method we don't want to fail.
            return;
        }

        //hopefully it returns the right stuff!
        $sectionFormatDLI = $originalRecordModel->getDetailLineItems();
        $outputArray = [];
        foreach ($sectionFormatDLI as $sectionName => $sectionItems) {
            foreach ($sectionItems as $index => $individualItem) {
                $outputArray[$individualItem['DetailLineItemId']] = $individualItem;
            }
        }
        return $outputArray;
    }
}
