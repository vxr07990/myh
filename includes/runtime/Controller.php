<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Abstract Controller Class
 */
abstract class Vtiger_Controller
{
    public function __construct()
    {
    }

    public function loginRequired()
    {
        return true;
    }

    abstract public function getViewer(Vtiger_Request $request);
    abstract public function process(Vtiger_Request $request);
    
    public function validateRequest(Vtiger_Request $request)
    {
    }
    public function preProcess(Vtiger_Request $request)
    {
    }
    public function postProcess(Vtiger_Request $request)
    {
    }

    // Control the exposure of methods to be invoked from client (kind-of RPC)
    protected $exposedMethods = array();

    /**
     * Function that will expose methods for external access
     * @param <String> $name - method name
     */
    protected function exposeMethod($name)
    {
        if (!in_array($name, $this->exposedMethods)) {
            $this->exposedMethods[] = $name;
        }
    }

    /**
     * Function checks if the method is exposed for client usage
     * @param string $name - method name
     * @return boolean
     */
    public function isMethodExposed($name)
    {
        if (in_array($name, $this->exposedMethods)) {
            return true;
        }
        return false;
    }

    /**
     * Function invokes exposed methods for this class
     * @param string $name - method name
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function invokeExposedMethod()
    {
        $parameters = func_get_args();
        $name = array_shift($parameters);
        if (!empty($name) && $this->isMethodExposed($name)) {
            return call_user_func_array(array($this, $name), $parameters);
        }
        throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
    }

    /**
     * Function to get the value of a given property
     * @param <String> $propertyName
     * @return <Object>
     */
    public function get($propertyName)
    {
        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        }
    }

    /**
     * Function to set the value of a given property
     * @param <String> $propertyName
     * @param <Mixed> $value
     * @return <Object>
     */
    public function set($propertyName, $value)
    {
        $this->$propertyName = $value;
        return $this;
    }
}

/**
 * Abstract Action Controller Class
 */
abstract class Vtiger_Action_Controller extends Vtiger_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getViewer(Vtiger_Request $request)
    {
        throw new AppException('Action - implement getViewer - JSONViewer');
    }
    
    public function validateRequest(Vtiger_Request $request)
    {
        return $request->validateReadAccess();
    }

    public function preProcess(Vtiger_Request $request)
    {
        return true;
    }

    protected function preProcessDisplay(Vtiger_Request $request)
    {
    }

    protected function preProcessTplName()
    {
        return false;
    }

    //TODO: need to revisit on this as we are not sure if this is helpful
    /*function preProcessParentTplName(Vtiger_Request $request) {
        return false;
    }*/

    public function postProcess(Vtiger_Request $request)
    {
        return true;
    }
}

class Block_View_Handler
{
    // String: name of module that has the templates
    public $moduleName;
    // Instance of controller, .e.g. Estimates/views/Edit.php
    protected $viewController;
    // String: Label of section -- used to pull a subset of blocks to view
    public $sectionLabel;
    // String: Detail view template name
    public $templateDetail;
    // String: Edit view template name
    public $templateEdit;
    // String: name of function to assign template vars
    public $viewerHandler;
    // String or array of strings: JS path
    public $jsPath;
    // Array of strings
    public $subBlocks = [];

    public function __construct($moduleName, $controller, $label, $templateDetail, $templateEdit, $viewHandler, $jsPath) {
        $this->moduleName = $moduleName;
        $this->viewController = $controller;
        $this->sectionLabel = $label;
        $this->templateDetail = $templateDetail;
        $this->templateEdit = $templateEdit;
        $this->viewerHandler = $viewHandler;
        $this->jsPath = $jsPath;
    }

    public function process(Vtiger_View_Controller $controller, Vtiger_Request $request)
    {
        $viewer = $controller->getViewer($request);
        $viewer->assign('CONTENT_DIV_CLASS',' ');
        $viewer->assign('ALWAYS_SHOW_CONTENT_DIV', 0);
        $this->viewController->{$this->viewerHandler}($request, $this);
        if($this->templateEdit && $request->get('view') == 'Edit')
        {
            $viewer->view($this->templateEdit, $this->moduleName);
        }
        else if ($this->templateDetail && $request->get('view') == 'Detail')
        {
            $viewer->view($this->templateDetail, $this->moduleName);
        } else {
            $viewer->view($this->templateDetail ?: $this->templateEdit, $this->moduleName);
        }
    }

