<?php

/* +***********************************************************************************
 * The Original Code is:  VGS Global
 * The Initial Developer of the Original Code is VGS Global.
 * *********************************************************************************** */

class Potentials_GetHiddenBlocks_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray, $adb;

        $forModule = $request->get('formodule');
        $viewMode = $request->get('viewMode');
        $businessLines = explode('::', $request->get('businessline'));
        if ($viewMode == 'detail') {
            foreach ($businessLines as $index=>$line) {
                $businessLines[$index] = vtranslate($line);
            }
        }
        $info['show']=array();
        $blocksToHide=array();
        
        $showInterstate = false;

        foreach ($businessLines as $businessLine) {
            if (array_key_exists($businessLine, $hiddenBlocksArray[$forModule])) {
                $info['show'] = array_merge($info['show'], explode('::', $hiddenBlocksArray[$forModule][$businessLine]));

                if ($businessLine == "Intrastate Move") {
                    //                    $businessLine = "Interstate Move";
//                    $info['show'] = array_merge($info['show'], explode('::', $hiddenBlocksArray[$forModule][$businessLine]));
//                    $showInterstate = true;
                }
            }
        }
        
        foreach ($hiddenBlocksArray[$forModule] as $businessLine => $blocks) {
            if (!in_array($businessLine, $businessLines) && (!$showInterstate)) {
                $blocksToHide = array_merge($blocksToHide, explode('::', $hiddenBlocksArray[$forModule][$businessLine]));
            }
        }
        
        $info['hide']=$blocksToHide;

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
