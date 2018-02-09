<?php
/* ********************************************************************************
* The content of this file is subject to the Quoting Tool ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */

include_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_Edit_View
 */
Class QuotingTool_Edit_View extends Vtiger_Edit_View
{
    /**
     * @var bool
     */
    protected $record = false;

    /**
     * @param Vtiger_Request $request
     * @return bool|void
     * @throws AppException
     */
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        global $current_user;

        $quotingTool = new QuotingTool();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $primaryModule = $request->get('primary_module');
        $record = $request->get('record');
        $userProfile = array(
            'user_name' => $current_user->user_name,
            'first_name' => $current_user->first_name,
            'last_name' => $current_user->last_name,
            'full_name' => $current_user->first_name . ' ' . $current_user->last_name,
            'email1' => $current_user->email1,
            'id' => $current_user->id,
        );

        $quotingToolSettingRecordModel = new QuotingTool_SettingRecord_Model();
        $settings = array();
        $template = null;

        if (isRecordExists($record)) {
            $template = Vtiger_Record_Model::getInstanceById($record);
            $objSettings = $quotingToolSettingRecordModel->findByTemplateId($record);

            if ($objSettings) {
                $settings = array(
                    'template_id' => $objSettings->get('template_id'),
                    'description' => $objSettings->get('description'),
                    'label_decline' => $objSettings->get('label_decline'),
                    'label_accept' => $objSettings->get('label_accept'),
                    'background' => json_decode(html_entity_decode($objSettings->get('background'))),
                    'page_format' => json_decode(html_entity_decode($objSettings->get('page_format'))),
                );
            }
        } else {
            $template = Vtiger_Record_Model::getCleanInstance($moduleName);
            $template->set('module', $primaryModule);
        }

        //Owner
        $userModel = Users_Record_Model::getInstanceById($current_user->id, 'Users');
        $listAgentManager = $userModel->getAccessibleOwnersForUser($moduleName);
        $newListAgentManager = $listAgentManager;
        unset($newListAgentManager['agents']);
        unset($newListAgentManager['vanlines']);

        $viewer->assign('RECORD_ID', $record);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('TEMPLATE', $template);
        $viewer->assign('SETTINGS', QuotingToolUtils::jsonUnescapedSlashes(json_encode($settings, JSON_FORCE_OBJECT)));
        $viewer->assign('USER_PROFILE', $userProfile);
        $viewer->assign('CONFIG', QuotingTool::getConfig());
        $viewer->assign('MODULES', $quotingTool->getModules());
        $viewer->assign('AGENTS', $newListAgentManager);
        $viewer->assign('CUSTOM_FUNCTIONS', $quotingTool->getCustomFunctions());
        $viewer->assign('CUSTOM_FIELDS', $quotingTool->getCustomFields());
        $viewer->assign('IS_DUPLICATE', ($request->has('isDuplicate')?$request->has('isDuplicate'):false));
        $viewer->view('EditView.tpl', $moduleName);
    }

    /**
     * @param Vtiger_Request $request
     * @return array
     */
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/ckeditor_4.5.6_full/CustomFonts/fonts.css',
            '~/libraries/bootstrap/js/eternicode-bootstrap-datepicker/css/datepicker.css',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/signature-pad/assets/jquery.signaturepad.css',
            '~/layouts/vlayout/modules/QuotingTool/resources/css/font-awesome-4.5.0/css/font-awesome.min.css',
            '~/modules/QuotingTool/resources/styles.css',
            '~/modules/QuotingTool/resources/web.css',
            '~/layouts/vlayout/modules/QuotingTool/resources/css/app.css',
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return array
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames = array(
            /*Begin libs*/
            '~/modules/QuotingTool/resources/mpdf/mpdf.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/modernizr-2.8.3/modernizr.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/angularjs-1.3.1/angular.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/angular-resource-1.3.1/angular-resource.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/angular-ui-router-0.2.11/angular-ui-router.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/angular-translate-2.4.2/angular-translate.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/ui-bootstrap-tpls-0.14.3/ui-bootstrap-tpls-0.14.3.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/angular-sanitize-1.2.26/angular-sanitize.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/jquery.nicescroll-3.6.0/jquery.nicescroll.min.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/ckeditor_4.5.6_full/ckeditor.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/ckeditor_4.5.6_full/adapters/jquery.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/ng-ckeditor-0.2.0/ng-ckeditor.min.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/signature-pad/jquery.signaturepad.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/signature-pad/assets/flashcanvas.js',

            '~/libraries/jQuery-File-Upload/js/vendor/jquery.ui.widget.js',
            '~/libraries/jQuery-File-Upload/js/jquery.fileupload.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/css-element-queries/src/ResizeSensor.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/libs/css-element-queries/src/ElementQueries.js',

            /*End libs*/
            /*Begin configs & init app*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/configs/app-constants.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/configs/app-config.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/app.js',
            /*End configs & init app*/
            /*Begin utils*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/utils/app-utils.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/utils/helper.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/utils/jQuery-customs.js',
            /*End utils*/
            /*Begin directives*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/directives/app-directive.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/directives/file.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/directives/datetime.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/directives/select2.js',
            /*End directives*/
            /*Begin locale*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/locale/i18n.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/locale/app-i18n.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/locale/en.js',
            /*End locale*/
            /*Begin models*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/models/app-model.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/models/template.js',
            /*End models*/
            /*Begin controllers*/
            '~/layouts/vlayout/modules/QuotingTool/resources/js/controllers/app-controller.js',
            '~/layouts/vlayout/modules/QuotingTool/resources/js/controllers/right-panel-controller.js',
            /*End controllers*/
            'modules.Emails.resources.Emails',
            "libraries/jquery/colorpicker/js/colorpicker",
            "libraries/jquery/colorpicker/js/eye",
            "libraries/jquery/colorpicker/js/utils"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

}
