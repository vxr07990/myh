<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 4:51 PM
 */

class Vtiger_AccountingIntegrationPopup_View extends Vtiger_Footer_View {

    protected $integration = null;

    protected function getIntegration(Vtiger_Request $request)
    {
        if(!$this->integration)
        {
            $this->integration = new \MoveCrm\AccountingIntegration($request->get('agentid'));
        }
        return $this->integration;
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        if(!$currentUser->canAccessAgent($request->get('agentid')))
        {
            throw new Exception('Access denied');
        }
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
        $companyLogo    = $companyDetails->getLogo();
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('COMPANY_LOGO', $companyLogo);
        $viewer->view('AccountingIntegrationPopup.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->view('PopupFooter.tpl', $moduleName);
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
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.Popup',
            "modules.$moduleName.resources.Popup",
            'modules.Vtiger.resources.BaseList',
            "modules.$moduleName.resources.BaseList",
            'libraries.jquery.jquery_windowmsg',
            'modules.Vtiger.resources.validator.BaseValidator',
            'modules.Vtiger.resources.validator.FieldValidator',
            "modules.$moduleName.resources.validator.FieldValidator",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {

        $agentId = $request->get('agentid');
        $src_module = $request->get('src_module');
        $parentModule = $request->get('parent_module');
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $pageNumber          = $request->get('page');
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);

        $int = $this->getIntegration($request);
        $int->setSearch($src_module, $pagingModel, $searchKey, $searchValue);
        $result = $int->getResults();
        $viewer->assign('LISTVIEW_HEADERS', $result['headers']);
        $viewer->assign('LISTVIEW_ENTRIES', $result['entries']);
        $totalCount = $result['total_count'];
        $pagingModel->calculatePageRange($result['entries']);

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);

        $pageLimit  = $pagingModel->getPageLimit();
        $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('LISTVIEW_COUNT', $totalCount);
        $viewer->assign('SOURCE_MODULE', $src_module);
        $viewer->assign('PARENT_MODULE', $parentModule);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', count($result['entries']));
        $viewer->assign('AGENTID', $agentId);
        $viewer->assign('SOURCE_FIELD', $request->get('src_field'));
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', 'Vtiger');
    }

    public function getListViewCount(Vtiger_Request $request) {
        $src_module = $request->get('src_module');
        $int = $this->getIntegration($request);
        $int->setSearch($src_module);
        $totalCount = $int->getTotalCount();
        return $totalCount;
    }

    public function getPageCount(Vtiger_Request $request){
        $listViewCount = $this->getListViewCount($request);
        $pagingModel = new Vtiger_Paging_Model();
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $listViewCount / (int) $pageLimit);

        if($pageCount == 0){
            $pageCount = 1;
        }
        $result = array();
        $result['page'] = $pageCount;
        $result['numberOfRecords'] = $listViewCount;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}