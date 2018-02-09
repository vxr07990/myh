<?php
class Tariffs_DetailView_Model extends Vtiger_DetailView_Model{
    public function getDetailViewLinks($linkParams)
    {
        $linkModelList = parent::getDetailViewLinks($linkParams);
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();
        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();
        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DUPLICATETARIFF',
                'linklabel' => 'LBL_DUPLICATETAIFF',
                'linkurl' => $recordModel->getDuplicateRecordUrl(),
                'linkicon' => ''
            );

            foreach ($detailViewLinks as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }

        
        return $linkModelList;
    }
}