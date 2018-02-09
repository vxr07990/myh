<?php
use \Dropbox as dbx;

class Users_GetDropboxAuth_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $method = $request->get('method');
        if ($method == 0) {
            $userId = vglobal('current_user')->id;
            $appInfo = dbx\AppInfo::loadFromJsonFile("libraries/Dropbox/appInfo.json");
            $_SESSION['webAuth'] = new dbx\WebAuthNoRedirect($appInfo, "reloCRM");
            
            $info['url'] = $_SESSION['webAuth']->start();
            
            $response = new Vtiger_Response();
            $response->setResult($info);
            $response->emit();
        } elseif ($method == 1) {
            $userId = vglobal('current_user')->id;
            
            $authCode = $request->get('authCode');
            
            try {
                list($accessToken, $dropboxUserId) = $_SESSION['webAuth']->finish($authCode);
            } catch (dbx\Exception $ex) {
                echo $ex;
            }
            
            $db = PearDatabase::getInstance();
            
            $sql = "UPDATE `vtiger_users` SET `dbx_token`='$accessToken', `dbx_userid`='$dropboxUserId' WHERE id=?";
            $params[] = $userId;
            
            $result = $db->pquery($sql, $params);
            
            unset($_SESSION['webAuth']);
            
            $response = new Vtiger_Response();
            $response->setResult($info);
            $response->emit();
        }
    }
}
