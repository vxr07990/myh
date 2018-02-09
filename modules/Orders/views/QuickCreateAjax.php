<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/18/2016
 * Time: 11:15 AM
 */

class Orders_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View
{
    public function process(Vtiger_Request $request)
    {
        // if this is a relation operation, pull the billing_type field
        $isRelationOperation = $request->get('relationOperation');
        $sourceModule = $request->get('sourceModule');
        $sourceRecord = $request->get('sourceRecord');
        if ($isRelationOperation && $sourceModule && $sourceRecord) {
            $src = Vtiger_Record_Model::getInstanceById($sourceRecord);
            if ($src) {
                $owner = $src->get('billing_type');
                $request->set('billing_type', $owner);
            }
        }
        parent::process($request);
    }
}
