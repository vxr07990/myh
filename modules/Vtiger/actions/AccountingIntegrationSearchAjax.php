<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 4:49 PM
 */
class Vtiger_AccountingIntegrationSearchAjax_Action extends Vtiger_Action_Controller
{
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
        $result = [];
        $src_module = $request->get('search_module');
        $searchKey = $request->get('search_key') ?: 'label';
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
        foreach($result['entries'] as $rec)
        {
            $rec['value'] = $rec['label'];
            $result[] = $rec;
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}