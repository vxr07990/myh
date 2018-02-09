<?php
require_once('libraries/opentok.phar');
use OpenTok\OpenTok;
use OpenTok\MediaMode;

class Cubesheets_VideoFeed_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $viewer      = $this->getViewer($request);
        $recordModel = Cubesheets_Record_Model::getInstanceById($request->get('record'));
        $viewer->assign('TOKBOX_SESSIONID', $recordModel->getTokboxSession());
        $viewer->assign('TOKBOX_SERVERTOKEN', $recordModel->getTokboxServerToken());
        $viewer->view('VideoFeed.tpl', $moduleName);
    }
}
