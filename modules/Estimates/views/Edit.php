<?php

/****************************************************************************************
 * @author             Louis Robinson
 * @file               Edit.php
 * @description        Overridden so we can assign what we wish and get extras from updates
 * @contact        lrobinson@igcsoftware.com
 * @company            IGC Software
 ****************************************************************************************/

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';

require_once('ViewHandler.php');


class Estimates_Edit_View extends Quotes_Edit_View
{
    public $recordModel;
    public $ordersRecordModel;
    public $viewHandler;
    public $tariffInfo;
    public $recordId;

    public function __construct()
    {
        parent::__construct();
        $this->viewHandler = new Estimates_ViewHandler($this);
        $this->exposeMethod('reloadContents');
        //$this->exposeMethod('updateLocalTariffSections');
        //$this->exposeMethod('updateLocalTariff');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $record           = $request->get('record');

        if(!empty($record) && $record != ''){
            $recordInstance = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        }

        if(!empty($recordInstance) && $recordInstance != false && $recordInstance->get('quotestage') == 'Accepted' && $request->get('isDuplicate') != 'true'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED_ESTIMATE_ACCEPTED'));
        }else{
            parent::checkPermission($request);
        }
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        } else {
            $viewer = $this->getViewer($request);
            if($this->assignDefaultVars($request, $viewer)) {
                //$moduleName = $request->getModule();
                if(!isset($_REQUEST['isDuplicate']))
                {
                    $viewer->assign('DISABLE_SAVE', true);
                }elseif($request->get('isDuplicate') == true){
                    $viewer->assign('RECORD_ID', '');
                    $viewer->assign('MODE', '');
                    $viewer->assign('IS_DUPLICATE', 'yes');

                }

                $viewer->view('EditViewHeader.tpl', 'Estimates');

                $this->viewFull($request);


                $viewer->view('EditViewActions.tpl', 'Estimates');
            }
        }
    }

    protected function assignDefaultVars(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $db = &PearDatabase::getInstance();
        $recordId = $request->get('record');
        if ($recordId == 'false') {
            $recordId = null;
        }
        $this->recordId = $recordId;
        $moduleName = $request->getModule();
        if($recordId)
        {
            $this->recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);

            if($request->get('isDuplicate') == true){
                $this->recordModel->set('quotestage', 'Created');
                $this->recordModel->set('subject', $this->recordModel->get('subject').' - Copy');
                if(getenv('INSTANCE_NAME') != 'sirva') {
                    $this->recordModel->set('is_primary', 0);
                }
            }

            $viewer->assign('RECORD_ID', $recordId);

        } else {
            $this->recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);

            $this->recordModel->set('interstate_effective_date', date('Y-m-d'));
            $this->recordModel->set('validtill', date('Y-m-d', strtotime('+1 month')));
            $effDate = $request->get('effective_date');
            if(!$effDate)
            {
                $effDate = date('Y-m-d');
            }
            $this->recordModel->set('effective_date', $effDate);

            $sourceRecord = $request->get('sourceRecord');
            if($sourceRecord && $request->get('relationOperation'))
            {
                $this->processRelationOperation($request, $viewer, $sourceRecord);
            }
        }

        $tariffID = $request->get('effective_tariff_id');
        if(!$tariffID)
        {
            $tariffID = $this->recordModel->getCurrentAssignedTariff() ?: $this->recordModel->get('effective_tariff');
            // SIRVA defaults the effective_tariff to TPG or Pricelock when applicable
            if(!$tariffID && getenv('INSTANCE_NAME') == 'sirva'&& !$recordId)
            {
                // default agent
                if(!$this->recordModel->get('agentid')) {
                    $defaultAgent = array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser())[0];
                    $this->recordModel->set('agentid', $defaultAgent);
                }
                $brand = AgentManager_GetBrand_Action::retrieve($this->recordModel->get('agentid'));
                if($brand == 'AVL')
                {
                    $res = $db->pquery('SELECT tariffmanagerid FROM vtiger_tariffmanager WHERE custom_tariff_type=? LIMIT 1', ['TPG']);
                } else if($brand == 'NVL') {
                    $res = $db->pquery('SELECT tariffmanagerid FROM vtiger_tariffmanager WHERE custom_tariff_type=? LIMIT 1', ['Pricelock']);
                }
                if($res && ($row = $res->fetchRow()))
                {
                    $tariffID = $row['tariffmanagerid'];
                }
            }
            $request->set('effective_tariff_id', $tariffID);
        }
        $tariffInfo = Estimates_Record_Model::getTariffInfo($tariffID);
        $request->set('is_interstate', $tariffInfo['is_interstate']);
        $viewer->assign('EFFECTIVE_TARIFF', $tariffID);
        $viewer->assign('EFFECTIVE_TARIFF_NAME', $tariffInfo['name']);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMTYPE', $tariffInfo['custom_type']);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMJS', $tariffInfo['custom_js']);

        // Get Section Discounts
        $sectionDiscounts = TariffSections_Record_Model::getDiscounts($recordId);
        $viewer->assign('SECTION_DISCOUNTS', $sectionDiscounts);

        $this->tariffInfo = $tariffInfo;

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('dateFormat', $currentUser->get('date_format'));

        // default subject
        // GVL doesn't want this though
        if(getenv('INSTANCE_NAME') != 'graebel' && !$this->recordModel->get('subject')) {
            $firstName   = $currentUser->get('first_name');
            $lastName    = $currentUser->get('last_name');
            $time        = new DateTime('now', new DateTimeZone($currentUser->time_zone));
            $subjectName = strtoupper(substr($firstName, 0, 1).$lastName)." ".$time->format('Y-m-d H:i:s');
            $this->recordModel->set('subject', $subjectName);
        }

        $this->assignVarsForGVL($request, $viewer);
        $this->assignVarsForSIRVA($request, $viewer);
        $this->assignVarsForOthers($request, $viewer);
        $this->assignVarsFromENV($request, $viewer);

        $recordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($this->recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $structuredValues = $recordStructure->getStructure();
        $moduleModel      = $this->recordModel->getModule();

        $viewer->assign('MODULE_NAME', $request->get('module'));
        $viewer->assign('MODULE', $request->get('module'));
        $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
        $viewer->assign('RECORD', $this->recordModel);
        $viewer->assign('RECORD_ID', $this->recordId);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructure);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', $currentUser);
        $viewer->assign('IS_EDIT_VIEW', $request->isEditView());

        if($this->recordModel->get('is_primary') == '1') {
            $viewer->assign('LOCK_PRIMARY_FIELD', true);
        }

        $guestRecord = $recordId ?: $sourceRecord;
        $this->setViewerForGuestBlocks($moduleName, $guestRecord, $viewer);

        $viewer->assign('DEFUALT_TO_HIDDEN', true);
        return true;
    }

    protected function assignVarsFromENV(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        if (getenv('DEFAULT_IRR')) {
            $irr_charge = $this->recordModel->get('irr_charge');
            if (!$irr_charge) {
                $this->recordModel->set('irr_charge', getenv('DEFAULT_IRR'));
            }
        }
    }

    protected function assignVarsForGVL(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        if(getenv('INSTANCE_NAME') != 'graebel')
        {
            return;
        }
        $recordModel = $this->recordModel;
        if ($recordModel->get('sit_origin_date_in')) {
            $recordModel->set('sit_origin_auth_no', 'O'.$this->recordId);
        } else {
            $recordModel->set('sit_origin_auth_no', '');
        }
        if ($recordModel->get('sit_dest_date_in')) {
            $recordModel->set('sit_dest_auth_no', 'D'.$this->recordId);
        } else {
            $recordModel->set('sit_dest_auth_no', '');
        }
        if ($recordModel->get('is_primary') && $recordModel->get('quotestage') == 'Accepted') {
            //And now we wait for the change request to improve this Feature.
            $viewer->assign('LOCK_ESTIMATE', true);
        }
        if ($recordModel->get('orders_id')) {
            try {
                $ordersRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('orders_id'), 'Orders');
                $this->ordersRecordModel = $ordersRecordModel;
                if ($ordersRecordModel->isLocked()) {
                    $viewer->assign('LOCK_ESTIMATE', true);
                    $viewer->assign('LOCK_RATING', true);
                }
            } catch (Exception $e) {
            }
        }
    }

    protected function assignVarsForSIRVA(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        if(getenv('INSTANCE_NAME') != 'sirva')
        {
            return;
        }
        if($request->get('is_interstate') != 1) {
          $viewer->assign('SHOW_TRANSIT_GUIDE', false);
        } else {
          $viewer->assign('SHOW_TRANSIT_GUIDE', true);
        }
        // TODO: not sure if this is the right place for all this
        $recordModel = $this->recordModel;
        if(!$this->recordId)
        {
            // default agent
            if(!$recordModel->get('agentid')) {
                $defaultAgent = array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser())[0];
                $recordModel->set('agentid', $defaultAgent);
            }
            if(!$request->get('relationOperation')) {
                //default business line
                $recordModel->set('business_line_est', 'Interstate Move');
            }

            // default valuation type
            $recordModel->set('valuation_deductible', 'FVP - $0');
            $recordModel->set('weight', 0);
        }
        //logic to include Address Segments
        $AddressSegmentsModel = Vtiger_Module_Model::getInstance('AddressSegments');
        if ($AddressSegmentsModel && $AddressSegmentsModel->isActive()) {
            global $adb;
            $FromLocationType = [
                'Extra Pickup 1' => '3',
                'Extra Pickup 2' => '4',
                'Extra Pickup 3' => '5',
                'Extra Pickup 4' => '6',
                'Extra Pickup 5' => '7',
                'O - SIT'        => '8',
                'Self Stg PU'    => '9',
                'Perm PU'        => '10',
                'Origin'         => '1'
            ];
            $ToLocationType   = [
                'Extra Delivery 1' => '3',
                'Extra Delivery 2' => '4',
                'Extra Delivery 3' => '5',
                'Extra Delivery 4' => '6',
                'Extra Delivery 5' => '7',
                'D - SIT'          => '8',
                'Perm Dlv'         => '9',
                'Self Stg Dlv'     => '10',
                'Destination'      => '2'
            ];
            $SegmentType      = [
                '0' => 'Road',
                '1' => 'Air',
                '2' => 'Perm',
                '3' => 'Sea',
            ];
            if ($recordModel) {
                $potential_id  = $recordModel->get('potential_id');
            } else {
                $potential_id = $request->get('potential_id');
            }
            $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
            if ($extraStopsModel && $extraStopsModel->isActive()) {
                $extraStopLocationType = ['Origin', 'Destination'];
                $extraStops            = $extraStopsModel->getStops($potential_id);
                foreach ($extraStops as $extraStop) {
                    $extrastops_type = $extraStop['extrastops_type'];
                    if (!in_array($extrastops_type, $extraStopLocationType)) {
                        $extraStopLocationType[] = $extrastops_type;
                    }
                }
            }
            $viewer->assign('ADDRESSSEGMENTS_MODULE_MODEL', $AddressSegmentsModel);
            $viewer->assign('ADDRESSSEGMENTS_BLOCK_FIELDS', $AddressSegmentsModel->getFields('LBL_ADDRESSSEGMENTS_INFORMATION'));
            $addresssegments_list = $AddressSegmentsModel->getAddressSegments($this->recordId);
            if (count($addresssegments_list) > 0) {
                $viewer->assign('ADDRESSSEGMENTS_LIST', $addresssegments_list);
            } else {
                if ($this->recordId == '' && $request->get('potential_id') == '') {
                    $addresssegments_list[] = [
                        'addresssegmentsid'              => 'none',
                        'addresssegments_sequence'       => '1',
                        'addresssegments_origin'         => 'Origin',
                        'addresssegments_destination'    => 'Destination',
                        'addresssegments_transportation' => 'Road',
                        'addresssegments_cube'           => '',
                        'addresssegments_weight'         => '',
                        'addresssegments_weightoverride' => '',
                        'addresssegments_cubeoverride'   => '',
                    ];
                } else {
                    // Get cubesheet id
                    $rsCubesheet = $adb->pquery("SELECT cubesheetsid FROM vtiger_cubesheets
INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_cubesheets.cubesheetsid
WHERE vtiger_crmentity.deleted=0 AND potential_id=?",
                                                [$potential_id]);
                    if ($adb->num_rows($rsCubesheet) > 0) {
                        $cubesheet_id = $adb->query_result($rsCubesheet, 0, 'cubesheetsid');
                    }
                    $addresssegments_list = [];
                    if ($cubesheet_id) {
                        $cubeSheetDataArray = $AddressSegmentsModel->getCubeSheetData($cubesheet_id);
                        $cubeSheetDataArray = $cubeSheetDataArray['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'];
                        if (count($cubeSheetDataArray) > 0) {
                            if (count($extraStops) > 0) {
                                $i = 1;
                                foreach ($cubeSheetDataArray as $k => $cubeSheetData) {
                                    $addresssegments_list[] = [
                                        'addresssegmentsid'              => 'none',
                                        'addresssegments_sequence'       => $i,
                                        'addresssegments_origin'         => 'Origin',
                                        'addresssegments_destination'    => 'Destination',
                                        'addresssegments_transportation' => $i == 1?'Road':$SegmentType[$cubeSheetData['SegmentType']],
                                        'addresssegments_cube'           => $cubeSheetData['TotalCube'],
                                        'addresssegments_weight'         => $cubeSheetData['TotalWeight'],
                                        'addresssegments_weightoverride' => '',
                                        'addresssegments_cubeoverride'   => '',
                                    ];
                                    $i++;
                                }
                            }
                        }
                    }
                    if (count($addresssegments_list) == 0) {
                        $addresssegments_list[] = [
                            'addresssegmentsid'              => 'none',
                            'addresssegments_sequence'       => '1',
                            'addresssegments_origin'         => 'Origin',
                            'addresssegments_destination'    => 'Destination',
                            'addresssegments_transportation' => 'Road',
                            'addresssegments_cube'           => '',
                            'addresssegments_weight'         => '',
                            'addresssegments_weightoverride' => '',
                            'addresssegments_cubeoverride'   => '',
                        ];
                    }
                }
                $viewer->assign('ADDRESSSEGMENTS_LIST', $addresssegments_list);
            }
            $viewer->assign('FROMLOCATIONTYPE', array_keys($FromLocationType));
            $viewer->assign('TOLOCATIONTYPE', array_keys($ToLocationType));
            $viewer->assign('EXTRASTOP_LOCATIONTYPE', $extraStopLocationType);
        }
    }

    protected function assignVarsForOthers(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $recordModel = $this->recordModel;
        if ($recordModel->get('bottom_line_discount') == '') {
            $recordModel->set('bottom_line_discount', 0);
        }
        $hdnGrandTotal = $recordModel->get('hdnGrandTotal');
        $viewer->assign('TOTAL_VALUE', $hdnGrandTotal);
        if(getenv('INSTANCE_NAME') == 'graebel' || getenv('INSTANCE_NAME') == 'sirva')
        {
            return;
        }
        if ($recordModel->get('is_primary') && $recordModel->get('quotestage') == 'Accepted') {
            //And now we wait for the change request to improve this Feature.
            $viewer->assign('LOCK_ESTIMATE', true);
        }
        $viewer->assign('MOVE_TYPE', $request->get('move_type'));

        $paths = new stdClass;
        //@TODO: er?
        $paths->root = realpath(__DIR__ . '/../../../');
        $paths->templates = realpath("{$paths->root}/layouts/vlayout/modules/Estimates");
        $paths->smartyPlugins = realpath("{$paths->templates}/plugins");
        if (file_exists($paths->smartyPlugins) && is_dir($paths->smartyPlugins)) {
            $viewer->addPluginsDir(realpath("{$paths->templates}/plugins"));
        }

        //$viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        $viewer->assign('BUSINESS_LINE', $recordModel->get('business_line_est'));

        //$viewer->assign('PRIMARY_ESTIMATES', $this->primaryEstimateCheck($recordModel->get('potential_id')));
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,
                                                                                            Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $structuredValues = $recordStructureInstance->getStructure();
        if($this->record) {
            $this->convertSurveyTimeFormat($structuredValues);
        }
        $viewer->assign('VIEW_MODE', "fullForm");
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', 1);
        }
    }

    protected function processRelationOperation(Vtiger_Request $request, Vtiger_Viewer $viewer, $sourceRecord)
    {
        global $adb;
        $sourceModule = $request->get('sourceModule');
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $current_user = Users_Record_Model::getCurrentUserModel();
        if($sourceModule == 'Orders')
        {
            $this->recordModel->set('orders_id', $sourceRecord);
        }
        $parentRecord = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
        $this->recordModel->setParentRecordData($parentRecord);
        $contact_id = $this->recordModel->get('contact_id');
        if ($contact_id) {
            $contactsRecord = Vtiger_Record_Model::getInstanceById($contact_id, 'Contacts');
            $this->recordModel->setParentRecordData($contactsRecord);
                $firstName   = $contactsRecord->get('firstname');
                $lastName    = $contactsRecord->get('lastname');
            if (getenv('INSTANCE_NAME') != 'graebel') {
                $time        = new DateTime('now', new DateTimeZone($current_user->time_zone));
                $subjectName = strtoupper(substr($firstName, 0, 1).$lastName)." ".$time->format('Y-m-d H:i:s');
                $this->recordModel->set('subject', $subjectName);
            }
        }
        $account_id = $this->recordModel->get('account_id');
        if ($accountID && !in_array($recordModel->get('shipper_type'), ['MIL', 'GVT'])) {
            if(getenv('INSTANCE_NAME') != 'graebel') {
                $accountRecord = Vtiger_Record_Model::getInstanceById($account_id, 'Accounts');
                $this->recordModel->setParentRecordData($accountRecord);
                $this->recordModel->set('shipper_type', 'NAT');
            }
        }
        if($sourceModule == 'Opportunities' || $sourceModule == 'Orders') {
            $relationField = $sourceModule == 'Opportunities' ? 'potentialid' : 'orders_id';
            $sql = "SELECT COUNT(quoteid) as numRelated FROM `vtiger_quotes` WHERE $relationField=? AND is_primary = 1";
            $result = $adb->pquery($sql, [$sourceRecord]);
            if($result && $result->fields['numRelated'] == 0) {
                $this->recordModel->set('is_primary', '1');
            } elseif ($result) {
                $viewer->assign('PARENT_HAS_PRIMARY', true);
            }
        }
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        // TODO: fix this
        $headerScriptInstances = parent::getHeaderScripts($request);
        //Added to remove the module specific js, as they depend on inventory files
        unset($headerScriptInstances['modules.Quotes.resources.Edit']);
        unset($headerScriptInstances['modules.Estimates.resources.Edit']);
        unset($headerScriptInstances['modules.Estimates.resources.Detail']);
        $jsFileNames   = [
            'modules.Inventory.resources.Popup',
            'modules.Inventory.resources.Edit',
            'modules.Quotes.resources.Edit',
            'modules.Estimates.resources.Edit',
            '~/libraries/jquery/colorbox/jquery.colorbox-min.js'
        ];

        $jsFileNames[] = 'modules.Estimates.resources.BaseTariff';
        $jsFileNames[] = 'modules.Vtiger.resources.MoveType';
        $jsFileNames[] = 'modules.Estimates.resources.ServiceCharges';
        $jsFileNames[] = 'modules.Vtiger.resources.DaysToMove';
        $jsFileNames[] = 'modules.Valuation.resources.Local';
        $jsFileNames[] = 'modules.Estimates.resources.SIT';
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = 'modules.Estimates.resources.BaseSIRVA';
            $jsFileNames[] = 'modules.Estimates.resources.TPGTariff';
            $jsFileNames[] = 'modules.Valuation.resources.SIRVA';
            $jsFileNames[] = 'modules.Opportunities.resources.MilitaryFields';
        }

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/libraries/jquery/colorbox/example1/colorbox.css'
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    public function reloadContents(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $this->assignDefaultVars($request, $viewer);
        $this->viewBlocks($request);
    }

}
