<?php

class TariffServices_Module_Model extends Vtiger_Module_Model
{
    public function isSummaryViewSupported()
    {
        return false;
    }
    
    public function getDetailViewName()
    {
        return 'Edit';
    }
}
