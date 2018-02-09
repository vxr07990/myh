<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class Employees_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);
        $parentQuickLinks = parent::getSideBarLinks($linkParams);


        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_CONTRACTORS',
                'linkurl' => 'index.php?module=Employees&view=Contractors',
                'linkicon' => '',

            )
        );

        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_RESOURCE_DASHBOARD',
                'linkurl' => $this->getResourcesListUrl(),
                'linkicon' => '',
            )
        );


        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        //return $links;

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if ($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }



        return $parentQuickLinks;
    }

    public function isSummaryViewSupported()
    {
        return false;
    }

    public function getResourcesListUrl()
    {
        //this broke things because we aren't using resource dashboard
        //$employeeModel = Vtiger_Module_Model::getInstance('ResourceDashboard');
        // return $employeeModel->getListViewUrl();
    }
    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }

    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        if (!empty($searchValue)&& !empty($parentId) && $parentModule == 'EmployeeRoles') {
            $matchingRecords = Employees_Record_Model::getSearchResult($searchValue, $this->getName(), $parentId);
        } else {
            if (!empty($searchValue)&& empty($parentId) && empty($parentModule)) {
                $matchingRecords = Vtiger_Record_Model::getSearchResult($searchValue, $this->getName());
            } elseif ($parentId && $parentModule) {
                $db = PearDatabase::getInstance();
                $result = $db->pquery($this->getSearchRecordsQuery($searchValue, $parentId, $parentModule), array());
                $noOfRows = $db->num_rows($result);

                $moduleModels = array();
                $matchingRecords = array();
                for ($i=0; $i<$noOfRows; ++$i) {
                    $row = $db->query_result_rowdata($result, $i);
                    if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                        $row['id'] = $row['crmid'];
                        $moduleName = $row['setype'];
                        if (!array_key_exists($moduleName, $moduleModels)) {
                            $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                        }
                        $moduleModel = $moduleModels[$moduleName];
                        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                        $recordInstance = new $modelClassName();
                        $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                    }
                }
            }
        }

        return $matchingRecords;
    }

}
