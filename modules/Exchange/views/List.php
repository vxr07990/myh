<?php
use MoveCrm\Models\User;
use Igc\Ews\Synchronization\Local;
use Igc\Ews\Synchronization\PushUp;
use Igc\Ews\Synchronization\PullDown;
use Igc\Ews\Calendar as ExchangeCalendar;
use PhpEws\Exception\EwsException;

require_once 'include/Webservices/Create.php';

class Exchange_List_View extends Vtiger_PopupAjax_View
{
    public function __construct()
    {
        $this->exposeMethod('Calendar');
    }

    /**
     * The primary runtime method for this class.
     *
     * @param  Vtiger_Request $request
     *
     * @return void
     */
    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/devLog.log', "\n Php_sapi_name() : ".print_r(php_sapi_name(), true), FILE_APPEND);
        $db   = PearDatabase::getInstance();
        $user = User::current();
        $running = $this->checkSync($db,$user);
        // Switch on the operation url parameter as to whether
        // we're showing the blank sync widget or the detail results
        switch ($request->get('operation')) {
            case 'sync':
                if($running) {
                    return;
                }
                $this->renderSyncUI($request, $db, $user);
                break;
            case 'removeSync':
                if($running) {
                    return;
                }
                if ($request->validateWriteAccess()) {
                    $this->deleteSync($request);
                }
                break;
            default:
                $this->renderWidgetUI($request, $db, $user);
        }
    }

    /**
     * Show the widget UI with a Synchronize button
     *
     * @param Vtiger_Request $request
     * @param PearDatabase   $db
     * @param                $user
     */
    protected function renderWidgetUI(Vtiger_Request $request, PearDatabase $db, $user)
    {
        file_put_contents('logs/devLog.log', "\n Request : ".print_r($request, true), FILE_APPEND);
        $userId = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $lastSync  = null;
        $sync      = new MoveCrm\WebServices\Exchange\Sync($userId);
        $firstTime = $sync->isFirst();
        $viewer    = $this->getViewer($request);
        if (!$firstTime) {
            $lastSync = $sync->lastOccurred()->diffForHumans();
        }
        // Assign the view variables.
        $viewer->assign('MODULE_NAME', $request->getModule());
        $viewer->assign('FIRSTTIME', $firstTime);
        $viewer->assign('STATE', 'home');
        $viewer->assign('SYNCTIME', $lastSync);
        $viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        // Render the view.
        $viewer->view('Contents.tpl', $request->getModule());
    }

    /**
     * Show the widget UI with a sync button as well as
     * the details from the just-completed synchronization
     *
     * @param Vtiger_Request $request
     *
     * @return bool
     * @throws Exception
     */
    protected function renderSyncUI(Vtiger_Request $request)
    {
        $db   = PearDatabase::getInstance();
        file_put_contents('logs/devLog.log', "\n".print_r("Entering renderSyncUI", true), FILE_APPEND);
        logCLI('Entering renderSyncUI');
        logCLI('Request in renderSyncUI: '.print_r($request, true));
        $userId        = $request->get('current_user_id');
        if (empty($userId)) {
          $user = Users_Record_Model::getCurrentUserModel();
          $userId = $user->getId();
        } else {
          $user = Users_Record_Model::getInstanceById($userId,'Users');
        }

        $pid = getmypid();
        if($this->checkSync($db,$user)){
            return;
        }
        //file_put_contents('logs/devLog.log', "\n UserId1 : ".print_r($userId, true), FILE_APPEND);
        logCLI('UserId in renderSyncUI: '.$userId);
        $sync          = new MoveCrm\WebServices\Exchange\Sync($userId);
        $firstTime     = $sync->isFirst();
        $alreadyForked = $request->get('forked');
        //file_put_contents('logs/devLog.log', "\n Exchange renderSyncUI Request : ".print_r($request, true), FILE_APPEND);
        if ($firstTime && !$alreadyForked) {
            logCLI('Not forked yet - forking');
            //Initial sync - fork process and run in the background
            $cliString = 'nohup php -f exchangeHelper.php module=Exchange view=List operation=sync sourcemodule='.$request->get('sourcemodule').' forked=1 current_user_id='.$userId.' >logs/exchangeOutput'.$userId.'.log 2> logs/exchangeErrors'.$userId.'.log &';
            $cliString = str_replace('\\', '/', $cliString);
            file_put_contents('logs/devLog.log', "\n CliString : ".print_r($cliString, true), FILE_APPEND);

            shell_exec($cliString);

            //file_put_contents('logs/devLog.log', "\n Shell_exec : ".shell_exec($cliString)."\n", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n After shell_exec command \n", FILE_APPEND);
            $viewer       = $this->getViewer($request);
            die($viewer->view('BackgroundSync.tpl', $request->getModule(), true));
        } elseif ($alreadyForked) {
            file_put_contents('logs/devLog.log', "\n ALREADY FORKED!\n", FILE_APPEND);
            $db->pquery("INSERT INTO exchange_pid VALUES('',?,?,?)",[$pid,$userId,date("Y-m-d H:i:s")]);
            logCLI('Forked - Updating global current_user_id value');
            $GLOBALS['current_user_id'] = $userId;
        }
        $sourceModule = $request->get('sourcemodule');
        $viewer       = $this->getViewer($request);
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        if (!empty($sourceModule)) {
            try {
                logCLI('Preparing to call '.$sourceModule.'() function in renderSyncUI');
                logCLI('Request before '.$sourceModule.'() call : '.print_r($request, true));
                $records = $this->invokeExposedMethod($sourceModule);
                logCLI('Request after '.$sourceModule.'() call : '.print_r($request, true));
                $db = PearDatabase::getInstance();
                $db->pquery("DELETE FROM exchange_pid WHERE pid = ? AND user_id = ?",[$pid, $userId]);
                logCLI($sourceModule.'() function completed in renderSyncUI without throwing exception');
            } catch (EwsException $e) {
                file_put_contents('logs/devLog.log', "\n Request on failed try : ".print_r($request, true), FILE_APPEND);
                $db->pquery("DELETE FROM exchange_pid WHERE pid = ? AND user_id = ?",[$pid, $userId]);
                logCLI($sourceModule.'() threw exception containing message : '.$e->getMessage());
                $errorCode = $e->getCode();
                if (php_sapi_name() != 'cli') {
                    if ($errorCode == 401) {
                        //$this->removeSynchronization($request);
                        $response = new Vtiger_Response();
                        $response->setError(401, 'JS_LBL_ERROR_UNAUTHORIZED');
                        $response->emit();

                        return false;
                    } else {
                        $response = new Vtiger_Response();
                        $response->setError(999, $e->getMessage());
                        $response->emit();

                        return false;
                    }
                } else {
                    if ($errorCode == 401) {
                        $GLOBALS['exchange_error_code'] = 401;
                        $GLOBALS['exchange_error_message'] = 'LBL_ERROR_UNAUTHORIZED';
                    } else {
                        $GLOBALS['exchange_error_code'] = 999;
                        $GLOBALS['exchange_error_message'] = $e->getMessage();
                    }
                    return false;
                }
            }
        }
        $db->pquery("DELETE FROM exchange_pid WHERE pid = ? AND user_id = ?",[$pid, $userId]);
        if (php_sapi_name() == 'cli') {
            $GLOBALS['record_sync'] = $records;
            return;
        }
        logCLI('Preparing to assign SMARTY variables in renderSyncUI');
        $sync          = new MoveCrm\WebServices\Exchange\Sync($userId);
        $viewer->assign('MODULE_NAME', $request->getModule());
        $viewer->assign('FIRSTTIME', $firstTime);
        $viewer->assign('RECORDS', $records);
        $viewer->assign('NORECORDS', $this->noRecords);
        $viewer->assign('SYNCTIME', $sync->lastOccurred()->diffForHumans());
        $viewer->assign('STATE', $request->get('operation'));
        $viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
        logCLI('SMARTY variable assignment complete');
        if ($firstTime) {
            $viewer->view('Contents.tpl', $request->getModule());
        } else {
            echo $viewer->view('ContentDetails.tpl', $request->getModule(), true);
        }
    }

    /**
     * This is the method called when the user clicks on the sync button
     * @return array
     */
    public function Calendar()
    {
        file_put_contents('logs/devLog.log', "\n (".__FILE__.":" . __LINE__ . ") -- Entering Calendar() inside of Exchange_List_View\n", FILE_APPEND);
        logCLI('Entering Calendar() function in Exchange_List_View');
        $userId        = $GLOBALS['current_user_id'];
        if (empty($userId)) {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
        }
        $user = Users_Record_Model::getInstanceById($userId, 'Users');
        // pull down and persist changes from remote master

        logCLI('Preparing to pull down from Exchange');
        $changes = PullDown::remoteChanges($user);
        logCLI('Completed pull down from Exchange');
        logCLI('$changes after PullDown::remoteChanges : '.print_r($changes, true));

        // grab any local changes that haven't been pushed to remote
        logCLI('Preparing to lookup local creates');
        $localCreates = Local::getCreates($user);
        logCLI('Completed looking up local creates.');
        logCLI('$changes after Local::getCreates : '.print_r($changes, true));
        logCLI('Preparing to lookup local updates');
        $localUpdates = Local::getUpdates($user);
        logCLI('Completed looking up local updates');
        logCLI('$changes after Local::getUpdates : '.print_r($changes, true));
        logCLI('Preparing to lookup local deletes');
        $localDeletes = Local::getDeletes($user);
        logCLI('Completed looking up local deletes');
        logCLI('$changes after Local::getDeletes : '.print_r($changes, true));
        // push new local changes to remote
        logCLI('Preparing to push local creates to Exchange');
        $changes = $this->pushUpCreates($user, $localCreates, $changes);
        logCLI('Completed pushing local creates to Exchange');
        logCLI('$changes after pushUpCreates : '.print_r($changes, true));
        logCLI('Preparing to push local updates to Exchange');
        $changes = $this->pushUpUpdates($user, $localUpdates, $changes);
        logCLI('Completed pushing local updates to Exchange');
        logCLI('$changes after pushUpUpdates : '.print_r($changes, true));
        logCLI('Preparing to push local deletes to Exchange');
        $changes = $this->pushUpDeletes($user, $localDeletes, $changes);
        logCLI('Completed pushing local deletes to Exchange');
        logCLI('$changes after pushUpDeletes : '.print_r($changes, true));

        logCLI('Exiting Calendar() function in Exchange_List_View with $changes = '.print_r($changes, true));
        return $changes;
    }

    /**
     * Push up any new local events
     *
     * @param $user
     * @param $localCreates
     * @param $changes
     *
     * @return mixed
     */
    private function pushUpCreates($user, $localCreates, $changes)
    {
        foreach ($localCreates as $event) {
            $ret = PushUp::newLocalEvent($user, $event);
            if ($ret != null) {
                $changes['exchange']['create']++;
            }
        }

        return $changes;
    }

    /**
     * Push up any new local updates
     *
     * @param $user
     * @param $localUpdates
     * @param $changes
     *
     * @return mixed
     */
    private function pushUpUpdates($user, $localUpdates, $changes)
    {
        foreach ($localUpdates as $event) {
            PushUp::updatedLocalEvent($user, $event);
            $changes['exchange']['update']++;
        }

        return $changes;
    }

    /**
     * Push up any new local deletes
     *
     * @param $user
     * @param $localDeletes
     * @param $changes
     *
     * @return mixed
     */
    private function pushUpDeletes($user, $localDeletes, $changes)
    {
        foreach ($localDeletes as $event) {
            PushUp::deletedLocalEvent($user, $event);
            $changes['exchange']['delete']++;
        }

        return $changes;
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return array <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $scripts    = ['~libraries/bootstrap/js/bootstrap-popover.js', "modules.{$moduleName}.resources.List"];

        return $this->checkAndConvertJsScripts($scripts);
    }

    /**
     * Function to check if a sync for this user is already running
     * then check the run duration. If the duration is over <time>
     * end the previous pid, and begin new sync.
     *
     * @param PearDatabase $db
     * @param Vtiger_User $user
     * @return boolean
     */
    private function checkSync($db,$user) {
        $running = false;
        $result = $db->pquery('SELECT * FROM exchange_pid WHERE user_id = ?', [$user->id]);

        if ($result->numRows() != 0) {
            $sync = $result->fetchRow();
            $runtime = time() - strtotime($sync['start_time']);
            if ($runtime >= 14400 ) {
                if (posix_kill($sync['pid'],0)) {
                    $db->pquery('DELETE FROM exchange_pid WHERE id = ?',[$sync['id']]);
                }
            } else {
                $running = true;
            }
        }

        return $running;
    }
}
