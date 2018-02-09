<?php
class WFLocations_DetailView_Model extends Vtiger_DetailView_Model
{

    public function getDetailViewLinks($linkParams)
    {
        $linkTypes = array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWCUSTOM');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();

        $isBase = $recordModel->isBaseLocation();

        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();
        $defaultRecord = $recordModel->get('is_default');
        $detailViewLink = array();

        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId) && !$defaultRecord) {
            $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_EDIT',
                    'linkurl' => $recordModel->getEditViewUrl(),
                    'linkicon' => ''
            );

            foreach ($detailViewLinks as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }

        $linkModelListDetails = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
        //Mark all detail view basic links as detail view links.
        //Since ui will be look ugly if you need many basic links
        $detailViewBasiclinks = $linkModelListDetails['DETAILVIEWBASIC'];
        //adding new DETAILVIEWCUSTOM link type for custom buttons that aren't in the dropdown menu
        //$linkModelList['DETAILVIEWCUSTOM'] = $linkModelListDetails['DETAILVIEWCUSTOM'];
        unset($linkModelListDetails['DETAILVIEWBASIC']);
        if (Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId) && !(getenv('INSTANCE_NAME') == 'sirva' && ($moduleName == 'Opportunities' || $moduleName == 'Leads' || $moduleName == 'Estimates' || $moduleName == 'Cubesheets'))) {
            $deleteLinkUrl='javascript:Vtiger_Detail_Js.deleteRecord("'.$recordModel->getDeleteUrl().'")';
            if($moduleModel->isCheckBeforeEditDeleteRequired()) {
                $deleteLinkUrl='javascript:Vtiger_Detail_Js.checkAndDeleteRecord("'.$recordModel->getDeleteUrl().'")';
            }
            $deletelinkModel = array(
                    'linktype' => 'DETAILVIEW',
                    'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_'. $moduleName, $moduleName)),
                    'linkurl' => $deleteLinkUrl,
                    'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($deletelinkModel);
        }

        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId) && $moduleName != 'Media') {
            $duplicateLinkModel = array(
                        'linktype' => 'DETAILVIEWBASIC',
                        'linklabel' => 'LBL_DUPLICATE',
                        'linkurl' => $recordModel->getDuplicateRecordUrl(),
                        'linkicon' => ''
                );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($duplicateLinkModel);
        }

        if (!empty($detailViewBasiclinks)) {
            foreach ($detailViewBasiclinks as $linkModel) {
                // Remove view history, needed in vtiger5 to see history but not in vtiger6
                if ($linkModel->linklabel == 'View History') {
                    continue;
                }
                $linkModelList['DETAILVIEW'][] = $linkModel;
            }
        }

        $relatedLinks = $this->getDetailViewRelatedLinks();

        foreach ($relatedLinks as $relatedLinkEntry) {
            $relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
            $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }

        if(!$isBase) {
          $moveLocationLink = [
            'linktype' => 'DETAILVIEWBASIC',
            'linklabel' => 'LBL_WFLOCATIONS_MOVE_LOCATION_SINGULAR',
            'linkurl' => 'javascript:triggerMoveLocationDetail()',
            'linkicon' => ''
          ];

          $relatedLink = Vtiger_Link_Model::getInstanceFromValues($moveLocationLink);
          $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }


        $widgets = $this->getWidgets();
        foreach ($widgets as $widgetLinkModel) {
            $linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if ($currentUserModel->isAdminUser()) {
            $settingsLinks = $moduleModel->getSettingLinks();
            foreach ($settingsLinks as $settingsLink) {
                $linkModelList['DETAILVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
            }
        }

        return $linkModelList;
    }

}
