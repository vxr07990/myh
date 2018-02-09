<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class Opportunities_Record_Model extends Potentials_Record_Model
{
    public function getCreateInvoiceUrl()
    {
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');
        return 'index.php?module='.$invoiceModuleModel->getName().'&view='.$invoiceModuleModel->getEditViewName().'&account_id='.$this->get('related_to').'&contact_id='.$this->get('contact_id');
    }

    public function getSTSRegistrationUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&view=RegisterSTS&record='.$this->getId();
    }

    public function getRegisterSTSFields()
    {
        $STSFieldModels = array();
        $STSFieldModels['LBL_OPPORTUNITY_REGISTRATIONINFO'] = array();
        $STSFieldModels['LBL_OPPORTUNITY_BILLINGINFO'] = array();
        $STSFieldModels['LBL_OPPORTUNITY_SERVICEPROVIDERS'] = array();
        $STSFieldModels['LBL_OPPORTUNITY_STSINFORMATION'] = array();

        //Added this to better see where it's doing the getParticipantInfo
        $OAType = [
            'ba' => 'Booking Agent',
            'da' => 'Destination Agent',
            'ha' => 'Hauling Agent',
            'ea' => 'Estimating Agent',
            'oa' => 'Origin Agent',
        ];

        $registrationInfoBlock = array(
            'payment_type_sts' => 0,
            'payment_method' => 1,
            'agmt_id' => 2,
            'subagmt_nbr' => 3,
            'agrmt_cod' => 2,
            'subagrmt_cod' => 3,
            'national_account_number' => 4,
            'self_haul' => 5,
            'express_shipment' => 6,
            'cbs_ind' => 7,
            'move_type' => 8,
            'grr_estimate' => 9,
        );

        $billingInfoBlock = array(
            'booker_split' => 0,
            'origin_split' => 1,
            'billing_apn' => 2,
            'credit_check' => 3,
            'credit_check_amount' => 4,
            'ref_number' => 5,
            'ref_type' => 6,
            'contact_name' => 7,
        );

        $serviceProvidersBlock = array(
            'ba_code' => 0,
            'oa_code' => 4,
            'ea_code' => 8,
            'ha_code' => 12,
            'da_code' => 16,
            'ba_city' => 1,
            'oa_city' => 5,
            'ea_city' => 9,
            'ha_city' => 13,
            'da_city' => 17,
            'ba_state' => 2,
            'oa_state' => 6,
            'ea_state' => 10,
            'ha_state' => 14,
            'da_state' => 18,
            'ba_name' => 3,
            'oa_name' => 7,
            'ea_name' => 11,
            'ha_name' => 15,
            'da_name' => 19,
        );

        $STSInformation = array(
            'brand' => 0,
            'registration_date' => 1,
            'order_number' => 2,
            'sts_response' => 3,
        );

        $registrationFields = array(
            'move_type',
            'payment_type_sts',
            'payment_type',
            'payment_method',
            'brand',
            'self_haul',
            'express_shipment',
            'ba_code',
            'oa_code',
            'ea_code',
            'ha_code',
            'da_code',
            'ba_city',
            'oa_city',
            'ea_city',
            'ha_city',
            'da_city',
            'ba_state',
            'oa_state',
            'ea_state',
            'ha_state',
            'da_state',
            'ba_name',
            'oa_name',
            'ea_name',
            'ha_name',
            'da_name',
            'registration_date',
            'sts_response',
            'booker_split',
            'origin_split',
        );

        $nationalAccountFields = array(
            'national_account_number',
            'billing_apn',
            'cbs_ind',
            'ref_number',
            'ref_type',
            'credit_check',
            'credit_check_amount',
            'contact_name',
            'agmt_id',
            'subagmt_nbr',
        );

        $consumerFields = array(
            'subagrmt_cod',
            'agrmt_cod',
        );

        $tariffType = $this->getTariffType();

        $agmtId = '';
        $subAgmtNbr = '';
        $agrmtCOD = '';
        $subAgrmtCOD = '';
        $expressChecked = 0;
        $paymentMethodType = 'V~O';
        if ($this->isNationalAccount()) {
            $registrationFields = array_merge($registrationFields, $nationalAccountFields);
        } else {
            //file_put_contents('logs/devLog.log', "\n it's consumer \n tariff type: $tariffType", FILE_APPEND);
            $paymentMethodType = 'V~M';
            $registrationFields = array_merge($registrationFields, $consumerFields);
            //default agrmtid and sub_agrmt_nbr for consumer
            switch ($tariffType) {
                case 'TPG':
                    $agrmtCOD = 'TPG';
                    $subAgrmtCOD = '001';
                    break;
                case 'TPG GRR':
                    $agrmtCOD = 'GRR';
                    $subAgrmtCOD = '001';
                    break;
                case 'Pricelock':
                    $agrmtCOD = 'CGP';
                    $subAgrmtCOD = '001';
                    break;
                case 'Pricelock GRR':
                    $agrmtCOD = 'GRR';
                    $subAgrmtCOD = '001';
                    break;
                case 'Blue Express':
                    $agrmtCOD = 'CGP';
                    $expressTrucking = $this->getTrucking();
                    if ($expressTrucking == 1 || $expressTrucking == '1' || $expressTrucking == 'yes' || $expressTrucking == 'on') {
                        $subAgrmtCOD = '007';
                        $expressChecked = false;
                    } else {
                        $subAgrmtCOD = '002';
                        $expressChecked = true;
                    }
                    break;
                case 'UAS':
                    $agrmtCOD = 'UAS';
                    $subAgrmtCOD = '001';
                    break;
                case 'Allied Express':
                    $agrmtCOD = 'TPG';
                    $subAgrmtCOD = '005';
                    $expressChecked = true;
                    break;
                default:
                    //error
            }
        }

        $moduleName = 'Opportunities';

        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            return;
        }
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if ($moduleModel->isActive()) {
            $fieldModels = $moduleModel->getFields();
        }

        //$vanline = $this->getSirvaVanline();

        //file_put_contents('logs/devLog.log', "\n vanline: ".$vanline, FILE_APPEND)

        //file_put_contents('logs/devLog.log', "\n TARIFF TYPE: ".$tariffType, FILE_APPEND);

        $registrationFieldInfo = array(
            'move_type' => array('LBL_OPPORTUNITY_AUTHORITYTYPE', 'V~M', $this->get('move_type')),
            'payment_type_sts' => array('', 'V~M', $this->get('billing_type')),
            'payment_method' => array('', $paymentMethodType),
            'subagmt_nbr' => array('', 'V~M', $subAgmtNbr),
            'subagrmt_cod' => array('', 'V~M', $subAgrmtCOD),
            'national_account_number' => array('', 'V~M'),
            'agmt_id' => array('', 'V~M', $agmtId),
            'agrmt_cod' => array('', 'V~M', $agrmtCOD),
            'oa_code' => array('', 'V~M', $this->getParticipantInfo($OAType['oa'], 'agency_code')),
            'ba_code' => array('', 'V~M', $this->getParticipantInfo($OAType['ba'], 'agency_code')),
            'ea_code' => array('', 'V~M', $this->getParticipantInfo($OAType['ea'], 'agency_code')),
            'da_code' => array('', 'V~M', $this->getParticipantInfo($OAType['da'], 'agency_code')),
            'ha_code' => array('', 'V~O', $this->getParticipantInfo($OAType['ha'], 'agency_code')),

            'oa_name' => array('', 'V~O', $this->getParticipantInfo($OAType['oa'], 'agency_name')),
            'ba_name' => array('', 'V~O', $this->getParticipantInfo($OAType['ba'], 'agency_name')),
            'ea_name' => array('', 'V~O', $this->getParticipantInfo($OAType['ea'], 'agency_name')),
            'da_name' => array('', 'V~O', $this->getParticipantInfo($OAType['da'], 'agency_name')),
            'ha_name' => array('', 'V~O', $this->getParticipantInfo($OAType['ha'], 'agency_name')),

            'oa_city' => array('', 'V~O', $this->getParticipantInfo($OAType['oa'], 'city')),
            'ba_city' => array('', 'V~O', $this->getParticipantInfo($OAType['ba'], 'city')),
            'ea_city' => array('', 'V~O', $this->getParticipantInfo($OAType['ea'], 'city')),
            'da_city' => array('', 'V~O', $this->getParticipantInfo($OAType['da'], 'city')),
            'ha_city' => array('', 'V~O', $this->getParticipantInfo($OAType['ha'], 'city')),

            'oa_state' => array('', 'V~O', $this->getParticipantInfo($OAType['oa'], 'state')),
            'ba_state' => array('', 'V~O', $this->getParticipantInfo($OAType['ba'], 'state')),
            'ea_state' => array('', 'V~O', $this->getParticipantInfo($OAType['ea'], 'state')),
            'da_state' => array('', 'V~O', $this->getParticipantInfo($OAType['da'], 'state')),
            'ha_state' => array('', 'V~O', $this->getParticipantInfo($OAType['ha'], 'state')),
            'brand' => array('', 'V~M', $this->getBrand()),
            'grr_estimate' => array('', 'V~M', $this->getGRRd()),

                    'express_shipment' => array('', '', $expressChecked),
                );

                //file_put_contents('logs/devLog.log', "\n PARTICIPATER CODE: ".$this->getParticipantInfo(0), FILE_APPEND);

                //file_put_contents('logs/devLog.log', "\n STS FIELD MODELS: ".print_r($fieldModels, true), FILE_APPEND);

                foreach ($fieldModels as $fieldModel) {
                    if ($fieldModel->name == 'move_type') {
                        $fieldModel->set('uitype', 3);
                    }
                    if (in_array($fieldModel->name, $registrationFields)) {
                        if ($registrationFieldInfo[$fieldModel->name]) {
                            if ($registrationFieldInfo[$fieldModel->name][0]) {
                                $fieldModel->set('label', $registrationFieldInfo[$fieldModel->name][0]);
                                //file_put_contents('logs/devLog.log', "\n FIELD INFO: ".print_r($fieldModel, true), FILE_APPEND);
                            }
                            if ($registrationFieldInfo[$fieldModel->name][1]) {
                                $fieldModel->set('typeofdata', $registrationFieldInfo[$fieldModel->name][1]);
                            }
                            if ($registrationFieldInfo[$fieldModel->name][2]) {
                                $fieldModel->set('fieldvalue', $registrationFieldInfo[$fieldModel->name][2]);
                            }
                        }
                        if (array_key_exists($fieldModel->name, $registrationInfoBlock)) {
                            $STSFieldModels['LBL_OPPORTUNITY_REGISTRATIONINFO'][$registrationInfoBlock[$fieldModel->name]] = $fieldModel;
                        } elseif (array_key_exists($fieldModel->name, $billingInfoBlock)) {
                            //file_put_contents('logs/devLog.log', "\n NAME: ".$fieldModel->name, FILE_APPEND);
                            //file_put_contents('logs/devLog.log', "\n INDEX: ".$billingInfoBlock[$fieldModel->name], FILE_APPEND);
                            $STSFieldModels['LBL_OPPORTUNITY_BILLINGINFO'][$billingInfoBlock[$fieldModel->name]] = $fieldModel;
                        } elseif (array_key_exists($fieldModel->name, $serviceProvidersBlock)) {
                            $STSFieldModels['LBL_OPPORTUNITY_SERVICEPROVIDERS'][$serviceProvidersBlock[$fieldModel->name]] = $fieldModel;
                        } elseif (array_key_exists($fieldModel->name, $STSInformation)) {
                            $STSFieldModels['LBL_OPPORTUNITY_STSINFORMATION'][$STSInformation[$fieldModel->name]] = $fieldModel;
                        }
                    }
                }

                //$STSFieldModels['LBL_OPPORTUNITY_REGISTRATIONINFO']['grr_estimate'] = Vtiger_Field_Model::getInstance('grr_estimate', Vtiger_Module::getInstance('Quotes'));
                //$STSFieldModels['LBL_OPPORTUNITY_REGISTRATIONINFO']['grr_estimate']->set('fieldvalue', $registrationFieldInfo['grr_estimate'][2]);

                ksort($STSFieldModels['LBL_OPPORTUNITY_REGISTRATIONINFO']);
                ksort($STSFieldModels['LBL_OPPORTUNITY_BILLINGINFO']);
                ksort($STSFieldModels['LBL_OPPORTUNITY_SERVICEPROVIDERS']);
                ksort($STSFieldModels['LBL_OPPORTUNITY_STSINFORMATION']);

                //$this->set('brand', 'AVL');
                //echo '<pre>'.print_r($STSFieldModels['LBL_OPPORTUNITY_STSINFORMATION'][0]->get('fieldvalue')).'</pre>';
                //echo '<pre>'.print_r($this->get('brand'), true).'</pre>';

                //file_put_contents('logs/devLog.log', "\n BILLING INFO: ".print_r($STSFieldModels['LBL_OPPORTUNITY_BILLINGINFO'], true), FILE_APPEND);

                return $STSFieldModels;
    }

    public function getBrand()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT IF(`vtiger_vanlinemanager`.vanline_id = 9, 'NAVL', IF(`vtiger_vanlinemanager`.vanline_id = 1, 'AVL', '')) AS carrier_code FROM `vtiger_vanlinemanager`
	   JOIN `vtiger_agentmanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
	   WHERE `vtiger_agentmanager`.agentmanagerid = ?";
        $result = $db->pquery($sql, [array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser())[0]]);
        $row    = $result->fetchRow();
        if ($row) {
            return $row['carrier_code'];
        }

        return false;
    }

    public function getGRRd()
    {
        $db = PearDatabase::getInstance();
        $estimateId = $db->getOne("SELECT `quoteid` FROM `vtiger_quotes` where `potentialid` = " . $this->getId() . " AND `is_primary` = '1'");
        if ($estimateId == '') {
            return '';
        }

        return Vtiger_Record_Model::getInstanceById($estimateId, 'Estimates')->get('grr_estimate');
    }

    public function getSirvaVanline()
    {
        $db = PearDatabase::getInstance();

        $sql = 'SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?';
        $result = $db->pquery($sql, array($this->getId()));
        $row = $result->fetchRow();

        $ownerId = $row[0];

        file_put_contents('logs/devLog.log', "\n ownerId: ".$ownerId, FILE_APPEND);

        if ($ownerId) {
            $sql = 'SELECT `vtiger_agentmanager`.agentmanagerid FROM `vtiger_agentmanager` JOIN `vtiger_groups` ON  `vtiger_agentmanager`.agency_name = `vtiger_groups`.groupname WHERE groupid = ?';
            $result = $db->pquery($sql, array($ownerId));
            $row = $result->fetchRow();
            $agentId = $row[0];
            file_put_contents('logs/devLog.log', "\n agentId: ".$agentId, FILE_APPEND);
            if ($agentId) {
                $sql = 'SELECT vanline_id FROM `vtiger_agentmanager` WHERE agentmanagerid = ?';
                $result = $db->pquery($sql, array($agentId));
                $row = $result->fetchRow();
                $vanlineId = $row[0];
                file_put_contents('logs/devLog.log', "\n vanlineId: ".$vanlineId, FILE_APPEND);
                if ($vanlineId) {
                    $sql = 'SELECT vanline_name, vanline_id FROM `vtiger_vanlinemanager` WHERE vanlinemanagerid = ?';
                    $result = $db->pquery($sql, array($vanlineId));
                    $row = $result->fetchRow();
                    $vanlineName = $row[0];
                    $vanlineCode = $row[1];
                    if ($vanlineName == 'Allied' || $vanlineCode == 1) {
                        return 'Allied';
                    } elseif ($vanlineName == 'North American Van Lines' || $vanlineCode == 9) {
                        return 'North American';
                    } else {
                        return null;
                    }
                }
            }
        }
        return null;
    }

    public function getIntlQuoteUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&view=IntlQuote&record='.$this->getId();
    }

    public function createQuoteField($params)
    {
        $field = new Vtiger_Field_Model;
        $field->initialize($params);
        return $field;
    }

    public function getCubeSheetData($cubesheetsid)
    {
        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        return $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => $cubesheetsid]);
    }

    public function getIntlQuote()
    {
        $db = PearDatabase::getInstance();

        if ($this->get('contact_id') && $this->get('contact_id') != '') {
            $contactInfo = Vtiger_Record_Model::getInstanceById($this->get('contact_id'));
        }

        $agencyInfo = ['agency_name' => 'Non Agency', 'agency_code' => 'N/A'];
        $userInfo = Users_Record_Model::getCurrentUserModel();
        $accessibleAgency = $userInfo->getAccessibleAgentsForUser();
        if ($accessibleAgency > 0) {
            foreach ($accessibleAgency as $agency_code => $agency_name) {
                $agencyInfo = [
                            'agency_name' => $agency_name,
                            'agency_code' => $agency_code
                        ];
                break;
            }
            if ($agencyInfo['agency_code']!='N/A') {
                $agencyInfo['agency_code'] = $db->getOne("SELECT `agency_code` FROM `vtiger_agentmanager` where `agentmanagerid` = ".$agency_code);
            }
        }

        $cubesheetId = $db->getOne("SELECT `cubesheetsid` FROM `vtiger_cubesheets` where `potential_id` = " . $this->getId() . " AND `is_primary` = 1");
        if ($cubesheetId == '') {
            $cubesheetId = -1;
        }

                //Our values will be stored here after adding everyting up
                $values = [];

        $cubesheetOverallInfo = $this->getCubeSheetData($cubesheetId);
        $cubesheetInfo = $cubesheetOverallInfo['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'];
                //file_put_contents('logs/devLog.log', "\n IO HERE (Record.php:391): DEBUG TRACE\n" . print_r($cubesheetOverallInfo, TRUE), FILE_APPEND);
                //Cubesheets doesn't send an array if there is only 1 element.
                if (!isset($cubesheetInfo[0])) {
                    $cubesheetInfo = [$cubesheetInfo];
                }
                //file_put_contents('logs/devLog.log', "\n IO HERE (Record.php:391): DEBUG TRACE\n" . print_r($cubesheetOverallInfo, TRUE), FILE_APPEND);
                //Filter cubesheet segments into Air/Sea
                $segments['air'] = array_filter($cubesheetInfo, function ($sub) { //Air
                    if (intval($sub['SegmentType']) == 1) {
                        return $sub;
                    }
                });
        $segments['sea'] = array_filter($cubesheetInfo, function ($sub) { //Sea
                    if (intval($sub['SegmentType']) == 3) {
                        return $sub;
                    }
        });

                //Add all segments by type and put them into our $values array
                foreach ($segments as $key => $segment) {
                    $values[$key]['weight'] = array_sum(array_map(function ($sub) {
                        return $sub['TotalWeight'];
                    }, $segment));

                    $values[$key]['cube'] = array_sum(array_map(function ($sub) {
                        return $sub['TotalCube'];
                    }, $segment));
                }

        $defaultValue['LBL_OPPORTUNITY_AGENTINFO'] = array(
                    'to_international'=>['fieldlabel' => 'LBL_TO_INTERNATIONAL','defaultvalue' => 'International'],
                    'to_attention'=>['fieldlabel' => 'LBL_TO_ATTENTION','defaultvalue' => 'SIRVA Quote Team'],
                    'to_request_date'=>['fieldlabel' => 'LBL_TO_REQUEST_DATE','defaultvalue' => date('m-d-Y')],
                    'from_agent_name'=>['fieldlabel' => 'LBL_FROM_AGENT_NAME','defaultvalue' => $agencyInfo['agency_name']],
                    'from_agent_code'=>['fieldlabel' => 'LBL_FROM_AGENT_CODE','defaultvalue' => $agencyInfo['agency_code']],
                    'requested_by'=>['fieldlabel' => 'LBL_REQUESTED_BY','defaultvalue' => $userInfo->get('user_name')],
                    'fax'=>['fieldlabel' => 'LBL_FAX','defaultvalue' => $userInfo->get('phone_fax')],
                    'email'=>['fieldlabel' => 'LBL_EMAIL','defaultvalue' => $userInfo->get('email1')],
                );

        $defaultValue['LBL_OPPORTUNITY_QUOTEINFO'] = array(
                    'transferee_name'=>['fieldlabel' => 'LBL_TRANSFEREE_NAME','defaultvalue' => (isset($contactInfo) ? $contactInfo->get('firstname'). ' ' . $contactInfo->get('lastname') : '')],
                    'potential_id'=>['fieldlabel' => 'LBL_INTLQUOTES_POTENTIALID','defaultvalue' => $this->getId()],
                );

        $defaultValue['LBL_OPPORTUNITY_LOCATIONDETAILS'] = array(
                    'origin_city_country'=>['fieldlabel' => 'LBL_ORIGIN_CITY_COUNTRY', 'defaultvalue' => $this->get('origin_city').', '.$this->get('origin_country')],
                    'destination_city_country'=>['fieldlabel' => 'LBL_DESTINATION_CITY_COUNTRY','defaultvalue' => $this->get('destination_city').', '.$this->get('destination_country')],
                );

        $defaultValue['LBL_OPPORTUNITY_OTHERINFO'] = array(
                    'air_weight'=>['fieldlabel' => 'LBL_AIR_WEIGHT','defaultvalue' => $values['air']['weight']],
                    'air_volume'=>['fieldlabel' => 'LBL_AIR_VOLUME','defaultvalue' => $values['air']['cube']],
                    'fcl_weight'=>['fieldlabel' => 'LBL_FCL_WIEGHT','defaultvalue' => $values['sea']['weight']],
                    'fcl_volume'=>['fieldlabel' => 'LBL_FCL_VOLUME','defaultvalue' => $values['sea']['cube']],
                );

        $sql = "SELECT * FROM `vtiger_intlquote` WHERE `potential_id` = ?";
        $result = $db->pquery($sql, [$this->getId()]);

        $row = $result->fetchRow();
        if ($row) {
            //                    $quoteModel = Vtiger_Record_Model::getInstanceById($row['intlquoteid'], 'IntlQuote');
//                    $mandatoryFieldModels = $quoteModel->getModule()->getMandatoryFieldModels();
                    $quoteModel = Vtiger_Record_Model::getCleanInstance('IntlQuote');
            $cleanInfo = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($quoteModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
            $quoteFieldInfo = $cleanInfo->getStructure();
            foreach ($quoteFieldInfo as $blockLabel => $blockSection) {
                foreach ($blockSection as $field) {
                    $name = $field->get('name');
                    if (isset($row[$name])) {
                        $field->set('fieldvalue', $row[$name]);
                    }
                }
            }
        } else {
            $quoteModel = Vtiger_Record_Model::getCleanInstance('IntlQuote');
            $cleanInfo = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($quoteModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
            $quoteFieldInfo = $cleanInfo->getStructure();

            foreach ($quoteFieldInfo as $blockLabel => $blockSection) {
                foreach ($blockSection as $field) {
                    $name = $field->get('name');
                    $value = $field->get('fieldvalue');
                    if (isset($defaultValue[$blockLabel][$name])&&$value=="") {
                        $field->set('fieldvalue', $defaultValue[$blockLabel][$name]['defaultvalue']);
                    }
                }
            }
        }
        $moduleModel      = $quoteModel->getModule();
        $fieldList        = $moduleModel->getFields();
        return $quoteFieldInfo;
    }


    public function isNationalAccount()
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT lead_type FROM `vtiger_potential` WHERE potentialid = ?';
        $result = $db->pquery($sql, array($this->getId()));
        $row = $result->fetchRow();
        if ($row[0] == 'National Account') {
            return true;
        } else {
            return false;
        }
    }

    public function isConsumer()
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT lead_type FROM `vtiger_potential` WHERE potentialid = ?';
        $result = $db->pquery($sql, array($this->getId()));
        $row = $result->fetchRow();
        if ($row[0] == 'Consumer') {
            return true;
        } else {
            return false;
        }
    }

    public function getSubContract()
    {
    }

    public function getTariffType()
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT effective_tariff FROM `vtiger_quotes` WHERE potentialid = ? AND is_primary = 1';
        $result = $db->pquery($sql, array($this->getId()));
        $row = $result->fetchRow();
        if ($row[0]) {
            $tariffId = $row[0];
        } else {
            $sql = 'SELECT effective_tariff FROM `vtiger_quotes` WHERE potentialid = ?';
            $result = $db->pquery($sql, array($this->getId()));
            $row = $result->fetchRow();
            $tariffId = $row[0];
        }
        //file_put_contents('logs/devLog.log', "\n TARIFF ID: ".$tariffId, FILE_APPEND);
        $sql = 'SELECT custom_tariff_type FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ?';
        $result = $db->pquery($sql, array($tariffId));
        $row = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n CUSTOM JS : ".$row[0], FILE_APPEND);
        if ($row[0]) {
            return $row[0];
        }
        return null;
    }

    public function getTrucking()
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT express_truckload FROM `vtiger_quotes` WHERE potentialid = ? AND is_primary = 1';
        $result = $db->pquery($sql, array($this->getId()));
        $row = $result->fetchRow();
        if ($row[0]) {
            $expressTrucking = $row[0];
        } else {
            $sql = 'SELECT express_truckload FROM `vtiger_quotes` WHERE potentialid = ?';
            $result = $db->pquery($sql, array($this->getId()));
            $row = $result->fetchRow();
            $expressTrucking = $row[0];
        }
        return $expressTrucking;
    }

    public function getPrimaryEstimateRecordModel($getAny = true, $setype = 'Estimates', $relatedField = 'potentialid')
    {
        //@NOTE: making it like this so a merge doesn't add the function back.
        return parent::getPrimaryEstimateRecordModel($getAny, $setype, $relatedField);
    }

    public function getPrimarySurveyRecordModel(){
        $surveyRecordModel = false;
        $db = PearDatabase::getInstance();
        $stmt = "SELECT cubesheetsid FROM `vtiger_cubesheets`"
                    . " INNER JOIN `vtiger_crmentity` ON (`vtiger_cubesheets`.cubesheetsid = `vtiger_crmentity`.crmid)"
                    . " WHERE `vtiger_cubesheets`.potential_id = ? "
                    . " AND `vtiger_crmentity`.deleted = 0"
                    . " LIMIT 1";
        $result = $db->pquery($stmt, [$this->getId()]);

        if (method_exists($result, 'fetchRow') && $row = $result->fetchRow()) {
            $surveyID = $row['cubesheetsid'];
        }

        if ($surveyID) {
            try {
                $surveyRecordModel = Vtiger_Record_Model::getInstanceById($surveyID, 'Cubesheets');
            } catch (WebServiceException $ex) {
                $surveyRecordModel = false;
            }
        }

        return $surveyRecordModel;
    }
    public function getParticipantInfo($agentType, $infoColumn)
    {
        //file_put_contents('logs/devLog.log', "\n IN GET PARTICIPANT Info", FILE_APPEND);
        $db = PearDatabase::getInstance();
        //grab agentid for participant
		$sql = 'SELECT agents_id FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND agent_type = ? AND deleted=0';
        $result = $db->pquery($sql, array($this->getId(), $agentType));
        if (!$result) {
            //didn't find whatever in the table just give up and go home.
            return null;
        }
        $row = $result->fetchRow();
        $agentId = $row[0];
        $rv = null;
        try {
            //wrapped in a try to prevent/hide the stupid Record Permission denied error message.
            //pull the Agents Module record for this ID
            $agentRecord = Vtiger_Record_Model::getInstanceById($agentId, 'Agents');
            if ($agentRecord) {
                //process the request IFF we have an agentRecord
                switch ($infoColumn) {
                    case 'agency_code':
                    case 'code':
                        $infoColumn = 'agent_number';
                        break;
                    case 'agency_name':
                    case 'name':
                        $infoColumn = 'agentname';
                        break;
                    case 'city':
                        $infoColumn = 'agent_city';
                        break;
                    case 'state':
                        $infoColumn = 'agent_state';
                        break;
                    default:
                        //error, it's not really an error we just don't have a special map to something,
                        // so like let's hope the caller is right about the column name
                        break;
                }
                $rv = $agentRecord->get($infoColumn);
            }
        } catch (Exception $e) {
            //sigh do something in the future probably.
            //@TODO: add some error handling that isn't "Record Permission Denied" total fail
        }
        return $rv;
    }

/**
     * Function returns the url for create event
     * @return <String>
     */
    public function getCreateEventUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
    }

    /**
     * Function returns the url for create todo
     * @return <String>
     */
    public function getCreateTaskUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
    }


	public function getMappingFields($forModuleName) {
        $res = [];
        if($forModuleName == 'Estimates' || $forModuleName == 'Actuals')
        {
            foreach ($this->getInventoryMappingFields() as $field)
            {
                $res[$field['parentField']] = $field['inventoryField'];
            }
            return $res;
        }
        if($forModuleName == 'Orders')
        {
            foreach ($this->getOrderMappingFields() as $field)
            {
                $res[$field['parentField']] = $field['orderField'];
            }
            return $res;
        }
        if($forModuleName == 'Surveys')
        {
            foreach ($this->getSurveyMappingFields() as $field)
            {
                $res[$field['parentField']] = $field['surveyField'];
            }
            return $res;
        }
        return $res;
    }

    /**
     * Function to get List of Fields which are related from Opportunity to Inventory Record
     * @return <array>
     */
    public function getInventoryMappingFields()
    {
        $arr = array(
                ['parentField'=>'id',                      'inventoryField'=>'potential_id',           'defaultValue'=>''],
                ['parentField'=>'related_to',              'inventoryField'=>'account_id',             'defaultValue'=>''],
                ['parentField'=>'contact_id',              'inventoryField'=>'contact_id',             'defaultValue'=>''],
                ['parentField'=>'commodities',             'inventoryField'=>'commodities',            'defaultValue'=>''],
                ['parentField'=>'business_line',           'inventoryField'=>'business_line_est',      'defaultValue'=>''],
                ['parentField'=>'origin_address1',         'inventoryField'=>'origin_address1',        'defaultValue'=>''],
                ['parentField'=>'origin_address2',         'inventoryField'=>'origin_address2',        'defaultValue'=>''],
                ['parentField'=>'origin_city',             'inventoryField'=>'origin_city',            'defaultValue'=>''],
                ['parentField'=>'origin_state',            'inventoryField'=>'origin_state',           'defaultValue'=>''],
                ['parentField'=>'origin_phone1',           'inventoryField'=>'origin_phone1',          'defaultValue'=>''],
                ['parentField'=>'origin_phone2',           'inventoryField'=>'origin_phone2',          'defaultValue'=>''],
                ['parentField'=>'origin_zip',              'inventoryField'=>'origin_zip',             'defaultValue'=>''],
                ['parentField'=>'destination_address1',    'inventoryField'=>'destination_address1',   'defaultValue'=>''],
                ['parentField'=>'destination_address2',    'inventoryField'=>'destination_address2',   'defaultValue'=>''],
                ['parentField'=>'destination_city',        'inventoryField'=>'destination_city',       'defaultValue'=>''],
                ['parentField'=>'destination_state',       'inventoryField'=>'destination_state',      'defaultValue'=>''],
                ['parentField'=>'destination_zip',         'inventoryField'=>'destination_zip',        'defaultValue'=>''],
                ['parentField'=>'destination_phone1',      'inventoryField'=>'destination_phone1',     'defaultValue'=>''],
                ['parentField'=>'destination_phone2',      'inventoryField'=>'destination_phone2',     'defaultValue'=>''],
                ['parentField'=>'load_date',               'inventoryField'=>'load_date',              'defaultValue'=>''],
                ['parentField'=>'billing_type',            'inventoryField'=>'billing_type',           'defaultValue'=>''],

                ['parentField'=>'pack_date',               'inventoryField'=>'pack_date',              'defaultValue'=>''],
                ['parentField'=>'pack_to_date',            'inventoryField'=>'pack_to_date',           'defaultValue'=>''],
                ['parentField'=>'preffered_ppdate',        'inventoryField'=>'preffered_ppdate',       'defaultValue'=>''],
                ['parentField'=>'load_date',               'inventoryField'=>'load_date',              'defaultValue'=>''],
                ['parentField'=>'load_to_date',            'inventoryField'=>'load_to_date',           'defaultValue'=>''],
                ['parentField'=>'preferred_pldate',        'inventoryField'=>'preferred_pldate',       'defaultValue'=>''],
                ['parentField'=>'deliver_date',            'inventoryField'=>'deliver_date',           'defaultValue'=>''],
                ['parentField'=>'deliver_to_date',         'inventoryField'=>'deliver_to_date',        'defaultValue'=>''],
                ['parentField'=>'preferred_pddate',        'inventoryField'=>'preferred_pddate',       'defaultValue'=>''],
                ['parentField'=>'survey_date',             'inventoryField'=>'survey_date',            'defaultValue'=>''],
                ['parentField'=>'survey_time',             'inventoryField'=>'survey_time',            'defaultValue'=>''],
                ['parentField'=>'followup_date',           'inventoryField'=>'followup_date',          'defaultValue'=>''],
                ['parentField'=>'decision_date',           'inventoryField'=>'decision_date',          'defaultValue'=>''],
                ['parentField'=>'days_to_move',            'inventoryField'=>'days_to_move',           'defaultValue'=>''],
                //array('parentField'=>'opp_type', 'inventoryField'=>'lead_type', 'defaultValue'=>''),
                ['parentField'=>'business_line2',          'inventoryField'=>'business_line_est2',     'defaultValue'=>''],
                ['parentField'=>'billing_type',            'inventoryField' => 'billing_type',         'defaultValue'=>''],
        );
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $arr[] = array('parentField'=>'related_to', 'inventoryField'=>'account_id', 'defaultValue'=>'');
            $arr[] = array('parentField'=>'lead_type', 'inventoryField'=>'lead_type', 'defaultValue'=>'');
            $arr[] = array('parentField'=>'shipper_type', 'inventoryField'=>'shipper_type', 'defaultValue'=>'');
            $arr[] = array('parentField'=>'move_type', 'inventoryField'=>'move_type', 'defaultValue'=>'');
            $arr[] = array('parentField'=>'origin_country', 'inventoryField'=>'estimates_origin_country', 'defaultValue'=>'');
            $arr[] = array('parentField'=>'destination_country', 'inventoryField'=>'estimates_destination_country', 'defaultValue'=>'');
        }

        return $arr;
    }

    public function getOrderMappingFields()
    {
        file_put_contents('logs/OpportunitiesMapping.log', date('Y-m-d H:i:s - ')."aaEntering1 getOrderMappingFields function\n", FILE_APPEND);
        return array(
                //Details
                //array('parentField'=>'potentialid', 'ordersField'=>'orders_potentials', 'defaultValue'=>''),
                //array('parentField'=>'potential_no', 'projectField'=>'potential_no', 'defaultValue'=>''),
                //array('parentField'=>'potentialname', 'projectField'=>'projectname', 'defaultValue'=>''),
                // array('parentField'=>'amount', 'projectField'=>'
                // array('parentField'=>'potentialtype', 'projectField'=>'
                //array('parentField'=>'related_to', 'orderField'=>'orders_account', 'defaultValue'=>''),
            //	array('parentField'=>'contact_id', 'orderField'=>'orders_contacts', 'defaultValue'=>''),
                //array('parentField'=>'business_line', 'orderField'=>'orderspriority', 'defaultValue'=>''),
                //array('parentField'=>'amount', 'projectField'=>'targetbudget', 'defaultValue'=>''),
                // array('parentField'=>'estimate_type', 'projectField'=>'
                // array('parentField'=>'pricing_type', 'projectField'=>'

                //Details Fields
                array('parentField'=>'business_line', 'orderField'=>'business_line', 'defaultValue'=>''),
                array('parentField'=>'business_line2', 'orderField'=>'business_line2', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'orderField'=>'orders_contacts', 'defaultValue'=>''),
                array('parentField'=>'related_to', 'orderField'=>'orders_account', 'defaultValue'=>''),
                array('parentField'=>'agentid', 'orderField'=>'agentid', 'defaultValue'=>''),
                array('parentField'=>'is_competitive', 'orderField'=>'competitive', 'defaultValue'=>''),
                array('parentField'=>'billing_type', 'orderField'=>'billing_type', 'defaultValue'=>''),
                array('parentField'=>'authority', 'orderField'=>'authority', 'defaultValue'=>''),
                array('parentField'=>'amount', 'orderField'=>'orders_etotal', 'defaultValue'=>''),
                array('parentField'=>'leadsource', 'orderField'=>'leadsource', 'defaultValue'=>''),
                array('parentField' => 'oppotunitiescontract', 'orderField' => 'account_contract', 'defaultValue' => ''),

                //Origin Address Fields
                array('parentField'=>'origin_address1', 'orderField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'orderField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'orderField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'orderField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'orderField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'origin_country', 'orderField'=>'origin_country', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'orderField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'orderField'=>'origin_phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_description', 'orderField'=>'origin_description', 'defaultValue'=>''),

                //Destination Address Fields
                array('parentField'=>'destination_address1', 'orderField'=>'destination_address1', 'defaultValue'=>''),
                array('parentField'=>'destination_address2', 'orderField'=>'destination_address2', 'defaultValue'=>''),
                array('parentField'=>'destination_city', 'orderField'=>'destination_city', 'defaultValue'=>''),
                array('parentField'=>'destination_state', 'orderField'=>'destination_state', 'defaultValue'=>''),
                array('parentField'=>'destination_zip', 'orderField'=>'destination_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_country', 'orderField'=>'destination_country', 'defaultValue'=>''),
                array('parentField'=>'destination_phone1', 'orderField'=>'destination_phone1', 'defaultValue'=>''),
                array('parentField'=>'destination_phone2', 'orderField'=>'destination_phone2', 'defaultValue'=>''),
                array('parentField'=>'destination_description','orderField'=>'destination_description', 'defaultValue'=>''),

                //Dates
                array('parentField'=>'pack_date', 'orderField'=>'orders_pdate', 'defaultValue'=>''),
                array('parentField'=>'pack_to_date', 'orderField'=>'orders_ptdate', 'defaultValue'=>''),
                array('parentField'=>'preffered_ppdate', 'orderField'=>'orders_ppdate', 'defaultValue'=>''),
                array('parentField'=>'load_date', 'orderField'=>'orders_ldate', 'defaultValue'=>''),
                array('parentField'=>'load_to_date', 'orderField'=>'orders_ltdate', 'defaultValue'=>''),
                array('parentField'=>'preferred_pldate', 'orderField'=>'orders_pldate', 'defaultValue'=>''),
                array('parentField'=>'deliver_date', 'orderField'=>'orders_ddate', 'defaultValue'=>''),
                array('parentField'=>'deliver_to_date', 'orderField'=>'orders_dtdate', 'defaultValue'=>''),
                array('parentField'=>'preferred_pddate', 'orderField'=>'orders_pddate', 'defaultValue'=>''),
                array('parentField'=>'survey_date', 'orderField'=>'orders_surveyd', 'defaultValue'=>''),
                array('parentField'=>'survey_time', 'orderField'=>'orders_surveyt', 'defaultValue'=>''),
                array('parentField'=>'commodities', 'orderField'=>'commodities', 'defaultValue'=>''),
                //array('parentField'=>'followup_date', 'projectField'=>'followup_date', 'defaultValue'=>''),
                //array('parentField'=>'decision_date', 'projectField'=>'decision_date', 'defaultValue'=>'')

        );
    }

    public function getSurveyMappingFields()
    {
        return array(
                array('parentField'=>'related_to', 'inventoryField'=>'account_id', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'inventoryField'=>'contact_id', 'defaultValue'=>''),
                array('parentField'=>'business_line', 'inventoryField'=>'business_line_est', 'defaultValue'=>''),
                array('parentField'=>'origin_address1', 'inventoryField'=>'address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'inventoryField'=>'address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'inventoryField'=>'city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'inventoryField'=>'state', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'inventoryField'=>'phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'inventoryField'=>'phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'inventoryField'=>'zip', 'defaultValue'=>''),
        );
    }
    /*
    public function getProjectMappingFields() {
        return array(
                //Details
                //array('parentField'=>'potentialid', 'projectField'=>'potentialid', 'defaultValue'=>''),
                //array('parentField'=>'potential_no', 'projectField'=>'potential_no', 'defaultValue'=>''),
                //array('parentField'=>'potentialname', 'projectField'=>'projectname', 'defaultValue'=>''),
                // array('parentField'=>'amount', 'projectField'=>'
                // array('parentField'=>'potentialtype', 'projectField'=>'
                //array('parentField'=>'related_to', 'projectField'=>'linktoaccounts', 'defaultValue'=>''),
                //array('parentField'=>'contact_id', 'projectField'=>'contacts_id', 'defaultValue'=>''),
                //array('parentField'=>'business_line', 'projectField'=>'business_line', 'defaultValue'=>''),
                //array('parentField'=>'amount', 'projectField'=>'targetbudget', 'defaultValue'=>''),
                // array('parentField'=>'estimate_type', 'projectField'=>'
                // array('parentField'=>'pricing_type', 'projectField'=>'

                //Origin Address Fields
                array('parentField'=>'origin_address1', 'projectField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'projectField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'projectField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'projectField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'projectField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'projectField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'projectField'=>'origin_phone2', 'defaultValue'=>''),

                //Destination Address Fields
                array('parentField'=>'destination_address1', 'projectField'=>'destination_address1', 'defaultValue'=>''),
                array('parentField'=>'destination_address2', 'projectField'=>'destination_address2', 'defaultValue'=>''),
                array('parentField'=>'destination_city', 'projectField'=>'destination_city', 'defaultValue'=>''),
                array('parentField'=>'destination_state', 'projectField'=>'destination_state', 'defaultValue'=>''),
                array('parentField'=>'destination_zip', 'projectField'=>'destination_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_phone1', 'projectField'=>'destination_phone1', 'defaultValue'=>''),
                array('parentField'=>'destination_phone2', 'projectField'=>'destination_phone2', 'defaultValue'=>''),

                //Dates
                array('parentField'=>'pack_date', 'projectField'=>'pack_date', 'defaultValue'=>''),
                array('parentField'=>'pack_to_date', 'projectField'=>'pack_to_date', 'defaultValue'=>''),
                array('parentField'=>'load_date', 'projectField'=>'load_date', 'defaultValue'=>''),
                array('parentField'=>'load_to_date', 'projectField'=>'load_to_date', 'defaultValue'=>''),
                array('parentField'=>'deliver_date', 'projectField'=>'deliver_date', 'defaultValue'=>''),
                array('parentField'=>'deliver_to_date', 'projectField'=>'deliver_to_date', 'defaultValue'=>''),
                array('parentField'=>'survey_date', 'projectField'=>'survey_date', 'defaultValue'=>''),
                array('parentField'=>'survey_time', 'projectField'=>'survey_time', 'defaultValue'=>''),
                array('parentField'=>'followup_date', 'projectField'=>'followup_date', 'defaultValue'=>''),
                array('parentField'=>'decision_date', 'projectField'=>'decision_date', 'defaultValue'=>'')
        );
    }*/

    public function getCreateProjectUrl()
    {
        $projectModuleModel = Vtiger_Module_Model::getInstance('Orders');
        return $projectModuleModel->getCreateRecordUrl().'&sourceRecord='.$this->getId().'&sourceModule='.$this->getModuleName().'&potential_id='.$this->getId().'&relationOperation=true';
    }

    public function getCreateQuoteUrl()
    {
        $quoteModuleModel = Vtiger_Module_Model::getInstance('Estimates');
        return $quoteModuleModel->getCreateRecordUrl().'&sourceRecord='.$this->getId().'&sourceModule='.$this->getModuleName().'&potential_id='.$this->getId().'&relationOperation=true';
    }

    /**
    * Function returns the url for create survey
    * @return <String>
    */
    public function getCreateSurveyUrl()
    {
        $surveyModuleModel = Vtiger_Module_Model::getInstance('Surveys');
        return $surveyModuleModel->getCreateRecordUrl().'&sourceRecord='.$this->getId().'&sourceModule='.$this->getModuleName().'&potential_id='.$this->getID().'&relationOperation=true';
    }

    public function allowedSTS()
    {
        //@TODO: to be removed when we want to have production also have the STS button.
        //if (getenv('PHP_ENV') == 'prod' || getenv('PHP_ENV' == 'PROD')) {
        //	return false;
        //}

        $db = $db ?: PearDatabase::getInstance();
        $opportunity   = Vtiger_Record_Model::getInstanceById($this->getId(), 'Opportunities');

        //Check how many auto's are not registered
        $autoQuery     = $db->pquery('SELECT * FROM `vtiger_autospotquote` WHERE `estimate_id` = (SELECT `quoteid` FROM `vtiger_quotes` WHERE `potentialid` = ? AND `is_primary` = 1 LIMIT 1) AND NULLIF(`registration_number`, "") IS NULL', [$this->getId()]);
        $autoCount     = $db->num_rows($autoQuery);

        //If this is already registered and we don't have any autos to register, then no need to register again
        if ($opportunity->get('business_channel') == 'Military' || ($opportunity->get('register_sts_number') && $autoCount == 0)){
            return false;
        }

        //Check to see if max3/4
        $result  = $db->pquery("SELECT `effective_tariff` FROM `vtiger_quotes` where `potentialid` = ? AND `is_primary` = 1 LIMIT 1", [$this->getId()]);
        while ($row = $db->fetch_row($result)) {
            $result = $db->pquery("SELECT `tariff_name` FROM `vtiger_tariffs` where `tariffsid` = ?", [$row['effective_tariff']]);
            while ($row = $db->fetch_row($result)) {
                $tariffName = trim($row['tariff_name']);
                if($tariffName == 'MAX3' || $tariffName == 'MAX4'){
                    $max34 = true;
                }
            }
        }
        if($max34){
            return true;
        }

        switch ($opportunity->get('move_type')) {
            case 'Intrastate':
            case 'Variable':
            case 'Local Canada':
            case 'Local US':
            case 'Max 3':
            case 'Max 4':
            case 'Intra-Provincial':
            case 'Sirva Military':
            case 'International':
                return false;
                break;

            default:
                return true;
                break;
        }
    }

    /**
     * Static Function to get the list of records matching the search key
     * @param <String> $searchKey
     * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
     */
    public static function getSearchResult($searchKey, $module=false)
    {
        $db = PearDatabase::getInstance();

        $query = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
        $params = array("%$searchKey%");

        if ($module !== false) {
            $query .= " AND (setype='Opportunities' OR setype='Potentials')";
            $params[] = $module;
        }

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if ($_REQUEST['agentId']){
                $query .= " AND vtiger_crmentity.agentid=?";
                $params[]=$_REQUEST['agentId'];
            }
        }

        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';

        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);

        $moduleModels = $matchingRecords = $leadIdsList = array();
        for ($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);

        for ($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
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
                $recordsCount++;
            }
        }
        return $matchingRecords;
    }

    public function updateOppFields($crmid, $crmrelid = '')
    {
        $mainOppRecord = Vtiger_Record_Model::getInstanceById($crmid, "Opportunities");
        $mainOppData = $mainOppRecord->getData();

        if ($crmrelid != '') {
            $newRelatedOppRecord = Vtiger_Record_Model::getInstanceById($crmrelid, "Opportunities");
            $newRelatedOppData = $newRelatedOppRecord->getData();
        } else {
            $newRelatedOppData = array();
        }


        try {
            $this->updateSalesStage($mainOppData, $newRelatedOppData);
            $this->updateOrderNumber($mainOppData, $newRelatedOppData);
            $this->updateRegistrationNumber($mainOppData, $newRelatedOppData);
        } catch (Exception $exc) {
            $error = $exc->getTraceAsString();
        }
    }

    public function updateSalesStage($mainOppData, $newRelatedOppData)
    {
        include_once 'include/Webservices/Revise.php';
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        $relatedIsWon = false;

        if (isset($newRelatedOppData['id'])) {
            $oppArray[] = array("crmid" => $newRelatedOppData['id']);
            if ($newRelatedOppData['sales_stage'] === "Closed Won") {
                $oppArray[] = array("crmid" => $mainOppData['id']);
                $relatedIsWon = true;
            }
        }

        if ($mainOppData['sales_stage'] === "Closed Won" || $relatedIsWon) {
            $res = $db->pquery("SELECT DISTINCT vtiger_crmentity.crmid,vtiger_crmentity.smownerid FROM vtiger_potential
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
                    INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                    WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentityrel.relmodule = 'Opportunities' AND vtiger_crmentityrel.crmid = ?", array($mainOppData['id']));

            if ($db->num_rows($res) > 0) {
                while ($row = $db->fetch_row($res)) {
                    $oppArray[] = array("crmid" => $row['crmid']);
                }
            }
            foreach ($oppArray as $opp) {
                $oppWB = array(
                    'id' => vtws_getWebserviceEntityId('Opportunities', $opp['crmid']),
                    'sales_stage' => "Closed Won",
                );
                $_REQUEST['repeat'] = 0; //Need to add this otherwise wont update the Opp. Not sure why this was added.
                vtws_revise($oppWB, $currentUser);
            }
        }
    }

    public function updateOrderNumber($mainOppData, $newRelatedOppData)
    {
        include_once 'include/Webservices/Revise.php';
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        if ($mainOppData['order_number'] !== "") {
            $orderNumber = $mainOppData['order_number'];
        } elseif (isset($newRelatedOppData['order_number']) && $newRelatedOppData['order_number'] !== "") {
            $oppArray[] = array("crmid" => $mainOppData['id']);
            $orderNumber = $newRelatedOppData['order_number'];
        }

        if ($orderNumber !== "") {
            $res = $db->pquery("SELECT DISTINCT vtiger_crmentity.crmid,vtiger_crmentity.smownerid
                    FROM vtiger_potential INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
                    INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                    WHERE vtiger_crmentity.deleted = 0
                    AND vtiger_crmentityrel.relmodule = 'Opportunities'
                    AND vtiger_crmentityrel.crmid = ?", array($mainOppData['id']));

            if ($db->num_rows($res) > 0) {
                while ($row = $db->fetch_row($res)) {
                    $oppArray[] = array("crmid" => $row['crmid']);
                }
            }
            foreach ($oppArray as $opp) {
                $oppWB = array(
                    'id' => vtws_getWebserviceEntityId('Opportunities', $opp['crmid']),
                    'order_number' => $orderNumber,
                );
                $_REQUEST['repeat'] = 0; //Need to add this otherwise wont update the Opp. Not sure why this was added.
                vtws_revise($oppWB, $currentUser);
            }
        }
    }

    /**
     * @param $mainOppData
     * @param $newRelatedOppData
     * @throws WebServiceException
     */
    public function updateRegistrationNumber($mainOppData, $newRelatedOppData)
    {
        include_once 'include/Webservices/Revise.php';
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $orderNumber = null;
        if ($mainOppData['register_sts_number'] !== "") {
            $orderNumber = $mainOppData['register_sts_number'];
        } elseif (isset($newRelatedOppData['register_sts_number']) && $newRelatedOppData['register_sts_number'] !== "") {
            $oppArray[] = array("crmid" => $mainOppData['id']);
            $orderNumber = $newRelatedOppData['register_sts_number'];
        }

        if ($orderNumber !== "") {
            $res = $db->pquery("SELECT DISTINCT vtiger_crmentity.crmid,vtiger_crmentity.smownerid
                    FROM vtiger_potential INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
                    INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                    WHERE vtiger_crmentity.deleted = 0
                    AND vtiger_crmentityrel.relmodule = 'Opportunities'
                    AND vtiger_crmentityrel.crmid = ?", array($mainOppData['id']));

            $oppArray = array();

            if ($db->num_rows($res) > 0) {
                while ($row = $db->fetch_row($res)) {
                    $oppArray[] = array("crmid" => $row['crmid']);
                }
            }
            foreach ($oppArray as $opp) {
                $oppWB = array(
                    'id' => vtws_getWebserviceEntityId('Opportunities', $opp['crmid']),
                    'register_sts_number' => $orderNumber,
                );
                $_REQUEST['repeat'] = 0; //Need to add this otherwise wont update the Opp. Not sure why this was added.
                vtws_revise($oppWB, $currentUser);
            }
        }
    }

    public function getContactPhones()
    {
        $recordId = $this->getId();
        $recordRawData = $this->getRawData();
        $contactId = $recordRawData['contact_id'];

        $phones = Vtiger_Cache::get('Opp-Popup', 'Contact-' . $contactId);

        if ($phones || empty($phones)) {
            if ($contactId && isRecordExists($contactId)) {
                $contactRecordModel = Vtiger_Record_Model::getInstanceById($contactId, 'Contacts');
                $phones = '<td>' . $this->format_telephone($contactRecordModel->get('phone')) . '</td>';
                $phones .= '<td>' . $this->format_telephone($contactRecordModel->get('mobile'))  . '</td>';
                $phones .= '<td>' . $this->format_telephone($contactRecordModel->get('homephone'))  . '</td>';
            } else {
                $phones = '<td></td>';
                $phones .= '<td></td>';
                $phones .= '<td>s</td>';
            }
            Vtiger_Cache::set('Opp-Popup', 'Contact-' . $contactId, $phones);
        }


        return $phones;
    }

    public function format_telephone($phone_number)
    {
        if ($phone_number =='') {
            return '';
        }
        $cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
        preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
        return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
    }

    public function isRegisteredSTS()
    {
        if ($this->getId()) {
            $db  = PearDatabase::getInstance();
            $sql = "SELECT register_sts FROM `vtiger_potential` WHERE potentialid=?";
            $res = $db->pquery($sql, [$this->getId()]);

            return $res->fields['register_sts'] == 1;
        }

        return false;
    }

    public static function getSalesPeopleByUserAgency() {
        return Opportunities_Record_Model::getCleanInstance("Opportunities")
            ->getSalesPeopleByOwner();
    }

    public function getSalesPeopleByOwner($owner = false) {
        $user = Users_Record_Model::getCurrentUserModel();
        $user_agents = explode(' |##| ',$user->get('agent_ids'));
        $db = &PearDatabase::getInstance();
        // Backup logic for when $owner is not supplied.
        $useUserAgents = !$owner;
        if($useUserAgents) {
            $owner = $this->get('agentid');
            // Get user data if it's a new Opp.
            if(!$owner) {
                $owner = $user_agents[0];
                if($user->isVanlineUser()) {
                    // If both vanlines are present, prioritize NAVL due to it's place in the Owner picklist.
                    if(sizeof($user_agents) > 1) {
                        $brand = AgentManager_GetBrand_Action::retrieve($owner);
                        if($brand == "AVL") {
                            $owner = $user_agents[1];
                        }
                    }
                    $res = $db->pquery("SELECT agentmanagerid FROM vtiger_agentmanager WHERE vanline_id = ? LIMIT 1", [$owner]);
                    if($res) {
                        $owner = $res->fetchRow()[0];
                    }else{
                        // Should probably put some error handling here.
                    }
                }
            }
        }
        $owner = [$owner];

        if(getenv('INSTANCE_NAME') == 'sirva' && $useUserAgents) {
        // Guess SIRVA no want this no more.
        //  // Get participating agents first, then merge.
        //  $partAgents = [];
        //  $sql = "SELECT agentmanager_id FROM `vtiger_participatingagents` WHERE rel_crmid=?";
        //  $result = $db->pquery($sql, [$this->getID()]);
        //  if($result) {
        //      while($row = $result->fetchRow()) {
        //          $partAgents[] = $row[0];
        //      }
        //  }
          $owner = array_unique(array_merge($owner,$user_agents));
        }
        $members = [];
        foreach($owner as $ownerId) {
            if (!$ownerId) {
                continue;
            }
            try {
                $agency  = AgentManager_Record_Model::getInstanceById($ownerId, 'AgentManager');
                $members = array_merge($members, $agency->getUsersByAgency());
            } catch (Exception $ex) {
                //either the record was deleted or not found. This is a valid error.
                //but this isn't really the place to worry about it.
            }
        }

        $salesPeople = [];

        if(count($members) > 0) {
            foreach ($members as $member) {
                if (!$member || !$member['id']) {
                    continue;
                }
                try {
                    $userRecord = Users_Record_Model::getInstanceById($member['id'], 'Users');
                    if (!$userRecord->isSalesUser()) {
                        continue;
                    }
                    $salesPeople[$userRecord->get('id')] = $userRecord->get('first_name').' '.$userRecord->get('last_name');
                } catch (Exception $ex) {
                    //we can return deleted users from that function above, we can't pull them use them for this so skip them.
                }
            }
        }
        return $salesPeople;
    }

    public static function handleAdditionalListViewLogic(&$rawData, &$record) {
        $db = &PearDatabase::getInstance();

        $sql = "SELECT vtiger_users.first_name, vtiger_users.last_name FROM vtiger_users LEFT JOIN vtiger_crmentity ON vtiger_crmentity.smownerid = vtiger_users.id WHERE vtiger_crmentity.crmid = ?";
        $result = $db->pquery($sql, array($record['id']));
        $row = $result->fetchRow();
        if ($row != null) {
            $record['assigned_user_id'] = $row[0].' '.$row[1];
        } else {
            $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
            $result = $db->pquery($sql, array($rawData['smownerid']));
            $row = $result->fetchRow();
            if ($row != null) {
                $record['assigned_user_id'] = $row[0];
            } else {
                $record['assigned_user_id'] = '--';
            }
        }
        //sales person display
        $sql = "SELECT first_name, last_name FROM `vtiger_users` WHERE id=?";
        $result = $db->pquery($sql, array($rawData['sales_person']));
        $row = $result->fetchRow();
        if ($row != null) {
            $record['sales_person'] = $row[0].' '.$row[1];
        } else {
            $record['sales_person'] = '--';
        }
        //agent owner display
        $sql = "SELECT agency_name, agency_code FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
        $result = $db->pquery($sql, array($rawData['agentid']));
        $row = $result->fetchRow();
        if ($row != null) {
            $record['agentid'] = ' ('.$row['agency_code'].') '.$row['agency_name'];
        } else {
            $record['agentid'] = '--';
        }
    }
}
