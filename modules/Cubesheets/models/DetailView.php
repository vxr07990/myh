<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Cubesheets_DetailView_Model extends Vtiger_DetailView_Model
{

    /**
     * Function to get the Quick Links for the Detail view of the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $tokboxPermitted = $currentUser->get('tokbox_permitted');
        file_put_contents('logs/tokboxPermitted.log', date('Y-m-d H:i:s - ').$currentUser->getId().': '.$tokboxPermitted."\n", FILE_APPEND);

        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $moduleLinks = $this->getModule()->getSideBarLinks($linkTypes);

        $listLinkTypes = array('DETAILVIEWSIDEBARLINK', 'DETAILVIEWSIDEBARWIDGET');
        $listLinks = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $listLinkTypes);

        if ($listLinks['DETAILVIEWSIDEBARLINK']) {
            foreach ($listLinks['DETAILVIEWSIDEBARLINK'] as $link) {
                $link->linkurl = $link->linkurl.'&record='.$this->getRecord()->getId().'&source_module='.$this->getModule()->getName();
                $moduleLinks['SIDEBARLINK'][] = $link;
            }
        }

        if ($currentUser->getTagCloudStatus()) {
            $tagWidget = array(
                'linktype' => 'DETAILVIEWSIDEBARWIDGET',
                'linklabel' => 'LBL_TAG_CLOUD',
                'linkurl' => 'module='.$this->getModule()->getName().'&view=ShowTagCloud&mode=showTags',
                'linkicon' => '',
            );
            $linkModel = Vtiger_Link_Model::getInstanceFromValues($tagWidget);
            if ($listLinks['DETAILVIEWSIDEBARWIDGET']) {
                array_unshift($listLinks['DETAILVIEWSIDEBARWIDGET'], $linkModel);
            } else {
                $listLinks['DETAILVIEWSIDEBARWIDGET'][] = $linkModel;
            }
        }

        if ($listLinks['DETAILVIEWSIDEBARWIDGET']) {
            foreach ($listLinks['DETAILVIEWSIDEBARWIDGET'] as $link) {
                if (
                    $link->linklabel == 'LBL_CUBESHEETS_TOKBOX' &&
                    (
                        $tokboxPermitted != 'on' ||
                        $this->getRecord()->get('survey_type') != 'LiveSurvey'
                    )
                ) {
                    continue;
                }
                $link->linkurl = $link->linkurl.'&record='.$this->getRecord()->getId().'&source_module='.$this->getModule()->getName();
                $moduleLinks['SIDEBARWIDGET'][] = $link;
            }
        }

        return $moduleLinks;
    }
}
