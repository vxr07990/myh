<?php

/* +***********************************************************************************
 * The Original Code is:  VGS Global
 * The Initial Developer of the Original Code is VGS Global.
 * *********************************************************************************** */

class Opportunities_GetHiddenBlocks_Action extends Vtiger_BasicAjax_Action
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
        
        foreach ($businessLines as $businessLine) {
            if (array_key_exists($businessLine, $hiddenBlocksArray[$forModule])) {
                $info['show'] = array_merge($info['show'], explode('::', $hiddenBlocksArray[$forModule][$businessLine]));
            }
        }
        
        foreach ($hiddenBlocksArray[$forModule] as $businessLine => $blocks) {
            if (!in_array($businessLine, $businessLines)) {
                $blocksToHideArray = explode('::', $hiddenBlocksArray[$forModule][$businessLine]);
                foreach ($blocksToHideArray as $block) {
                    file_put_contents('logs/HiddenBlocks.log', date('Y-m-d H:i:s - ').$block."\n", FILE_APPEND);
                        //check in blocksToHide to not duplicate
                        if (!in_array($block, $info['show']) && !in_array($block, $blocksToHide)) {
                            if (!is_array($blocksToHide)) {
                                $blocksToHide = array();
                            }
                            if (getenv('INSTANCE_NAME') != 'sirva') {
                                array_push($blocksToHide, $block);
                            }
                            if (getenv('INSTANCE_NAME') == 'sirva' && $block != 'LBL_LEADS_NATIONALACCOUNT' && $block != 'LBL_POTENTIALS_NATIONALACCOUNT') {
                                array_push($blocksToHide, $block);
                            }
                        }
                }
                    //$blocksToHide = array_merge($blocksToHide, explode('::', $hiddenBlocksArray[$forModule][$businessLine]));
            }
        }
        $info['hide']=$blocksToHide;

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
