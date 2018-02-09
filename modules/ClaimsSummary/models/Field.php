<?php

class ClaimsSummary_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed()
    {
        if($this->name == 'item_status'){
            return false;
        }
        return true;
    }
}