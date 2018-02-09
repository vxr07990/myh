<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/16/2016
 * Time: 9:36 AM
 */
class Estimates_ReloadContents_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {

        if($request->isEditView())
        {
            $controller = new Estimates_Edit_View();
        }
        else
        {
            $controller = new Estimates_Detail_View();
        }
        $request->set('mode','reloadContents');
        $controller->process($request);
    }
}
