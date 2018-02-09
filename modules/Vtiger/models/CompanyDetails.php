<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * CompanyDetails Record Model class
 */
class Vtiger_CompanyDetails_Model extends Vtiger_Base_Model
{

    /**
     * Function to get the Company Logo
     * @return Vtiger_Image_Model instance
     */
    public function getLogo()
    {
        $logoName  = decode_html($this->get('logoname'));
        $logo  = decode_html($this->get('logo'));

        $logoModel = new Vtiger_Image_Model();
        if (!empty($logoName)) {
            $companyLogo              = [];
            $companyLogo['imagepath'] = "test/logo/$logoName";
            //$companyLogo['imagepath'] = $logo;
            $companyLogo['alt']       = $companyLogo['title'] = $companyLogo['imagename'] = $logoName;
            $logoModel->setData($companyLogo);
        }

        //Turns out Sirva does not want this functionality.
        if (getenv('INSTANCE_NAME') != 'sirva') {
            //adding in so the user is given the image for the first Agency.
            $current_user = Users_Record_Model::getCurrentUserModel();
            $role = $current_user->getUserRoleDepth();
            if ($role >= 4) {
                $allAgencies = $current_user->getAccessibleAgentsForUser();
                if (count($allAgencies) > 0) {
                    $firstAgentId = array_keys($allAgencies)[0];
                    //ensure we have an AgentManager model
                    $agentModel   = Vtiger_Record_Model::getInstanceById($firstAgentId, 'AgentManager');

                    if ($agentModel) {
                        //so this returns an array of images... this is so, because I actually did it that way. 8|
                        //not fixing because it appears tpl expects an array of them.
                        $image      = $agentModel->getImageDetails()[0];
                        $agencyLogo = [];
                        //OH my GOD the path is part of the filename actually... why ... just why.
                        $agencyLogo['imagepath'] = $image['path'].'_'.$image['name'];
                        $agencyLogo['alt']       = $agencyLogo['title'] = $agencyLogo['imagename'] = $image['name'];
                        //ensure a name field exists and is filled, and that the built path finds a file.
                        if (
                            array_key_exists('imagename', $agencyLogo) &&
                            $agencyLogo['imagename'] &&
                            array_key_exists('imagepath', $agencyLogo) &&
                            $agencyLogo['imagepath'] &&
                            (
                                //if there's no document_root in SERVER (and the name/path are set) PASS!
                                !array_key_exists('DOCUMENT_ROOT', $_SERVER) ||
                                //otherwise verify the image file exists.
                                file_exists($_SERVER{'DOCUMENT_ROOT'}.'/'.$agencyLogo['imagepath'])
                            )
                        ) {
                            //I TRULY believe this will overwrite the previously set data.
                            $logoModel->setData($agencyLogo);
                        }
                    }
                }
            }
        }

        return $logoModel;
    }

    /**
     * Function to get the instance of the CompanyDetails model for a given organization id
     * @param <Number> $id
     * @return Vtiger_CompanyDetails_Model instance
     */
    public static function getInstanceById($id = 1)
    {
        $companyDetails = Vtiger_Cache::get('vtiger', 'organization');
        if (!$companyDetails) {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT * FROM vtiger_organizationdetails WHERE organization_id=?';
            $params = array($id);
            $result = $db->pquery($sql, $params);
            $companyDetails = new self();
            if ($result && $db->num_rows($result) > 0) {
                $resultRow = $db->query_result_rowdata($result, 0);
                $companyDetails->setData($resultRow);
            }
            Vtiger_Cache::set('vtiger', 'organization', $companyDetails);
        }
        return $companyDetails;
    }
}
