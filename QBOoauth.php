<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/17/2017
 * Time: 12:13 PM
 */

require_once('libraries/MoveCrm/AccountingIntegration.php');

session_start();
$userID = $_SESSION['AUTHUSERID'];
$agentID = $_SESSION['temp_agentid']?:$_REQUEST['agentid'];
$vanlineID = $_SESSION['temp_vanlineid']?:$_REQUEST['vanlineid'];
if($_SESSION['temp_agentid'])
{
    $_SESSION['temp_agentid'] = '';
}
if($_SESSION['temp_vanlineid'])
{
    $_SESSION['temp_vanlineid'] = '';
}

$integration = new \MoveCrm\AccountingIntegration();
$db = &PearDatabase::getInstance();

if(!$userID)
{
    $integration->log('Attempt to access QBOoauth without an authorized user id');
    return;
}

$currentUser = Users_Record_Model::getCurrentUserModel();

if(!$agentID && !$vanlineID)
{
    $integration->log('Attempt to access QBOoauth without specifying an agent or vanline');
    return;
}

if($agentID && !$currentUser->canAccessAgent($agentID))
{
    $integration->log('Attempt to access QBOoauth without access to the specified agent');
    return;
}

if($vanlineID && !$currentUser->canAccessVanline($vanlineID))
{
    $integration->log('Attempt to access QBOoauth without access to the specified vanline');
    return;
}

if($integration->isConnected($agentID ?: $vanlineID))
{
    $integration->log('Attempt to access QBOoauth for an agent/vanline that is already connected');
    echo 'This agent/vanline is already connected';
    return;
}

$appToken = getenv('QUICKBOOKS_APPTOKEN');
$consumerKey = getenv('QUICKBOOKS_CONSUMERKEY');
$consumerSecret = getenv('QUICKBOOKS_CONSUMERSECRET');

// Have to use GET for quickbooks
$oauth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
$oauth->enableDebug();

if(!isset($_REQUEST['oauth_verifier'])) {
    global $site_URL;
    $siteURL      = $site_URL
        ? :
        (
            ($_SERVER['HTTPS'] === 'on'?'https://':'http://')
            .$_SERVER['HTTP_HOST']
        );
    $callbackURL  = $siteURL.'/QBOoauth.php';
    $requestToken = $oauth->getRequestToken(getenv('QUICKBOOKS_REQUESTURL'), $callbackURL);
    if ($requestToken['oauth_callback_confirmed']) {
        $_SESSION['temp_agentid'] = $_REQUEST['agentid'];
        $_SESSION['temp_vanlineid'] = $_REQUEST['vanlineid'];
        $_SESSION['temp_oauth_token'] = $requestToken['oauth_token'];
        $_SESSION['temp_oauth_token_secret'] = $requestToken['oauth_token_secret'];
        header('Location: '.getenv('QUICKBOOKS_CONNECTURL').'?oauth_token='.$requestToken['oauth_token']);
    }
} else {
    $oauth->setToken($_REQUEST['oauth_token'], $_SESSION['temp_oauth_token_secret']);
    $accessToken = $oauth->getAccessToken(getenv('QUICKBOOKS_ACCESSURL'));
    $db->pquery('INSERT INTO vtiger_accountingintegration
                (auth_userid,remote_system,app_token,oauth_consumer_key,oauth_consumer_secret,realmid,oauth_token,oauth_token_secret)
                VALUES (?,?,?,?,?,?,?,?)',
                [
                    $userID,
                    'QBO',
                    $integration->encrypt($appToken),
                    $integration->encrypt($consumerKey),
                    $integration->encrypt($consumerSecret),
                    $integration->encrypt($_REQUEST['realmId']),
                    $integration->encrypt($accessToken['oauth_token']),
                    $integration->encrypt($accessToken['oauth_token_secret']),
                ]);
    $id = $db->getLastInsertID();
    if($agentID) {
        $db->pquery('INSERT INTO vtiger_accountingintegration_agents (id,agentid)
                   VALUES (?,?)',
                    [$id, $agentID]);
    } else {
        $db->pquery('INSERT INTO vtiger_accountingintegration_vanlines (id,vanlineid)
                   VALUES (?,?)',
                    [$id, $vanlineID]);
    }
    echo '<script>localStorage.setItem(\'moveCRMmessage\',JSON.stringify({\'command\':\'remove_qbo_connect\'}));
		localStorage.removeItem(\'moveCRMmessage\'); window.close();</script>';
}