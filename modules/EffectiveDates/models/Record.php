<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class EffectiveDates_Record_Model extends Vtiger_Record_Model
{
    /**
     * Fuction to get the Name of the record
     * @return <String> - Entity Name of the record
     */
    public function getName()
    {
        $displayName = $this->getDisplayName();

        $displayName = DateTimeField::convertToUserFormat($displayName);

        return Vtiger_Util_Helper::toSafeHTML(decode_html($displayName));
    }
}
