<?php
use \Dropbox as dbx;

class Users_DropboxHook_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        if (isset($_GET['challenge'])) {
            echo $_GET['challenge'];
            return;
        }
        
        if (isset($_SERVER['HTTP_X_DROPBOX_SIGNATURE'])) {
            if ($_SERVER['HTTP_X_DROPBOX_SIGNATURE'] != hash_hmac('sha256', $_POST, "bafwkfvfhnhw2um")) {
                die("Incorrect signature detected. Ending script.");
            }
        }
/*		else {
            die("No signature detected. Ending script.");
        }
*/

        if (isset($_POST) && !empty($_POST)) {
            print_r($_POST);
        }
        $accessToken = $this->getAccessToken();
        
        echo $accessToken;
        
        $dbxClient = new dbx\Client($accessToken, "reloCRM");
        
        print_r($dbxClient->getAccountInfo());
        
        echo "<br /> <br />";
        
        print_r($_SERVER);
    }
    
    public function getAccessToken()
    {
        $db = PearDatabase::getInstance();
        $userId = vglobal('current_user')->id;
        
        $sql = "SELECT dbx_token FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;
        
        $result = $db->pquery($sql, $params);
        
        return $db->query_result($result, 0, 'dbx_token');
    }
}
