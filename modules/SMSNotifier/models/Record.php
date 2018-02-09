<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/modules/SMSNotifier/SMSNotifier.php');

class SMSNotifier_Record_Model extends Vtiger_Record_Model
{
    public static function SendSMS($message, $toNumbers, $currentUserId, $recordIds, $moduleName)
    {
        $SmsNotifier = new SMSNotifier();
        $SmsNotifier->sendsms($message, $toNumbers, $currentUserId, $recordIds, $moduleName);
    }

    public function checkStatus()
    {
        $SmsNotifier = new SMSNotifier();
        $statusDetails = $SmsNotifier->getSMSStatusInfo($this->get('id'));

        //this is the original vtiger code and I am not sure this works
        //$statusColor = $this->getColorForStatus($statusDetails[0]['status']);

        //I added statuscolor to the details since that is saved and passed about.  so now it displays the bgcolor.
        $statusDetails[0]['statuscolor'] = $this->getColorForStatus($statusDetails[0]['status']);

        $this->setData($statusDetails[0]);

        return $this;
    }

    public function getCheckStatusUrl()
    {
        return "index.php?module=".$this->getModuleName()."&view=CheckStatus&record=".$this->getId();
    }

    public function getColorForStatus($smsStatus)
    {
        if ($smsStatus == 'Processing') {
            $statusColor = '#FFFCDF';
        } elseif ($smsStatus == 'Dispatched') {
            $statusColor = '#E8FFCF';
        } elseif ($smsStatus == 'Failed') {
            $statusColor = '#FFE2AF';
        } else {
            $statusColor = '#FFFFFF';
        }
        return $statusColor;
    }
}
