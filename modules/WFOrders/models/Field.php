<?php
/**
 * WFOrders Field Model Class
 */
class WFOrders_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to identify number fields which can be batch added to or subtracted from using a popup
     * @return <Boolean> - True/False
     */
    public function isBatchAddSubtract()
    {
        if ($this->get('name') == 'wforder_weight') {
            return true;
        }
        return parent::isBatchAddSubtract();
    }
}
