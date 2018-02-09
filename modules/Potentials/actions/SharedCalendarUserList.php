<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Potentials_SharedCalendarUserList_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getViewTypes');
        $this->exposeMethod('getSharedUsersList');
    }

    /**
     * Function to get Shared Users
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $moduleName = $request->getModule();
        $sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
        //$sharedUsersInfo = Calendar_Module_Model::getSharedUsersInfoOfCurrentUser($currentUser->id);

        $HTMLResponse = '<label class="checkbox addedCalendars"> <input type="checkbox"  data-calendar-sourcekey="Events33_' . $currentUser->id . '" data-calendar-feed="Events" data-calendar-userid="' . $currentUser->id . '"  checked="checked"> <span class="label" style="text-shadow: none;">Mine</span> </label>';
        
        
        foreach ($sharedUsers as $sharedUserId => $sharedUserName) {
            $HTMLResponse .= '<label class="checkbox addedCalendars">  <input type="checkbox"  data-calendar-sourcekey="Events33_' . $sharedUserId . '" data-calendar-feed="Events" data-calendar-userid="' . $sharedUserId . '" checked="checked"> <span class="label" style="text-shadow: none;">' . $sharedUserName .'</span> </label>';
        }

        $response = new Vtiger_Response();
        $response->setResult($HTMLResponse);
        $response->emit();
    }
}
