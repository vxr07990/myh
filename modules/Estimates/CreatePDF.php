<?php
/*********************************************************************************
 * @author 			Louis Robinson
 * @file 			EstimatePDFCpmtrpller.php
 * @function 		Estimates Module for moveCRM
 * @company 		IGC SOftware
 * @description 	QuotePDFController.php for the Estimates class
 * @contact 		lrobinson@igcsoftware.com
 *
 *********************************************************************************/
include_once 'modules/Estimates/EstimatePDFController.php';
$controller = new Vtiger_EstimatePDFController($currentModule);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$quote_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
if (isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
    $quote_id = vtlib_purify($_REQUEST['record']);
    $filepath='test/product/'.$quote_id.'_Estimates_'.$quote_no.'.pdf';
    //added file name to make it work in IE, also forces the download giving the user the option to save
    $controller->Output($filepath, 'F');
} else {
    //added file name to make it work in IE, also forces the download giving the user the option to save
    $controller->Output('Estimates_'.$quote_no.'.pdf', 'D');
    exit();
}
