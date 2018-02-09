<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/12/2016
 * Time: 10:15 AM
 */

require_once('ViewHandler.php');

class Estimates_Detail_View extends Quotes_Detail_View
{
    public $recordModel;
    public $viewHandler;
    public $tariffInfo;
    public $recordId;

    public function __construct()
    {
        parent::__construct();
        $this->viewHandler = new Estimates_ViewHandler($this);
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if ($currentUserModel->get('default_record_view') === 'Summary') {
            echo $this->showModuleBasicView($request);
        } else {
            echo $this->showModuleDetailView($request);
        }
    }

    public function showModuleDetailView(Vtiger_Request $request) {
        $this->recordId   = $request->get('record');
        $recordId = $this->recordId;
        $moduleName = $request->getModule();
        $viewer           = $this->getViewer($request);
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $this->recordModel    = $this->record->getRecord();
        $tariffID = $this->recordModel->getCurrentAssignedTariff();
        $request->set('effective_tariff_id', $tariffID);
        $tariffInfo = Estimates_Record_Model::getTariffInfo($tariffID);
        $request->set('effective_tariff', $tariffInfo['name']);
        $this->recordModel->set('effective_tariff', $tariffInfo['name']);
        $request->set('is_interstate', $tariffInfo['is_interstate']);
        $viewer->assign('EFFECTIVE_TARIFF', $tariffID);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMTYPE', $tariffInfo['custom_type']);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMJS', $tariffInfo['custom_js']);
        $this->tariffInfo = $tariffInfo;

        $this->assignVarsForGVL($request, $viewer);
        $this->assignVarsForSIRVA($request, $viewer);

        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($this->recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $this->recordModel->getModule();
        $viewer->assign('MODULE_NAME', $request->get('module'));
        $viewer->assign('MODULE', $request->get('module'));
        $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
        $viewer->assign('RECORD', $this->recordModel);
        $viewer->assign('RECORD_ID', $this->recordId);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($this->recordModel));
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);

        // Get Section Discounts
        $sectionDiscounts = TariffSections_Record_Model::getDiscounts($recordId);
        $viewer->assign('SECTION_DISCOUNTS', $sectionDiscounts);

        $this->viewFull($request);
    }

    protected function assignVarsForGVL(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        if(getenv('INSTANCE_NAME') != 'graebel')
        {
            return;
        }
        if ($this->recordModel->get('sit_origin_date_in')) {
            $this->recordModel->set('sit_origin_auth_no', 'O'.$this->recordId->id);
        } else {
            $this->recordModel->set('sit_origin_auth_no', '');
        }
        if ($this->recordModel->get('sit_dest_date_in')) {
            $this->recordModel->set('sit_dest_auth_no', 'D'.$this->recordId->id);
        } else {
            $this->recordModel->set('sit_dest_auth_no', '');
        }
    }

    protected function assignVarsForSIRVA(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        if(getenv('INSTANCE_NAME') != 'sirva')
        {
            return;
        }
        $brand = AgentManager_GetBrand_Action::retrieve($this->recordModel->get('agentid'));
        $viewer->assign('CURRENT_BRAND', $brand);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        // TODO: fix this
        $headerScriptInstances = parent::getHeaderScripts($request);
        //Added to remove the module specific js, as they depend on inventory files
        unset($headerScriptInstances['modules.Quotes.resources.Detail']);
        unset($headerScriptInstances['modules.Estimates.resources.Detail']);
        unset($headerScriptInstances['modules.Estimates.resources.Edit']);
        $jsFileNames   = [
            'modules.Inventory.resources.Edit',
            'modules.Quotes.resources.Detail',
            'modules.Estimates.resources.Detail',
            '~/libraries/jquery/colorbox/jquery.colorbox-min.js'
        ];

        $jsFileNames[] = 'modules.Estimates.resources.BaseTariff';
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = 'modules.Estimates.resources.BaseSIRVA';
            $jsFileNames[] = 'modules.Estimates.resources.TPGTariff';
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
}
