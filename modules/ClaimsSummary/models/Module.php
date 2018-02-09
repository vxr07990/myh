<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClaimsSummary_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function to check whether the module is summary view supported
     * @return <Boolean> - true/false
     */
    public function isSummaryViewSupported()
    {
        return false;
    }
}
