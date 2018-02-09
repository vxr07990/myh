<?php
/**
 * @author 			LouReport.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */
class Estimates_GetReport_Action extends Estimates_GetReportBase_Action
{
    public function process(Vtiger_Request $request)
    {
        $request->set('mode','interstate');
        parent::process($request, 'interstate');
    }
}