    public function addSubBlocks($data)
    {
        $this->subBlocks = array_merge($this->subBlocks,$data);
        return $this;
    }

    public function addJS($data)
    {
        if($this->jsPath && !is_array($this->jsPath))
        {
            $this->jsPath = [$this->jsPath];
        }
        if(!is_array($data))
        {
            $data = [$data];
        }
        $this->jsPath = array_merge($this->jsPath, $data);
        return $this;
    }
}

// TODO list: INTERESTING

// Stuff I know I have to fix:

// SIRVA
// local bottom line discount

// JS populating contacts details via opp didn't work ? appeared to work on re-test

// GVL
// high priority:
// lock primary and accepted estimate
// fix loading local move contents based on effective date now that it actually works

// super low priority:
// improve ListViewNavigation so that it loads after the initial page load for detail view

// Strange things I noticed:
// O&I Move type doesn't show any move details
// Line items do not appear to duplicate with an estimate (non-GVL)

// Other notes:
// I have removed the use of disabled inputs because they do not propagate events properly.
//  If we still need the look of the disabled chosen selects, we will need to find another way to do this.

/**
 * Abstract View Controller Class
 */
abstract class Vtiger_View_Controller extends Vtiger_Action_Controller
{
    // add blocks in derived constructor
    public $abstractBlocks = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function viewFull(Vtiger_Request $request)
    {
        foreach ($this->abstractBlocks as $block)
        {
            $block->process($this, $request);
        }
    }

    // view a subset of blocks
    public function viewBlocks(Vtiger_Request $request)
    {
        $blockList = explode(',',$request->get('_controllerBlockList'));
        foreach ($this->abstractBlocks as $block)
        {
            if(!in_array($block->sectionLabel, $blockList) && count(array_intersect($block->subBlocks, $blockList))==0)
            {
                continue;
            }
            //@NOTE: block is probably Block_View_Handler
            //file_put_contents('logs/devLog.log', "\n (Controller.php:".__LINE__.") get_class(block) : ".print_r(get_class($block), true), FILE_APPEND);
            $block->process($this, $request);
        }
    }

    public function getViewer(Vtiger_Request $request)
    {
        if (!$this->viewer) {
            global $vtiger_current_version;
            $viewer = new Vtiger_Viewer();
            $viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
            $viewer->assign('VTIGER_VERSION', $vtiger_current_version);
            $viewer->assign('ALWAYS_SHOW_CONTENT_DIV', 1);
            $this->viewer = $viewer;
        }
        return $this->viewer;
    }

    public function getPageTitle(Vtiger_Request $request)
    {
        return vtranslate($request->getModule(), $request->get('module'));
    }

    public function preProcess(Vtiger_Request $request, $display=true)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $viewer->assign('PAGETITLE', $this->getPageTitle($request));
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('SKIN_PATH', Vtiger_Theme::getCurrentUserThemePath());
        $viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
        $viewer->assign('LANGUAGE', $currentUser->get('language'));
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    protected function preProcessTplName(Vtiger_Request $request)
    {
        return 'Header.tpl';
    }

    //Note : To get the right hook for immediate parent in PHP,
    // specially in case of deep hierarchy
    //TODO: Need to revisit this.
    /*function preProcessParentTplName(Vtiger_Request $request) {
        return parent::preProcessTplName($request);
    }*/

    protected function preProcessDisplay(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
        /*if(!$displayed) {
            $tplName = $this->preProcessParentTplName($request);
            if($tplName) {
                $viewer->view($tplName, $request->getModule());
            }
        }*/
    }


    public function postProcess(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('ACTIVITY_REMINDER', $currentUser->getCurrentUserActivityReminderInSeconds());
        $viewer->view('Footer.tpl');
    }

