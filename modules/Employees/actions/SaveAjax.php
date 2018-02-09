<?php
require_once('modules/Users/CreateUserPrivilegeFile.php');
class Employees_SaveAjax_Action extends Vtiger_SaveAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('saveExchangeCredentials');
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        parent::process($request);
    }

    public function saveExchangeCredentials(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $userModel = Users_Record_Model::getCurrentUserModel();
        $hostname = $request->get('hostname');
        $username = $request->get('username');
        $userId = $request->get('userid');
        $password = bin2hex(openssl_encrypt($request->get('password'), 'AES-128-CBC', getenv('EXCHANGE_SALT'), OPENSSL_RAW_DATA));


        $db = PearDatabase::getInstance();
        $sql = "UPDATE `vtiger_users` SET exchange_hostname=?, exchange_username=?, exchange_password=? WHERE id=?";
        $db->startTransaction();
        $result = $db->pquery($sql, [$hostname, $username, $password, $userId]);
        $db->completeTransaction();

        // Get employee id
        $rsEmployee=$db->pquery("select * from `vtiger_employees` where `userid` = ?", array($userId));
        if ($db->num_rows($rsEmployee)>0) {
            $employeeId = $db->query_result($rsEmployee, 0, 'employeesid');
            if ($employeeId !='') {
                $sql = "UPDATE `vtiger_employeescf` SET user_exchange_hostname=?, user_exchange_username=?, user_exchange_password=? WHERE employeesid=?";
                $db->startTransaction();
                $result = $db->pquery($sql, [$hostname, $username, $password, $employeeId]);
                $db->completeTransaction();
            }
        }

        $hasError = $db->hasFailedTransaction();

        $response = new Vtiger_Response();
        if (!$hasError && $db->getAffectedRowCount($result) <= 0) {
            createUserPrivilegesfile($userModel->getId());
            $response->setResult('JS_CREDENTIALS_UPDATED');
        } else {
            $response->setError('JS_DATABASE_UPDATE_ERROR', 'JS_DATABASE_UPDATE_ERROR');
        }
        $response->emit();
    }
}
