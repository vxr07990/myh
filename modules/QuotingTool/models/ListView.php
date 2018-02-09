<?php

class QuotingTool_ListView_Model extends Vtiger_ListView_Model
{
    /**
      * Function to get the list of Mass actions for the module
      * @param <Array> $linkParams
      * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
      */
    public function getListViewMassActions($linkParams)
    {
        $massActionLinks = parent::getListViewMassActions($linkParams);
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleModel = $this->getModule();
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);

        if($request->get('view') == 'List'){
            if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
                $massActionLink = array(
                    'linktype' => 'LISTVIEWMASSACTION',
                    'linklabel' => 'LBL_DUPLICATE',
                    'linkurl' => 'javascript:triggerDuplicate()',
                    'linkicon' => ''
                );
                foreach ($massActionLinks['LISTVIEWMASSACTION'] as $key => $value) {
                    if($value->get('linklabel') == 'LBL_DUPLICATE'){
                        $massActionLinks['LISTVIEWMASSACTION'][$key] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
                        break;
                    }
                }
                
            }
        }
        return $massActionLinks;
    }
}