<?php

use MoveCrm\GraebelAPI\APIHandler;

require_once('libraries/nusoap/nusoap.php');
class Actuals_Record_Model extends Estimates_Record_Model
{
    const INVOICE_MODE = 1;

    public function pullApiServices($mode = self::INVOICE_MODE, $itemCodes = [])
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_detailed_lineitems` WHERE dli_relcrmid=? AND dli_tariff_item_number=?";
        $services = [];

        switch ($mode) {
            case self::INVOICE_MODE:
                $sequence = 1;

                foreach ($itemCodes as $itemCode) {
                    $result = $db->pquery($sql, [$this->getId(), $itemCode]);
                    $row = $result->fetchRow();
                    if (!$row) {
                        continue;
                    }

                    $services[] = [
                        'ServiceID'              => $row['dli_tariff_item_number'],
                        'ServiceDescription'     => $row['dli_description'],
                        'ServiceFlag'            => ($row['dli_invoiceable'] && $row['dli_ready_to_invoice']) ? APIHandler::CHAR_TRUE : APIHandler::CHAR_FALSE,
                        'BaseRate'               => $row['dli_base_rate'] ?: $row['dli_unit_rate'],
                        'Quantity'               => $row['dli_quantity'],
                        'Rate'                   => $row['dli_invoice_net'],
                        'Gross'                  => $row['dli_gross'],
                        'Discount'               => $row['dli_invoice_discount'],
                        'TransactionType'        => $row['dli_invoiced'] ? APIHandler::getStatic('TRANSACTION_TYPE')['update'] : APIHandler::getStatic('TRANSACTION_TYPE')['insert'],
                        'InvoiceFlag'            => ($row['dli_invoiceable'] && $row['dli_ready_to_invoice']) ? APIHandler::CHAR_TRUE : APIHandler::CHAR_FALSE,
                        'Sequence'               => $sequence++,
                        'UnitCode'               => $row['dli_unit_of_measurement'],
                    ];
                }
                break;
            default: break;
        }
        return $services;
    }
}
