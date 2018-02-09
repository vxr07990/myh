<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_ShowWidget_View extends Vtiger_IndexAjax_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        $currentUser   = Users_Record_Model::getCurrentUserModel();
        $moduleName    = $request->getModule();
        $componentName = $request->get('name');
        $linkId        = $request->get('linkid');
        if (!empty($componentName)) {
            $className = Vtiger_Loader::getComponentClassName('Dashboard', $componentName, $moduleName);
            if (!empty($className)) {
                $widget = null;
                if (!empty($linkId)) {
                    $widget = new Vtiger_Widget_Model();
                    $widget->set('linkid', $linkId);
                    $widget->set('userid', $currentUser->getId());
                    $widget->set('filterid', $request->get('filterid', null));
                    if ($request->has('data')) {
                        $widget->set('data', $request->get('data'));
                    }
                    $widget->add();
                }
                $classInstance = new $className();
                $classInstance->process($request, $widget);

                return;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult(['success' => false, 'message' => vtranslate('NO_DATA')]);
        $response->emit();
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
