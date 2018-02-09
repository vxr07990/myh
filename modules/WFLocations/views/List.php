<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 9/8/2017
 * Time: 10:14 AM
 */
class WFLocations_List_View extends Vtiger_List_View
{

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function process(Vtiger_Request $request)
    {
        if(isset($_SESSION['responseString'])){
            $viewer         = $this->getViewer($request);
            $viewer->assign('MULTISAVE', $_SESSION['responseString']);
            unset($_SESSION['responseString']);
        }
        parent::process($request);
    }
}