    /**
     * Retrieves headers scripts that need to loaded in the page
     * @param Vtiger_Request $request - request model
     * @return <array> - array of Vtiger_JsScript_Model
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = array();
        $languageHandlerShortName = Vtiger_Language_Handler::getShortLanguageName();
        $fileName = "libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
        if (!file_exists($fileName)) {
            $fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js";
        } else {
            $fileName = "~libraries/jquery/posabsolute-jQuery-Validation-Engine/js/languages/jquery.validationEngine-$languageHandlerShortName.js";
        }
        $jsFileNames = array($fileName);
        $moduleName = $request->getModule();
        $jsFileNames[] = 'modules'.$moduleName.'resources.Popup';
        if($request->get('view')) {
            $jsFileNames[] = 'modules'.$moduleName.'resources.'.$request->get('view');
        }
        foreach($this->abstractBlocks as $block)
        {
            if($block->jsPath)
            {
                if(is_array($block->jsPath))
                {
                    $jsFileNames = array_unique(array_merge($jsFileNames, $block->jsPath));
                }
                else
                {
                    $jsFileNames[] = $block->jsPath;
                }
            }
        }
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($jsScriptInstances, $headerScriptInstances);
        return $headerScriptInstances;
    }

    public function checkAndConvertJsScripts($jsFileNames)
    {
        $fileExtension = 'js';

        $jsScriptInstances = array();
        foreach ($jsFileNames as $jsFileName) {
            // TODO Handle absolute inclusions (~/...) like in checkAndConvertCssStyles
            $jsScript = new Vtiger_JsScript_Model();

            // external javascript source file handling
            if (strpos($jsFileName, 'http://') === 0 || strpos($jsFileName, 'https://') === 0) {
                $jsScriptInstances[$jsFileName] = $jsScript->set('src', $jsFileName);
                continue;
            }

            $completeFilePath = Vtiger_Loader::resolveNameToPath($jsFileName, $fileExtension);

            if (file_exists($completeFilePath)) {
                if (strpos($jsFileName, '~') === 0) {
                    $filePath = ltrim(ltrim($jsFileName, '~'), '/');
                    // if ~~ (reference is outside vtiger6 folder)
                    if (substr_count($jsFileName, "~") == 2) {
                        $filePath = "../" . $filePath;
                    }
                } else {
                    $filePath = str_replace('.', '/', $jsFileName) . '.'.$fileExtension;
                }

                $jsScriptInstances[$jsFileName] = $jsScript->set('src', $filePath);
            } else {
                $fallBackFilePath = Vtiger_Loader::resolveNameToPath(Vtiger_JavaScript::getBaseJavaScriptPath().'/'.$jsFileName, 'js');
                if (file_exists($fallBackFilePath)) {
                    $filePath = str_replace('.', '/', $jsFileName) . '.js';
                    $jsScriptInstances[$jsFileName] = $jsScript->set('src', Vtiger_JavaScript::getFilePath($filePath));
                }
            }
        }
        return $jsScriptInstances;
    }

    /**
     * Function returns the css files
     * @param <Array> $cssFileNames
     * @param <String> $fileExtension
     * @return <Array of Vtiger_CssScript_Model>
     *
     * First check if $cssFileName exists
     * if not, check under layout folder $cssFileName eg:layouts/vlayout/$cssFileName
     */
    public function checkAndConvertCssStyles($cssFileNames, $fileExtension='css')
    {
        $cssStyleInstances = array();
        foreach ($cssFileNames as $cssFileName) {
            $cssScriptModel = new Vtiger_CssScript_Model();

            if (strpos($cssFileName, 'http://') === 0 || strpos($cssFileName, 'https://') === 0) {
                $cssStyleInstances[] = $cssScriptModel->set('href', $cssFileName);
                continue;
            }
            $completeFilePath = Vtiger_Loader::resolveNameToPath($cssFileName, $fileExtension);
            $filePath = null;
            if (file_exists($completeFilePath)) {
                if (strpos($cssFileName, '~') === 0) {
                    $filePath = ltrim(ltrim($cssFileName, '~'), '/');
                    // if ~~ (reference is outside vtiger6 folder)
                    if (substr_count($cssFileName, "~") == 2) {
                        $filePath = "../" . $filePath;
                    }
                } else {
                    $filePath = str_replace('.', '/', $cssFileName) . '.'.$fileExtension;
                    $filePath = Vtiger_Theme::getStylePath($filePath);
                }
                $cssStyleInstances[] = $cssScriptModel->set('href', $filePath);
            }
        }
        return $cssStyleInstances;
    }

    /**
     * Retrieves css styles that need to loaded in the page
     * @param Vtiger_Request $request - request model
     * @return <array> - array of Vtiger_CssScript_Model
     */
    public function getHeaderCss(Vtiger_Request $request)
    {
        return array();
    }

    /**
     * Function returns the Client side language string
     * @param Vtiger_Request $request
     */
    public function getJSLanguageStrings(Vtiger_Request $request)
    {
        $moduleName = $request->getModule(false);
        return Vtiger_Language_Handler::export($moduleName, 'jsLanguageStrings');
    }
}
