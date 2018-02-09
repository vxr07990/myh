<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include_once 'modules/Vtiger/CRMEntity.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/com_vtiger_workflow/include.inc';
include_once 'modules/QuotingTool/QuotingToolUtils.php';

/**
 * Class QuotingTool
 */
class QuotingTool extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_quotingtool';
    public $table_index = 'id';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_quotingtoolcf', 'id');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_quotingtool', 'vtiger_quotingtoolcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_quotingtool' => 'id',
        'vtiger_quotingtoolcf' => 'id'
    );

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'File Name' => array('quotingtool', 'filename'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'File Name' => 'filename',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'filename';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'File Name' => array('quotingtool', 'filename'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'File Name' => 'filename',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('filename');

    // For Alphabetical search
    public $def_basicsearch_col = 'filename';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'filename';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('filename', 'assigned_user_id');

    public $default_order_by = 'filename';
    public $default_sort_order = 'ASC';
    /**
     * const
     */
    const MODULE_NAME = 'QuotingTool';

    public $specialGuestBlocks = array(
        'ParticipatingAgents' => array(
            'id' => 3,
            'name' => 'ParticipatingAgents',
            'label' => 'Participating Agents',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'agent_type',
                    'label' => 'Type'
                ),
                array(
                    'id' => 2,
                    'name' => 'agents_id',
                    'label' => 'Agent'
                ),
                array(
                    'id' => 3,
                    'name' => 'agent_permission',
                    'label' => 'Permission Level'
                ),
                array(
                    'id' => 4,
                    'name' => 'participating_status',
                    'label' => 'Status'
                )
            )
        ),
    );
    /**
     * @var array
     */
    public $enableModules = array();

    /**
     * @var array
     */
    public $enableModuleWithRelated = array();

    public $specialModules = array('Users');

    public $specialItemDetails = array('Estimates', 'Actuals');

    public $guestBlocks = array();

    public $modulesWithSuffix = array();

    /**
     * @var string
     */
    public $pdfLibLink = 'https://www.vtexperts.com/files/mpdf.zip';
    /**
     * @var array
     */
    public $workflows = array(
        'QuotingToolMailTask' => 'Send Email with Quoting Tool attachments'
    );
    // [Module [Block [Field]]]
    public $injectFields = array(
        'Users' => array(
            'LBL_USER_ADV_OPTIONS' => array('*'),
            'LBL_TAG_CLOUD_DISPLAY' => array('*'),
            'LBL_CURRENCY_CONFIGURATION' => array('*'),
            'LBL_CALENDAR_SETTINGS' => array('*'),
            'LBL_USERLOGIN_ROLE' => array('confirm_password', 'user_password')
        )
    );

    // Regex for get vartiable from 2 dollar sign ($var$)
    var $patternVar = '/.*?[^\\\]\\$(.+?[^\\\])\\$/';   // '.*?[^\\]\$(.+?[^\\])\$'
    var $patternEscapeCharacters = '/.*?[\\\](.+?)/';   // '.*?[\\](.+?)'
    const DEFAULT_PDF_FOLDER = 'storage/QuotingTool/';
    const DEFAULT_FOLDER = 'DocumentDesignerImages';

    public function __construct()
    {
        parent::__construct();

//        // Get configurations
//        $arrConfigs = $this->getQuotingToolConfigs();
//        $this->enableModules = $arrConfigs['enableModules'];
//        $this->enableModuleWithRelated = $arrConfigs['enableModuleWithRelated'];
//        $this->guestBlocks = $arrConfigs['guestBlocks'];
//
//        $this->modulesWithSuffix = $this->getModulesWithSuffix();
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type)
    {
//        $arrConfigs = $this->getQuotingToolConfigs();
//        $this->enableModules = $arrConfigs['enableModules'];

        if ($event_type == 'module.postinstall') {
            // Handle actions when this module is install.
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
            self::resetValid();
        } else if ($event_type == 'module.disabled') {
            // Handle actions when this module is disabled.
            self::removeWidgetTo($modulename);
            // self::removePDFWidget($modulename, $modulename, $this->enableModules);
            self::removeWorkflows($modulename);
        } else if ($event_type == 'module.enabled') {
            // Handle actions when this module is enabled.
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
        } else if ($event_type == 'module.preuninstall') {
            // Handle actions when this module is about to be deleted.
            self::removeWidgetTo($modulename);
            // self::removePDFWidget($modulename, $this->enableModules);
            self::removeWorkflows($modulename);
            self::removeValid();
        } else if ($event_type == 'module.preupdate') {
            // Handle actions before this module is updated.
            self::removeWidgetTo($modulename);
            // self::removePDFWidget($modulename, $this->enableModules);
            self::removeWorkflows($modulename);
        } else if ($event_type == 'module.postupdate') {
            // Handle actions when this module is update.
            self::addWidgetTo($modulename);
            // self::addPDFWidget($modulename, $this->enableModules);
            self::installWorkflows($modulename);
            self::resetValid();
        }
    }

    /**
     * @param string $moduleName
     */
    static function addWidgetTo($moduleName)
    {
        global $adb;

        $module = Vtiger_Module::getInstance($moduleName);
        $widgetName = 'Quoting Tool';

        if ($module) {
            $css_widgetType = 'HEADERCSS';
            $css_widgetLabel = vtranslate($widgetName, $moduleName);
            $css_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}CSS.css";

            $js_widgetType = 'HEADERSCRIPT';
            $js_widgetLabel = vtranslate($widgetName, $moduleName);
            $js_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}JS.js";
            $js_link_2 = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}Utils.js";

            // css
            $module->addLink($css_widgetType, $css_widgetLabel, $css_link);
            // js
            $module->addLink($js_widgetType, $js_widgetLabel, $js_link);
            $module->addLink($js_widgetType, $js_widgetLabel, $js_link_2);
        }

        // Check module
        $rs = $adb->pquery("SELECT * FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `vtiger_ws_entity` (`name`, `handler_path`, `handler_class`, `ismodule`)
            VALUES (?, 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');", array($moduleName));
        }
    }

    /**
     * @param $moduleName
     */
    static function removeWidgetTo($moduleName)
    {
        global $adb;

        $module = Vtiger_Module::getInstance($moduleName);
        $widgetName = 'Quoting Tool';

        if ($module) {
            $css_widgetType = 'HEADERCSS';
            $css_widgetLabel = vtranslate($widgetName, $moduleName);
            $css_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}CSS.css";

            $js_widgetType = 'HEADERSCRIPT';
            $js_widgetLabel = vtranslate($widgetName, $moduleName);
            $js_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}JS.js";
            $js_link_2 = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}Utils.js";

            // css
            $module->deleteLink($css_widgetType, $css_widgetLabel, $css_link);
            // js
            $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link);
            $module->deleteLink($js_widgetType, $js_widgetLabel, $js_link_2);
        }

        // Check module
        $adb->pquery("DELETE FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
    }

    /**
     * Add widget to other module.
     * @param string $moduleName
     * @param array $moduleNames
     * @param string $widgetType
     * @param string $widgetName
     */
    function addPDFWidget($moduleName, $moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Quoting Tool')
    {
        if (empty($moduleNames)) {
            return;
        }

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        $widgetLabel = vtranslate($widgetName, $moduleName);
        $url = 'module=' . $moduleName . '&view=Widget';

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->addLink($widgetType, $widgetLabel, $url, '', '', '');
            }
        }
    }

    /**
     * Remove widget from other modules.
     * @param string $moduleName
     * @param array $moduleNames
     * @param string $widgetType
     * @param string $widgetName
     */
    function removePDFWidget($moduleName, $moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Quoting Tool')
    {
        if (empty($moduleNames)) {
            return;
        }

        if (is_string($moduleNames)) {
            $moduleNames = array($moduleNames);
        }

        $widgetLabel = vtranslate($widgetName, $moduleName);
        $url = 'module=' . $moduleName . '&view=Widget';

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->deleteLink($widgetType, $widgetLabel, $url);
            }
        }
    }

    /**
     * @param string $fieldName
     * @param string $moduleName
     * @param string $referenceFieldname
     * @return string
     */
    public function convertFieldToken($fieldName, $moduleName = null, $referenceFieldname = null)
    {
        $supportedModulesList = Settings_LayoutEditor_Module_Model::getSupportedModules();
        $token = null;

        if (!$moduleName || (!in_array($moduleName, $supportedModulesList) && !in_array($moduleName, $this->specialModules))) {
            $token = '$' . $fieldName . '$';
        } else if (!$referenceFieldname) {
            $token = '$' . $moduleName . '__' . $fieldName . '$';
        } else {
            $token = '$' . $moduleName . '__' . $referenceFieldname . '__' . $fieldName . '$';
        }

        return $token;
    }

    /**
     * @param string $token
     * @return array
     */
    public function extractFieldToken($token)
    {
        $tmp = explode('__', $token);
        // Init with empty module
        $data = array(
            'moduleName' => ''
        );
        $num = count($tmp);

        if ($num == 1) {
            // Only field
            $data['fieldName'] = $tmp[0];
        } else if ($num == 2) {
            // Module field
            $data['moduleName'] = $tmp[0];
            $data['fieldName'] = $tmp[1];
        } else if ($num == 3) {
            // Related module field
            $data['moduleName'] = $tmp[0];
            $data['referenceFieldname'] = $tmp[1];
            $data['fieldName'] = $tmp[2];
        }

        return $data;
    }

    /**
     * @param string $subject
     * @return array
     */
    public function getVarFromString($subject)
    {
        $vars = array();

        if ($subject) {
            preg_match_all($this->patternVar, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $v = array_unique($matches[1]);

                foreach ($v as $t) {
                    if (!in_array($t, $vars)) {
                        $vars[] = '$' . $t . '$';
                    }
                }
            }
        }

        return $vars;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getFieldTokenFromString($subject)
    {
        $supportedModulesList = Settings_LayoutEditor_Module_Model::getSupportedModules();
        $tokens = array();

        if ($subject) {
            preg_match_all($this->patternVar, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $tk = array_unique($matches[1]);

                foreach ($tk as $t) {
                    $extract = $this->extractFieldToken($t);
                    $num = count($extract);
                    $moduleName = $extract['moduleName'];
                    $fieldName = $extract['fieldName'];

                    // Validate module (not allow empty module - not in entity field)
                    if (!$moduleName || (!in_array($moduleName, $supportedModulesList) && !in_array($moduleName, $this->specialModules))) {
                        continue;
                    }

                    if ($num == 3) {
                        // when is reference fields of related module
                        $fieldName = $extract['referenceFieldname'] . '__' . $fieldName;
                    }

                    if (!array_key_exists($moduleName, $tokens)) {
                        $tokens[$moduleName] = array();
                    }

                    $needle = '$' . $t . '$';
                    $tokens[$moduleName][$needle] = $fieldName;
                }
            }
        }

        return $tokens;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getEscapeCharactersFromString($subject)
    {
        $characters = array();

        if ($subject) {
            preg_match_all($this->patternEscapeCharacters, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $m = array_unique($matches[1]);

                foreach ($m as $c) {
                    if (!in_array($c, $characters)) {
                        $needle = '\\' . $c;
                        $characters[$needle] = $c;
                    }
                }
            }
        }

        return $characters;
    }

    /**
     * @param $subject
     * @return array
     */
    public function getEmailFromString($subject)
    {
        $email = '';

        if ($subject) {
            $pattern = '/\\((.*?)\\)/';
            preg_match_all($pattern, $subject, $matches);

            if ($matches && count($matches) > 0) {
                $email = ($matches[1][0]) ? $matches[1][0] : $subject;
            }
        }

        return $email;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @return mixed
     */
    public function mergeBlockTokens($tokens, $record, $content)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $crmid = 'crmid';
        $inventoryModules = getInventoryModules();
        $productModules = array('Products', 'Services');
        $currencyFieldsList = array('adjustment', 'grandTotal', 'hdnSubTotal', 'preTaxTotal', 'tax_totalamount',
            'shtax_totalamount', 'discountTotal_final', 'discount_amount_final', 'shipping_handling_charge', 'totalAfterDiscount');

        $pdfContentModel = new QuotingTool_PDFContent_Model();
        $recordModel = Vtiger_Record_Model::getInstanceById($record);
        $moduleName = $recordModel->getModuleName();

        $blockStartTemplates = array(
            '#PRODUCTBLOC_START#',
            '#SERVICEBLOC_START#',
            '#PRODUCTSERVICEBLOC_START#'
        );
        $blockEndTemplates = array(
            '#PRODUCTBLOC_END#',
            '#SERVICEBLOC_END#',
            '#PRODUCTSERVICEBLOC_END#'
        );
        $blockTemplates = array_merge($blockStartTemplates, $blockEndTemplates);
        $dataTableType = null;
        /**
         * Copy from modules/Inventory/views/Detail.php:99
         */
        $currencyFieldsList2 = array('taxTotal', 'netPrice', 'listPrice', 'unitPrice', 'productTotal',
            'discountTotal', 'discount_amount');

        /** @var simple_html_dom_node $table */
        foreach ($html->find('table') as $table) {
            $dataTableType = $table->attr['data-table-type'];

            if (!$dataTableType || $dataTableType != 'pricing_table') {
                // Only parse pricing table
                continue;
            }

            // Clean un-necessary attributes
            $table->removeAttribute('data-info');

            $isTemplateStart = false;
            $isTemplateEnd = false;

            $newHeader = array();
            $newBody = array();
            $newFooter = array();

            /** @var simple_html_dom_node $thead */
            $thead = null;
            /** @var simple_html_dom_node $tbody */
            $tbody = null;
            /** @var simple_html_dom_node $tfoot */
            $tfoot = null;

            $newHeaderTokens = array();
            $newBodyTokens = array();
            $newFooterTokens = array();

            $dataOddStyle = $table->attr['data-odd-style'];
            $dataEvenStyle = $table->attr['data-even-style'];

            /** @var simple_html_dom_node $row */
            foreach ($table->find('tr') as $row) {
                $isNormalRow = true;

                /** @var simple_html_dom_node $cell */
                foreach ($row->children() as $cell) {
                    $cellText = trim($cell->plaintext);

                    if (!in_array($cellText, $blockTemplates)) {
                        // Normal cell
                        continue;
                    }

                    $isNormalRow = false;
                    $cell->parent->outertext = $cellText;

                    if (in_array($cellText, $blockStartTemplates)) {
                        // BlockStart cell
                        $isTemplateStart = true;
                        break;
                    } else if (in_array($cellText, $blockEndTemplates)) {
                        // BlockEnd cell
                        $isTemplateEnd = true;
                        break;
                    }
                }

                if ($isNormalRow) {
                    if (!$isTemplateStart) {
                        $newHeader[] = $row;
                        $newHeaderTokens = array_merge($newHeaderTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateStart && !$isTemplateEnd) {
                        $newBody[] = $row;
                        $newBodyTokens = array_merge($newBodyTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateEnd) {
                        $newFooter[] = $row;
                        $newFooterTokens = array_merge($newFooterTokens, $this->getFieldTokenFromString($row->outertext));
                    }
                }

                /** @var simple_html_dom_node $parent */
                $parent = $row->parent();

                if (($thead === null) && ($parent->tag == 'thead')) {
                    $thead = $parent;
                } else if (($tbody === null) && ($parent->tag == 'tbody')) {
                    $tbody = $parent;
                } else if (($tfoot === null) && ($parent->tag == 'tfoot')) {
                    $tfoot = $parent;
                }
            }

            $innertext = '';

            // Header
            $tmpHead = '';
            foreach ($newHeader as $row) {
                $newTheadRowsText = $row->outertext;

                foreach ($newHeaderTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = $recordModel->getDisplayValue($f, $record);
                        $newTheadRowsText = str_replace($k, $needValue, $newTheadRowsText);
                    }
                }

                $tmpHead .= $newTheadRowsText;
            }

            if ($thead !== null) {
                $thead->innertext = $tmpHead;
                $innertext .= $thead->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<thead>' . $tmpHead . '</thead>';
            } else {
                $innertext .= $tmpHead;
            }

            // Body
            $tmpBody = '';
            if ($tbody !== null) {
                $dataOddStyle = $tbody->attr['data-odd-style'];
                $dataEvenStyle = $tbody->attr['data-even-style'];
            }

            $dataOddStyle = ($dataOddStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataOddStyle))) : '';
            $dataEvenStyle = ($dataEvenStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataEvenStyle))) : '';
            $final_details = array();

            if (in_array($moduleName, $inventoryModules)) {
                /** @var Inventory_Record_Model $recordModel */
                $recordModel = Inventory_Record_Model::getInstanceById($record, $moduleName);
                // Get products - to get final detail only
                $products = $recordModel->getProducts();

                if ($products && count($products) > 0) {
                    $final_details = $products[1]['final_details'];
                    $items = $pdfContentModel->getLineItemsAndTotal($record);

                    if ($items && count($items) > 0) {
                        // Merge
                        $products = $this->mergeRelatedProductWithQueryProduct($products, $items);
                    }

                    $counter = 0;
                    foreach ($products as $k => $value) {
                        $even = (++$counter % 2) == 0;

                        $cloneTbodyRowTokens = $newBodyTokens;

                        // Update token value to clone token
                        foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                            $moduleModel = Vtiger_Module_Model::getInstance($tModuleName);

                            foreach ($tFields as $fToken => $fName) {
                                // Hardcode fieldname - by vtiger core :(
                                if ($fName == $crmid) {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $record;
                                } elseif ($fName == 'productid') {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $value['productname'];
                                } else if ($fName == 'qty_per_unit' || $fName == 'unit_price' || $fName == 'weight'
                                    || $fName == 'commissionrate' || $fName == 'qtyinstock' || $fName == 'quantity'
                                    || $fName == 'listprice' || $fName == 'tax1' || $fName == 'tax2' || $fName == 'tax3'
                                    || $fName == 'discount_amount' || $fName == 'discount_percent'
                                    || in_array($fName, $currencyFieldsList) || in_array($fName, $currencyFieldsList2)
                                ) {
                                    //Format number
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = Vtiger_Currency_UIType::transformDisplayValue($value[$fName], null, true);
                                } else if ($fName == 'sequence_no') {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $value[$fName];
                                } else if (in_array($tModuleName, $productModules)) {
                                    $needValue = $value[$fName];
                                    if (is_numeric($needValue) && is_float($needValue)) {
                                        $needValue = Vtiger_Currency_UIType::transformDisplayValue($needValue, null, true);
                                    }

                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $needValue;
                                } else {
                                    $fieldModel = $moduleModel->getField($fName);
                                    // Check field on field table or table column
                                    if ($fieldModel) {
                                        $fieldDataType = $fieldModel->getFieldDataType();

                                        if ($fieldModel->get('table') == 'vtiger_inventoryproductrel') {
                                            // inventory table
                                            $needValue = $value[$fName];
                                            if (is_numeric($needValue) && is_float($needValue)) {
                                                $needValue = Vtiger_Currency_UIType::transformDisplayValue($needValue, null, true);
                                            } else if ($fieldDataType == 'text') {

                                            }

                                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $needValue;
                                        } else {
                                            // Base table
                                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $recordModel->getDisplayValue($fName, $recordModel->getId());
                                        }
                                    }
                                }
                            }
                        }

                        /** @var simple_html_dom_node $row */
                        foreach ($newBody as $row) {
                            // Row index
                            $row->setAttribute('data-row-number', $counter);

                            /** @var simple_html_dom_node $cell */
                            foreach ($row->children() as $cell) {
                                $style = ($even) ? $dataEvenStyle : $dataOddStyle;
                                $oldStyle = $cell->getAttribute('style');
                                $newStyle = null;

                                if (!$oldStyle) {
                                    $newStyle = $style;
                                } else {
                                    $oldStyle = trim($oldStyle);
                                    if (QuotingToolUtils::endsWith($oldStyle, ';')) {
                                        $newStyle = $oldStyle . ' ' . $style;
                                    } else {
                                        $newStyle = $oldStyle . '; ' . $style;
                                    }
                                }

                                $newStyle = trim($newStyle);

                                if ($newStyle !== '') {
                                    $cell->setAttribute('style', $newStyle);
                                }
                            }

                            $cloneTbodyRowsTemplate = $row->outertext;
                            // Update clone template
                            foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                                foreach ($tFields as $k => $f) {
                                    $f = nl2br($f);
                                    $cloneTbodyRowsTemplate = str_replace($k, $f, $cloneTbodyRowsTemplate);
                                }
                            }

                            $tmpBody .= $cloneTbodyRowsTemplate;
                        }
                    }
                }
            } else {
                foreach ($newBody as $row) {
                    $newTbodyRowsText = $row->outertext;

                    foreach ($newBodyTokens as $tModuleName => $tFields) {
                        foreach ($tFields as $k => $f) {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                            $newTbodyRowsText = str_replace($k, $needValue, $newTbodyRowsText);
                        }
                    }

                    $tmpBody .= $newTbodyRowsText;
                }
            }

            if ($tbody !== null) {
                $tbody->innertext = $tmpBody;
                $innertext .= $tbody->outertext;
            } else {
                $innertext .= $tmpBody;
            }

            // Footer
            $tmpFoot = '';
            foreach ($newFooter as $row) {
                $newTfootRowsText = $row->outertext;

                foreach ($tokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = null;
                        if (in_array($moduleName, $inventoryModules) && isset($final_details[$f])) {
                            $needValue = Vtiger_Currency_UIType::transformDisplayValue($final_details[$f], null, true);
                            $needValue = nl2br($needValue);
                        } else if ($f == $crmid) {
                            $needValue = $record;
                        } else {
                            $needValue = $recordModel->getDisplayValue($f, $record);
                        }

                        $newTfootRowsText = str_replace($k, $needValue, $newTfootRowsText);
                    }
                }

                $tmpFoot .= $newTfootRowsText;
            }

            if ($tfoot !== null) {
                $tfoot->innertext = $tmpFoot;
                $innertext .= $tfoot->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<tfoot>' . $tmpFoot . '</tfoot>';
            } else {
                $innertext .= $tmpFoot;
            }

            $table->innertext = $innertext;
            $content = $html->save();
        }

        return $content;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @return mixed
     */
    public function mergeGuestBlockTokens($tokens, $record, $content)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $crmid = 'crmid';
        $recordModel = Vtiger_Record_Model::getInstanceById($record);
        $moduleName = $recordModel->getModuleName();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $blockStartTemplates = array(
            '#GUESTBLOC_START#',
        );
        $blockEndTemplates = array(
            '#GUESTBLOC_END#',
        );
        $blockTemplates = array_merge($blockStartTemplates, $blockEndTemplates);
        $dataTableType = null;

        /** @var simple_html_dom_node $table */
        foreach ($html->find('table') as $table) {
            $dataTableType = $table->attr['data-table-type'];
            $dataModule = $table->attr['data-module'];

            if (!$dataTableType || $dataTableType != 'guest_block') {
                // Only parse pricing table
                continue;
            }

            // Clean un-necessary attributes
            $table->removeAttribute('data-info');

            $isTemplateStart = false;
            $isTemplateEnd = false;

            $newHeader = array();
            $newBody = array();
            $newFooter = array();

            /** @var simple_html_dom_node $thead */
            $thead = null;
            /** @var simple_html_dom_node $tbody */
            $tbody = null;
            /** @var simple_html_dom_node $tfoot */
            $tfoot = null;

            $newHeaderTokens = array();
            $newBodyTokens = array();
            $newFooterTokens = array();

            $dataOddStyle = $table->attr['data-odd-style'];
            $dataEvenStyle = $table->attr['data-even-style'];

            // guest block type
            $guestBlock = $table->attr['data-guest-block'];

            /** @var simple_html_dom_node $row */
            foreach ($table->find('tr') as $row) {
                $isNormalRow = true;

                /** @var simple_html_dom_node $cell */
                foreach ($row->children() as $cell) {
                    $cellText = trim($cell->plaintext);

                    if (!in_array($cellText, $blockTemplates)) {
                        // Normal cell
                        continue;
                    }

                    $isNormalRow = false;
                    $cell->parent->outertext = $cellText;

                    if (in_array($cellText, $blockStartTemplates)) {
                        // BlockStart cell
                        $isTemplateStart = true;
                        break;
                    } else if (in_array($cellText, $blockEndTemplates)) {
                        // BlockEnd cell
                        $isTemplateEnd = true;
                        break;
                    }
                }

                if ($isNormalRow) {
                    if (!$isTemplateStart) {
                        $newHeader[] = $row;
                        $newHeaderTokens = array_merge($newHeaderTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateStart && !$isTemplateEnd) {
                        $newBody[] = $row;
                        $newBodyTokens = array_merge($newBodyTokens, $this->getFieldTokenFromString($row->outertext));
                    } else if ($isTemplateEnd) {
                        $newFooter[] = $row;
                        $newFooterTokens = array_merge($newFooterTokens, $this->getFieldTokenFromString($row->outertext));
                    }
                }

                /** @var simple_html_dom_node $parent */
                $parent = $row->parent();

                if (($thead === null) && ($parent->tag == 'thead')) {
                    $thead = $parent;
                } else if (($tbody === null) && ($parent->tag == 'tbody')) {
                    $tbody = $parent;
                } else if (($tfoot === null) && ($parent->tag == 'tfoot')) {
                    $tfoot = $parent;
                }
            }

            // Table text content
            $innertext = '';

            // Header
            $tmpHead = '';
            foreach ($newHeader as $row) {
                $newTheadRowsText = $row->outertext;

                foreach ($newHeaderTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $k => $f) {
                        $needValue = $recordModel->getDisplayValue($f, $record);
                        $newTheadRowsText = str_replace($k, $needValue, $newTheadRowsText);
                    }
                }

                $tmpHead .= $newTheadRowsText;
            }

            if ($thead !== null) {
                $thead->innertext = $tmpHead;
                $innertext .= $thead->outertext;
            } else if ($tbody !== null) {
                $innertext .= '<thead>' . $tmpHead . '</thead>';
            } else {
                $innertext .= $tmpHead;
            }

            // Body
            $tmpBody = '';
            if ($tbody !== null) {
                $dataOddStyle = $tbody->attr['data-odd-style'];
                $dataEvenStyle = $tbody->attr['data-even-style'];
            }

            $dataOddStyle = ($dataOddStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataOddStyle))) : '';
            $dataEvenStyle = ($dataEvenStyle) ? QuotingToolUtils::convertArrayToInlineStyle(json_decode(html_entity_decode($dataEvenStyle))) : '';

            $items = array();
            $guestBlockModel = Vtiger_Module_Model::getInstance($guestBlock);
            $guestBlockRecordModel = Vtiger_Record_Model::getInstanceById($record);
            $guestModules = Vtiger_Index_View::getGuestBlocks($moduleName, false);

            if (isRecordExists($record) && array_key_exists($guestBlock, $guestModules)) {
                // When block is entity
                $items = $guestBlockRecordModel->getGuestModuleRecords($guestBlock);
            } else if ($guestBlock == 'ParticipatingAgents') {
                $items = $guestBlockModel->getParticipants($record);
            } else if ($dataModule == 'OrdersTask') {
                // When block is special module
                /** @var OrdersTask_Module_Model $guestBlockModel */
                $guestBlockModel = Vtiger_Module_Model::getInstance('OrdersTask');
                $blockName = null;
                
                $items = $guestBlockRecordModel->getExtraBlockFieldValues($guestBlock);
            }

            // Reference fields
            $referenceFields = array();

            if ($guestBlockModel) {
                $referenceFields = $guestBlockModel->getFieldsByType('reference');
            }

            $counter = 0;
            /**
             * @var array | Vtiger_Field_Model $value
             */
            foreach ($items as $k => $value) {
                $even = (++$counter % 2) == 0;
                $cloneTbodyRowTokens = $newBodyTokens;

                // Update token value to clone token
                /**
                 * @var string $tModuleName
                 * @var Vtiger_Field_Model $tFields
                 */
                foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                    foreach ($tFields as $fToken => $fName) {
                        if ($fName == $crmid) {
                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $record;
                        } else if (array_key_exists($guestBlock, $guestModules)) {
                            $fieldModel = $guestBlockModel->getField($fName);
                            // Check field on field table or table column
                            if ($fieldModel) {
                                // Base table
                                $cloneTbodyRowTokens[$tModuleName][$fToken] = $value->getDisplayValue($fName, $value->getId());
                                $fieldDataType = $fieldModel->getFieldDataType();

                                // For special types - prevent nl2br html code uitype = 10 will return <a> tag
                                if (array_key_exists($fieldModel->getName(), $referenceFields) || in_array($fieldDataType, array('owner'))) {
                                    $cloneTbodyRowTokens[$tModuleName][$fToken] = $this->getTextFromHtmlTag($cloneTbodyRowTokens[$tModuleName][$fToken], 'a');
                                }
                            }
                        } else if ($guestBlock == 'ParticipatingAgents') {
                            if ($fName == 'agent_permission') {
                                $value[$fName] = $value['view_level'];

                                if ($value[$fName] == 'full') {
                                    $value[$fName] = 'Full';
                                } else if ($value[$fName] == 'no_rates') {
                                    $value[$fName] = 'No-rates';
                                } else if ($value[$fName] == 'read_only') {
                                    $value[$fName] = 'Read-only';
                                } else if ($value[$fName] == 'no_access') {
                                    $value[$fName] = 'No-Access';
                                }
                            } else if ($fName == 'participating_status') {
                                $value[$fName] = $value['status'];
                            } else if ($fName == 'agents_id') {
                                $agentRecordModel = Vtiger_Record_Model::getInstanceById($value[$fName], 'Agents');
                                $value[$fName] = $agentRecordModel->getDisplayName();
                            }

                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $value[$fName];
                        } else if ($dataModule == 'OrdersTask') {
                            if ($fName == 'personnel_type') {
                                $fieldModel1 = Vtiger_Field_Model::getInstance($fName, $moduleModel);
                                $fieldModel1->set('fieldvalue', $value[$fName]);
                                $value[$fName] = $fieldModel1->getDisplayValue($value[$fName]);
                                $value[$fName] = $this->getTextFromHtmlTag($value[$fName], 'a');
                            } else if ($fName == 'equipment_name') {
                                $tmpRecordModel = $recordModel;
                                $tmpRecordModel->set('equipment_name', $value[$fName]);
                                $value[$fName] = $tmpRecordModel->getDisplayValue($fName, $tmpRecordModel->getId());
                                $value[$fName] = $this->getTextFromHtmlTag($value[$fName], 'a');
                            }

                            $cloneTbodyRowTokens[$tModuleName][$fToken] = $value[$fName];
                        }
                    }
                }

                /** @var simple_html_dom_node $row */
                foreach ($newBody as $row) {
                    // Row index
                    $row->setAttribute('data-row-number', $counter);

                    /** @var simple_html_dom_node $cell */
                    foreach ($row->children() as $cell) {
                        $style = ($even) ? $dataEvenStyle : $dataOddStyle;
                        $oldStyle = $cell->getAttribute('style');
                        $newStyle = null;

                        if (!$oldStyle) {
                            $newStyle = $style;
                        } else {
                            $oldStyle = trim($oldStyle);
                            if (QuotingToolUtils::endsWith($oldStyle, ';')) {
                                $newStyle = $oldStyle . ' ' . $style;
                            } else {
                                $newStyle = $oldStyle . '; ' . $style;
                            }
                        }

                        $newStyle = trim($newStyle);

                        if ($newStyle !== '') {
                            $cell->setAttribute('style', $newStyle);
                        }
                    }

                    $cloneTbodyRowsTemplate = $row->outertext;
                    // Update clone template
                    foreach ($cloneTbodyRowTokens as $tModuleName => $tFields) {
                        foreach ($tFields as $k => $f) {
                            $f = nl2br($f);
                            $cloneTbodyRowsTemplate = str_replace($k, $f, $cloneTbodyRowsTemplate);
                        }
                    }

                    $tmpBody .= $cloneTbodyRowsTemplate;
                }
            }

            if ($tbody !== null) {
                $tbody->innertext = $tmpBody;
                $innertext .= $tbody->outertext;
            } else {
                $innertext .= $tmpBody;
            }

            $table->innertext = $innertext;
            $content = $html->save();
        }

        return $content;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @return mixed
     */
    public function mergeCustomBlockTokens($tokens, $record, $content)
    {
        // Merge custom block token if have variable (tokens)
        $customBlockTokens = array();

        foreach ($tokens as $tModuleName => $tFields) {
            $tokenBlockName = '$' . $tModuleName . '__block$';

            foreach ($tFields as $k => $f) {
                if (in_array($tModuleName, $this->specialItemDetails) && ($k == $tokenBlockName)) {
                    $customBlockTokens[$tModuleName][$k] = $f;
                }
            }
        }

        if (count($customBlockTokens) == 0) {
            // return if not found block token
            return $content;
        }

        // Merge data
        foreach ($customBlockTokens as $tModuleName => $tFields) {
            $tokenBlockName = '$' . $tModuleName . '__block$';

            foreach ($tFields as $k => $f) {
                if (in_array($tModuleName, $this->specialItemDetails) && ($k == $tokenBlockName)) {
                    $viewer = new Vtiger_Viewer();

                    /** @var Estimates_Record_Model $recordModel */
                    $recordModel = Vtiger_Record_Model::getInstanceById($record);
                    $detailLineItems = $recordModel->getDetailLineItems();
                    $viewer->assign('LINEITEMS', $detailLineItems);
                    $viewer->assign('ALWAYS_SHOW_CONTENT_DIV', 1);

                    $f = $viewer->view('MoveHQLineItemDetail.tpl', 'Estimates', true);
                }

                // Convert token to HTML by get & merge template
                $content = str_replace($k, $f, $content);
            }
        }

        // Include simple html dom to remove all inputs
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        // Table from templates
        $tables = $html->find('table.lineItemsEdit');

        foreach ($tables as $table) {
            $table->setAttribute('border', '1');
            $table->setAttribute('cellspacing', '0');
            $table->setAttribute('cellpadding', '6');
            $table->setAttribute('style', 'width: 100%; border-collapse: collapse;');
            $itemDetailDivisions = $table->find('td.item-detail-division');
            $blockHeaders = $table->find('th.blockHeader');

            foreach ($blockHeaders as $blockHeader) {
                $blockHeader->setAttribute('colspan', '4');
            }
            foreach ($itemDetailDivisions as $itemDetailDivision) {
                $itemDetailDivision->setAttribute('colspan', '4');
            }

            // Remove inputs
            foreach ($table->find('input') as $element) {
                $element->outertext = '';
            }

            // Show item details
            foreach ($table->find('tr[class*="section_"]') as $element) {
                $element->class = '';
            }

            // Hide element
            foreach ($table->find('.hide') as $element) {
                // $element->setAttribute('style', 'display: none;');
                $element->outertext = '';
            }
        }

        $content = $html->save();

        return $content;
    }

    /**
     * @param $tokens
     * @param $record
     * @param $content
     * @param string $module
     * @return mixed
     */
    public function mergeTokens($tokens, $record, $content, $module = 'Vtiger')
    {
        if (empty($tokens) || !$record || $content == '') {
            // return if not found token
            return $content;
        }

        $crmid = 'crmid';

        $moduleModel = Vtiger_Module_Model::getInstance($module);
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $module);

        if (!$recordModel) {
            // Return if invalid record model
            return $content;
        }

        // Fields to ignore
        $ignoreFields = array();    // array('modifiedby', 'created_user_id')
        // Module to ignore
        $ignoreModules = array();

        // Parse data
        foreach ($tokens as $tModuleName => $tFields) {
            // Reference fields
            $referenceFields = $moduleModel->getFieldsByType('reference');

            // If Primary module
            if ($tModuleName == $module) {
                foreach ($tFields as $fToken => $fName) {
                    if (in_array($fName, $ignoreFields)) {
                        continue;
                    }

                    if ($fName == $crmid) {
                        $tokens[$tModuleName][$fToken] = $recordModel->getId();
                    } else {
                        $fieldModel = $moduleModel->getField($fName);

                        if (!$fieldModel) {
                            // Invalid field model
                            unset($tokens[$tModuleName][$fToken]);
                            continue;
                        }

                        $fieldDataType = $fieldModel->getFieldDataType();
                        $needValue = $recordModel->getDisplayValue($fName, $recordModel->getId());

                        if (array_key_exists($fName, $referenceFields)) {
                            // Prepare reference record model
                            // Merge later
                            if (!$recordModel->get($fName)) {
                                $needValue = '';
                            } else {
                                $relatedRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get($fName));
                                $needValue = $relatedRecordModel ? $relatedRecordModel->getName() : '';
                            }
                        }

                        if (in_array($fieldDataType, array('email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                            $needValue = $recordModel->get($fName);
                        }

                        // For special types - prevent nl2br html code uitype = 10 will return <a> tag
                        if (in_array($fieldDataType, array('owner'))) {
                            $needValue = $this->getTextFromHtmlTag($needValue, 'a');
                        }

                        $tokens[$tModuleName][$fToken] = $needValue;
                    }
                }

                $ignoreModules[] = $tModuleName;
            }

            // For reference record model
            /**
             * @var string $fieldName
             * @var Vtiger_Field_Model $fieldModel
             */
            foreach ($referenceFields as $fieldName => $fieldModel) {
                if (in_array($fieldName, $ignoreFields)) {
                    continue;
                }

                $relatedFieldValue = $recordModel->get($fieldName);

                // Validate all related records
                if (!$relatedFieldValue || !QuotingToolUtils::isRecordExists($relatedFieldValue)) {
                    $referenceList = $fieldModel->getReferenceList();

                    foreach ($referenceList as $ref) {
                        // Only parse with in reference module list
                        if (!isset($tokens[$ref]) || !$tokens[$ref] || in_array($ref, $ignoreModules)) {
                            continue;
                        }

                        $relatedFields = $tokens[$ref];

                        foreach ($relatedFields as $fToken => $fName) {
                            // Check if field is related module
                            // structure: referenceFieldName__fieldName
                            $arrFieldName = explode('__', $fName);
                            $numFieldName = count($arrFieldName);

                            if ($numFieldName > 1 && $arrFieldName[0] == $fieldName) {
                                $tokens[$ref][$fToken] = '';
                            }
                        }
                    }

                    continue;
                }

                // Start - Parse data from related record
                $relatedRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get($fieldName));

                if (in_array($fieldName, array('modifiedby', 'created_user_id'))) {
                    // With Users module
                    $relatedRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get($fieldName), 'Users');
                }

                $relatedModuleName = $relatedRecordModel->getModuleName();

                if (in_array($relatedModuleName, $ignoreModules)) {
                    // pass module
                    continue;
                }

                $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
                $relatedReferenceFields = $relatedModuleModel->getFieldsByType('reference');

                if (!array_key_exists($relatedModuleName, $tokens)) {
                    // Invalid related module name
                    continue;
                }

                $relatedFields = $tokens[$relatedModuleName];

                foreach ($relatedFields as $fToken => $fName) {
                    // Check if field is related module
                    // structure: referenceFieldName__fieldName
                    $arrFieldName = explode('__', $fName);
                    $numFieldName = count($arrFieldName);

                    if ($numFieldName <= 1 || $arrFieldName[0] != $fieldName) {
                        // invalid reference field
                        continue;
                    }

                    // get field name from referenceFieldName__fieldName
                    $fName = $arrFieldName[1];

                    // if crmid
                    if ($fName == $crmid) {
                        $tokens[$relatedModuleName][$fToken] = $relatedRecordModel->getId();
                        continue;
                    }

                    $relatedFieldModel = $relatedRecordModel->getField($fName);

                    if (!$relatedFieldModel) {
                        unset($tokens[$relatedModuleName][$fToken]);
                        continue;
                    }

                    $fieldDataType = $relatedFieldModel->getFieldDataType();
                    $needValue = $relatedRecordModel->getDisplayValue($fName, $relatedFieldModel->getId());

                    if (array_key_exists($fName, $relatedReferenceFields)) {
                        // Prepare reference record model
                        // Merge later
                        if (!$relatedRecordModel->get($fName)) {
                            $needValue = '';
                        } else {
                            $refRelatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedRecordModel->get($fName));
                            $needValue = $refRelatedRecordModel ? $refRelatedRecordModel->getName() : '';
                        }
                    }

                    if (in_array($fieldDataType, array('email', 'documentsFolder', 'fileLocationType', 'documentsFileUpload'))) {
                        $needValue = $relatedFieldModel->get($fName);
                    }

                    // For special types - prevent nl2br html code uitype = 10 will return <a> tag
                    if (in_array($fieldDataType, array('owner'))) {
                        $needValue = $this->getTextFromHtmlTag($needValue, 'a');
                    }

                    $tokens[$relatedModuleName][$fToken] = $needValue;
                }

//                $export[] = $relatedModuleName;
            }
        }

        // Merge data
        foreach ($tokens as $tModuleName => $tFields) {
            foreach ($tFields as $k => $f) {
                $f = nl2br($f);
                $content = str_replace($k, $f, $content);
            }
        }

        return $content;
    }

    /**
     * @param string $content
     * @param string $tagName
     * @return string
     */
    public function getTextFromHtmlTag($content, $tagName)
    {
        include_once 'include/simplehtmldom/simple_html_dom.php';

        $html = str_get_html($content);
        // If not found table block
        if (!$html) {
            return $content;
        }

        $text = $content;

        if (count($html->find($tagName)) > 0) {
            $text = '';

            foreach ($html->find($tagName) as $element) {
                $text .= $element->plaintext;
            }
        }

        return $text;
    }

    /**
     * @param string $content
     * @return string
     */
    public function mergeCustomFunctions($content)
    {
        if (is_numeric(strpos($content, '[CUSTOMFUNCTION|'))) {
            include_once 'include/simplehtmldom/simple_html_dom.php';
            foreach (glob('modules/QuotingTool/resources/functions/*.php') as $cfFile) {
                include_once $cfFile;
            }

            $data = array();
            $data['[CUSTOMFUNCTION|'] = '<customfunction>';
            $data['|CUSTOMFUNCTION]'] = '</customfunction>';
            $content = $this->mergeBodyHtml($content, $data);
            $domBodyHtml = str_get_html($content);

            if (is_array($domBodyHtml->find('customfunction'))) {
                foreach ($domBodyHtml->find('customfunction') as $element) {
                    $params = $this->splitParametersFromText(trim($element->plaintext));
                    $function_name = $params[0];
                    unset($params[0]);
                    $result = call_user_func_array($function_name, $params);
                    $element->outertext = $result;
                }

                $content = $domBodyHtml->save();
            }
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array $data
     * @return string
     */
    public function mergeBodyHtml($content, $data)
    {
        if (!empty($data)) {
            $content = str_replace(array_keys($data), $data, $content);
            return $content;
        }

        return null;
    }

    /**
     * @param $content
     * @param $keys_values - Example: array('$custom_proposal_link$' => 'modules/QuotingTool/proposal/index.php?record=1')
     * @return string
     */
    public function mergeCustomTokens($content, $keys_values)
    {
        foreach ($keys_values as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    /**
     * It like mergeCustomTokens, but maybe custom it later
     * @param $content
     * @return string
     */
    public function mergeEscapeCharacters($content)
    {
        $escapeCharacters = $this->getEscapeCharactersFromString($content);

        foreach ($escapeCharacters as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    /**
     * @param string $text
     * @return array
     */
    public function splitParametersFromText($text)
    {
        $params = array();
        $end = false;

        do {
            if (strstr($text, '|')) {
                if ($text[0] == '"') {
                    $delimiter = '"|';
                    $text = substr($text, 1);
                } elseif (substr($text, 0, 6) == '&quot;') {
                    $delimiter = '&quot;|';
                    $text = substr($text, 6);
                } else {
                    $delimiter = '|';
                }
                list($params[], $text) = explode($delimiter, $text, 2);
            } else {
                $params[] = $text;
                $end = true;
            }
        } while (!$end);

        return $params;
    }

    /**
     * @param string $moduleName
     * @param string $referenceFieldname
     * @return array
     */
    public function getOtherFields($moduleName, $referenceFieldname = null)
    {
        $inventoryModules = getInventoryModules();

        // Init by common field block
        $blocks = array(
            array(  // Block item
                'id' => 0,  // Option
                'name' => 'LBL_COMMON_FIELDS',  // Required
                'label' => vtranslate('LBL_COMMON_FIELDS', self::MODULE_NAME),  // Option
                'fields' => array(  // Fields - Required
                    array(  // Field item
                        'id' => 0,  // Option
                        'name' => 'crmid',  // Required
                        'label' => vtranslate('crmid', self::MODULE_NAME),  // Option
                        'token' => $this->convertFieldToken('crmid', $moduleName, $referenceFieldname),
                        'datatype' => 'integer'
                    )
                ),
            ),
        );

        if (in_array($moduleName, $inventoryModules)) {
            // Product detail block
            $blocks[] = array(
                'name' => 'LBL_ITEM_DETAILS',
                'fields' => array(
                    array(
                        'name' => 'sequence_no',
                        'datatype' => 'integer'
                    ),
                    array(
                        'name' => 'totalAfterDiscount',
                        'datatype' => 'currency'
                    ),
                    array(
                        'name' => 'netPrice',
                        'datatype' => 'currency'
                    ),
                    array(
                        'name' => 'unitPrice',
                        'datatype' => 'currency'
                    )
                )
            );
        }

        if (in_array($moduleName, $this->specialItemDetails)) {
            // Product detail block
            $blocks[] = array(
                'name' => 'Line Item Detail',
                'fields' => array(
                    array(
                        'name' => 'block',
                        'label' => vtranslate($moduleName . '_Block', $moduleName),
                        'datatype' => 'string'
                    ),
                )
            );
        }

        return $this->fillBlockFields($moduleName, $blocks);
    }

    /**
     * @param Vtiger_Module_Model $moduleModel
     * @param bool $isRelatedModule
     * @return array
     * @throws Exception
     */
    public function parseModule($moduleModel, $isRelatedModule = false)
    {
        $moduleId = $moduleModel->getId();
        $moduleName = $moduleModel->getName();
        $moduleFields = $moduleModel->getFields();

        $moduleInfo = array();
        $moduleInfo['id'] = $moduleId;
        $moduleInfo['name'] = $moduleName;

//        // Fix duplicate module label
//        if (array_key_exists($moduleModel->getId(), $this->modulesWithSuffix) && !$isRelatedModule) {
//            $moduleModel->set('label', $this->modulesWithSuffix[$moduleModel->getId()]);
//        }

        $moduleInfo['label'] = vtranslate($moduleModel->get('label'), $moduleName);
        $otherFields = ($isRelatedModule) ?
            $this->getOtherFields($moduleName, $moduleModel->get('reference_fieldname')) : $this->getOtherFields($moduleName);
        $moduleInfo['fields'] = $otherFields;

        /** @var Vtiger_Field_Model $moduleField */
        foreach ($moduleFields as $moduleField) {
            if ($moduleField->isViewableInDetailView()) {
                $fieldInfo = array();
                $fieldInfo['id'] = $moduleField->getId();
                $fieldInfo['uitype'] = $moduleField->get('uitype');
                $fieldInfo['datatype'] = $moduleField->getFieldDataType();
                $fieldInfo['name'] = $moduleField->getName();
                $fieldInfo['label'] = vtranslate($moduleField->get('label'), $moduleName);
                $referenceFieldname = $isRelatedModule ? $moduleModel->get('reference_fieldname') : null;
                $fieldInfo['token'] = $this->convertFieldToken($moduleField->getName(), $moduleName, $referenceFieldname);
                /** @var Vtiger_Block_Model $block */
                $block = $moduleField->get('block');
                $fieldInfo['block'] = array(
                    'id' => $block->id,
                    'name' => $block->label,
                    'label' => vtranslate($block->label, $moduleName)
                );

                // Flag
                $ignore = false;
                // Get inject fields from config
                $injectFields = $this->injectFields;

                if (isset($injectFields[$moduleName])) {
                    $ignoreBlocks = $injectFields[$moduleName];

                    foreach ($ignoreBlocks as $ignoreBlock => $ignoreFields) {
                        if ($block->label != $ignoreBlock) {
                            // Not match block
                            continue;
                        }

                        foreach ($ignoreFields as $ignoreField) {
                            if ($ignoreField == '*' || $ignoreField == $fieldInfo['name']) {
                                $ignore = true;
                                /**
                                 * Break multi loops
                                 * @link http://php.net/manual/en/control-structures.break.php
                                 */
                                break 2;
                            }
                        }
                    }
                }

                if (!$ignore) {
                    $moduleInfo['fields'][] = $fieldInfo;
                }
            }
        }

        return $moduleInfo;
    }

    /**
     * @param Vtiger_Module_Model $currentModuleModel
     * @param bool $isReference
     * @return array
     * @throws Exception
     */
    public function getRelatedModules($currentModuleModel, $isReference = false)
    {
        $relatedModules = array();
        $ignoreModules = array('Users');
        $referenceFields = $currentModuleModel->getFieldsByType('reference');
        $currentModuleName = $currentModuleModel->getName();

        /** @var Vtiger_Field_Model $fieldModel */
        foreach ($referenceFields as $fieldName => $fieldModel) {
            $referenceModules = $fieldModel->getReferenceList();

            if (count($referenceModules) == 2 && $referenceModules[0] == 'Campaigns') {
                // Fix when conflict between Users & Campaigns modules
                unset($referenceModules[0]);
            }

            $fieldLabel = $fieldModel->get('label');
            $translatedFieldLabel = vtranslate($fieldLabel, $currentModuleName);

            foreach ($referenceModules as $relatedModule) {
                if (in_array($relatedModule, $ignoreModules)) {
                    // ignore fields
                    continue;
                }

                $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);

                if ($isReference) {
                    /**
                     * Prevent cache object
                     * @var Vtiger_Module_Model $relatedModuleModel
                     * @link http://stackoverflow.com/questions/1190026/php-copying-array-elements-by-value-not-by-reference#answer-11569790
                     */
                    $relatedModuleModel = clone $relatedModuleModel;

                    // New label with reference field
                    $relatedModuleModel->set('label', $translatedFieldLabel
                        . ' (' . vtranslate($relatedModuleModel->get('label'), $relatedModuleModel->getName()) . ')');
                    // custom fields
                    $relatedModuleModel->set('reference_fieldid', $fieldModel->getId());
                    $relatedModuleModel->set('reference_fieldname', $fieldModel->getName());
                }

                $relatedModules[] = $relatedModuleModel;
            }
        }

        return $relatedModules;
    }

    /**
     * @param string $content
     * @param string $header
     * @param string $footer
     * @param string $name
     * @param string $page_format
     * @param string $path
     * @param array $styles
     * @param array $scripts
     * @param bool $escapeForm
     * @return string - path file
     */
    public function createPdf($content, $header = '', $footer = '', $name, $page_format = 'A4', $path = 'storage/QuotingTool/', 
                              $styles = array(), $scripts = array(), $escapeForm = true)
    {
        global $site_URL;

        // Check dir
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true))
                return '';
        }

        require_once('modules/QuotingTool/resources/mpdf/mpdf.php');
        include_once 'include/simplehtmldom/simple_html_dom.php';

        // Process if escape form
        if ($escapeForm) {
            // Replace <input> to <span>
            $contentDom = str_get_html($content);
            // with input type
            // @link http://www.w3schools.com/tags/tag_input.asp
            $inputs = $contentDom->find('input');

            if (is_array($inputs)) {
                foreach ($inputs as $k => $input) {
                    $value = $input->value;
                    $class = $input->class;
                    $style = $input->style;
                    $type = $input->type;

                    if ($type == 'text') {
                        $replaceBy = '<div class="' . $class . ' uneditable-input" style="' . $style . '">' . $value . '</div>';
                        $inputs[$k]->outertext = $replaceBy;
                    } else if ($type == 'checkbox') {
                        $inputs[$k]->disabled = 'disabled';
                    }
                }
            }

            $content = $contentDom->save();
        }

        $content = '<div id="quoting_tool-body">' . $content . '</div>';
        // Fix generate image from server
        $site = rtrim($site_URL, '/');
        $content = str_replace($site . '/test/upload/images/', 'test/upload/images/', $content);
        $content = str_replace($site . '/modules/QuotingTool/resources/images/', 'modules/QuotingTool/resources/images/', $content);

        $pdfMode = 'utf-8';
        $mpdf = new mPDF($pdfMode, $page_format);
        $mpdf->useActiveForms = true;

        // mpdf styles
        if (!$styles) {
            $styles = array();
        }
        $styles = array_merge($styles, array(
            'modules/QuotingTool/resources/styles.css',
            'modules/QuotingTool/resources/pdf.css'
        ));

        foreach ($styles as $css) {
            $stylesheet = file_get_contents($css);
            $mpdf->WriteHTML($stylesheet, 1);
        }

        // mpdf scripts
        if (!$scripts) {
            $scripts = array();
        }

        foreach ($scripts as $js) {
            $cScript = file_get_contents($js);
            $mpdf->WriteHTML($cScript, 1);
        }

        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($content);
        // put file for debug
//        file_put_contents('modules/QuotingTool/PDFcontent.html', $content);

        $fullFileName = $path . $name;
        $mpdf->Output($fullFileName, 'F');

        return $fullFileName;
    }

    /**
     * @param $content
     * @param $module
     * @param $record
     * @return mixed|string
     */
    public function parseTokens($content, $module, $record)
    {
        if ($content == '') {
            // empty content
            return '';
        }

        // Parse tokens
        $tokens = $this->getFieldTokenFromString($content);

        // Parse content
        $content = $this->mergeBlockTokens($tokens, $record, $content);
        $content = $this->mergeGuestBlockTokens($tokens, $record, $content);
        $content = $this->mergeCustomBlockTokens($tokens, $record, $content);
        // Merge tokens
        $content = $this->mergeTokens($tokens, $record, $content, $module);
        $content = $this->mergeCustomFunctions($content);
        // Escape special characters.
        $content = $this->mergeEscapeCharacters($content);

        return $content;
    }

    /**
     * @param string $moduleName
     */
    public function installWorkflows($moduleName)
    {
        global $adb;

        foreach ($this->workflows as $name => $label) {
            $dest1 = "modules/com_vtiger_workflow/tasks/{$name}.inc";
            $source1 = "modules/{$moduleName}/workflow/{$name}.inc";

            $file_exist1 = false;
            $file_exist2 = false;

            if (file_exists($dest1)) {
                $file_exist1 = true;
            } else {
                if (copy($source1, $dest1)) {
                    $file_exist1 = true;
                }
            }

            $dest2 = "layouts/vlayout/modules/Settings/Workflows/Tasks/{$name}.tpl";
            $source2 = "layouts/vlayout/modules/{$moduleName}/taskforms/{$name}.tpl";

            $templatepath = "modules/{$moduleName}/taskforms/{$name}.tpl";

            if (file_exists($dest2)) {
                $file_exist2 = true;
            } else {
                if (copy($source2, $dest2)) {
                    $file_exist2 = true;
                }
            }

            if ($file_exist1 && $file_exist2) {
                $sql1 = "SELECT * FROM com_vtiger_workflow_tasktypes WHERE tasktypename = ?";
                $result1 = $adb->pquery($sql1, array($name));

                if ($adb->num_rows($result1) == 0) {
                    // Add workflow task
                    $taskType = array(
                        'name' => $name,
                        'label' => vtranslate($label, $moduleName),
                        'classname' => $name,
                        'classpath' => $source1,
                        'templatepath' => $templatepath,
                        'modules' => array(
                            'include' => array(),
                            'exclude' => array()
                        ),
                        'sourcemodule' => $moduleName
                    );
                    VTTaskType::registerTaskType($taskType);
                }
            }
        }
    }

    /**
     * @param string $moduleName
     */
    public function removeWorkflows($moduleName)
    {
        global $adb;

        $sql1 = "DELETE FROM com_vtiger_workflow_tasktypes WHERE sourcemodule = ?";
        $adb->pquery($sql1, array($moduleName));

        foreach ($this->workflows as $name => $label) {
            $likeTasks = '%:"' . $name . '":%';
            $sql2 = "DELETE FROM com_vtiger_workflowtasks WHERE task LIKE ?";
            $adb->pquery($sql2, array($likeTasks));

            $incFile = "modules/com_vtiger_workflow/tasks/{$name}.inc";
            $tplFile = "layouts/vlayout/modules/Settings/Workflows/Tasks/{$name}.tpl";

            // Remove include file if exist
            if (file_exists($incFile)) {
                unlink($incFile);
            }

            // Remove template file if exist
            if (file_exists($tplFile)) {
                unlink($tplFile);
            }

        }
    }

    /**
     * @param $name
     * @param string $extension
     * @param string $hash
     * @return string
     */
    public function makeUniqueFile($name, $extension = 'pdf', $hash = '')
    {
        $replace = '_';
        $hash = $hash . time();
        $name = preg_replace("/[^A-Za-z0-9]/", $replace, $name);
        $file = $name . $replace . $hash . '.' . $extension;

        return $file;
    }

    /**
     * @param mixed $focus
     * @param string $name
     * @param string $path
     * @return bool
     */
    public function createAttachFile($focus, $name, $path = 'storage/QuotingTool/')
    {
        global $adb, $current_user;

        $timestamp = date('Y-m-d H:i:s');
        $ownerid = $focus->column_fields['assigned_user_id'];
        $id = $adb->getUniqueID('vtiger_crmentity');
        $filetype = 'application/pdf';

        $sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES(?, ?, ?, ?, ?, ?, ?)";
        $params1 = array($id, $current_user->id, $ownerid, 'Emails Attachment', $focus->column_fields['description'], $timestamp, $timestamp);
        $adb->pquery($sql1, $params1);
        $sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) VALUES(?, ?, ?, ?, ?)";
        $params2 = array($id, $name, $focus->column_fields['description'], $filetype, $path);
        $adb->pquery($sql2, $params2);
        $sql3 = "INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)";
        $adb->pquery($sql3, array($focus->id, $id));

        return $id;
    }

    /**
     * @param $relatedProducts
     * @param $queryProducts
     * @return mixed
     */
    private function mergeRelatedProductWithQueryProduct($relatedProducts, $queryProducts)
    {
        $data = array();
        $queryProductKey = 0;

        // Remove all numerical keys
        // @link http://www.codingforums.com/php/66190-remove-all-numerical-keys.html
        foreach ($queryProducts as $p => $array) {
            foreach ($array as $key => $val) {
                if (is_numeric($key))
                    unset($queryProducts[$p][$key]);
            }
        }

        // Merge
        foreach ($relatedProducts as $k => $product) {
            $data[$k] = array();

            foreach ($product as $fieldName => $fieldValue) {
                if ($fieldName == 'final_details') {
                    continue;
                }

                // @link http://php.net/rtrim
                $myFieldName = rtrim($fieldName, $k);
                $data[$k][$myFieldName] = $fieldValue;
            }

            $data[$k] = array_merge($data[$k], $queryProducts[$queryProductKey++]);
        }

        return $data;
    }

    /**
     * Fn - formatNumber
     * @param $string_number
     * @return float
     */
    public function formatNumber($string_number)
    {
        global $current_user;

        $grouping = $current_user->currency_grouping_separator;
        $decimal = $current_user->currency_decimal_separator;
        $no_of_decimals = $current_user->no_of_currency_decimals;

        return number_format($string_number, $no_of_decimals, $decimal, $grouping);
    }

    static function resetValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array(static::MODULE_NAME));
        $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array(static::MODULE_NAME, '0'));
    }

    static function removeValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array(static::MODULE_NAME));
    }

    /**
     * Copy from SelectEmailFields.php
     *
     * @param string $moduleName
     * @param int $recordId
     * @return array
     */
    public function getEmailList($moduleName, $recordId)
    {
        $email_field_list = array();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        $accountId = 0;
        $contactId = 0;

        if ($moduleName == 'Quotes' || $moduleName == 'Invoice' || $moduleName == 'Contacts' || $moduleName == 'SalesOrder') {
            $accountId = $recordModel->get('account_id');
            $contactId = $recordModel->get('contact_id');
        } elseif ($moduleName == 'HelpDesk') {
            $accountId = $recordModel->get('parent_id');
            $contactId = $recordModel->get('contact_id');
        } elseif ($moduleName == 'Potentials') {
            $accountId = $recordModel->get('related_to');
            $contactId = $recordModel->get('contact_id');
        } elseif ($moduleName == 'Project') {
            $accountId = $recordModel->get('linktoaccountscontacts');
            if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                $contactId = $accountId;
                $accountId = 0;
            }
        } elseif ($moduleName == 'ProjectTask' && QuotingToolUtils::isRecordExists($recordModel->get('projectid'))) {
            $projectRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('projectid'));
            $accountId = $projectRecordModel->get('linktoaccountscontacts');
            if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                $contactId = $accountId;
                $accountId = 0;
            }
        } elseif ($moduleName == 'ServiceContracts') {
            $accountId = $recordModel->get('sc_related_to');
            if ($accountId && getSalesEntityType($accountId) != 'Accounts') {
                $contactId = $accountId;
                $accountId = 0;
            }
        } elseif ($moduleName == 'Opportunities' || $moduleName == 'Estimates') {
            $OpportunitiesRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $contactId = $OpportunitiesRecordModel->get('contact_id');
            $accountId = $OpportunitiesRecordModel->get('related_to');
        } elseif ($moduleName == 'Orders') {
            $OrdersRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $contactId = $OrdersRecordModel->get('orders_contacts');
            $accountId = $OrdersRecordModel->get('orders_account');
        }

        // With only contactid
        if ($moduleName == 'PurchaseOrder') {
            $contactId = $recordModel->get('contact_id');
        }

        if ($moduleName == 'Contacts') {
            $contactId = $recordId;
        }

        if ($moduleName == 'Accounts') {
            $accountId = $recordId;
        }

        if ($accountId && QuotingToolUtils::isRecordExists($accountId)) {
            $accountModuleModel = Vtiger_Module_Model::getInstance('Accounts');
            $accountRecordModel = Vtiger_Record_Model::getInstanceById($accountId);
            $emailFields = $accountModuleModel->getFieldsByType('email');
            $emailFields = array_keys($emailFields);
            $i = 1;
            foreach ($emailFields as $fieldname) {
                $emailValue = $accountRecordModel->get($fieldname);
                if ($emailValue) {
                    $email_field_list[$i . "||" . $accountId . "||" . $emailValue] = $accountRecordModel->getDisplayName() . " ($emailValue)";
                    $i++;
                }
            }
        }

        if ($contactId && QuotingToolUtils::isRecordExists($contactId)) {
            $contactModuleModel = Vtiger_Module_Model::getInstance('Contacts');
            $contactRecordModel = Vtiger_Record_Model::getInstanceById($contactId);
            $emailFields = $contactModuleModel->getFieldsByType('email');
            $emailFields = array_keys($emailFields);
            $i = 1;
            foreach ($emailFields as $fieldname) {
                $emailValue = $contactRecordModel->get($fieldname);
                if ($emailValue) {
                    $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $contactRecordModel->getDisplayName() . " ($emailValue)";
                    $i++;
                }
            }
        }

        // Primitive email on other modules
        if ($moduleName == 'Leads' || $moduleName == 'Accounts' && QuotingToolUtils::isRecordExists($recordId)) {
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $emailFields = $moduleModel->getFieldsByType('email');
            $emailFields = array_keys($emailFields);
            $i = 1;

            foreach ($emailFields as $fieldname) {
                $emailValue = $recordModel->get($fieldname);

                if ($emailValue) {
                    $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                    $i++;
                }
            }
        }

        if ($moduleName == 'PurchaseOrder') {
            // Reference with vendor
            $vendorId = $recordModel->get('vendor_id');

            if (QuotingToolUtils::isRecordExists($vendorId)) {
                $moduleModel = Vtiger_Module_Model::getInstance('Vendors');
                $recordModel = Vtiger_Record_Model::getInstanceById($vendorId);
                $emailFields = $moduleModel->getFieldsByType('email');
                $emailFields = array_keys($emailFields);
                $i = 1;

                foreach ($emailFields as $fieldname) {
                    $emailValue = $recordModel->get($fieldname);

                    if ($emailValue) {
                        $email_field_list[$i . "||" . $contactId . "||" . $emailValue] = $recordModel->getDisplayName() . " ($emailValue)";
                        $i++;
                    }
                }
            }
        }

        return $email_field_list;
    }

    public static function getConfig()
    {
        global $site_URL, $current_user;
        $data = array();

        $base = $site_URL;
        $base = trim($base, '/');
        $data['base'] = $base . '/';
        $data['date_format'] = $current_user->date_format;
        $data['hour_format'] = $current_user->hour_format;
        $data['start_hour'] = $current_user->start_hour;
        $data['end_hour'] = $current_user->end_hour;
        $data['time_zone'] = $current_user->time_zone;
        $data['dayoftheweek'] = $current_user->dayoftheweek;
        $userRecordModel = Users_Record_Model::getCurrentUserModel();
        $data['default_agentid'] = $userRecordModel->getPrimaryOwnerForUser();

        return $data;
    }

    public function getModulesWithSuffix()
    {
        $allModules = Vtiger_Module_Model::getAll();
        $data = array();
        $exist = array();
        $existName = array();

        // Add suffix is (ModuleName)
        /** @var Vtiger_Module_Model $module */
        foreach ($allModules as $module) {
            $id = $module->getId();
            $moduleName = $module->get('name');
            $moduleLabel = vtranslate($module->get('name'), $module->get('label'));

            if (in_array($moduleLabel, $exist)) {
                $data[$id] = $moduleLabel . " ({$moduleName})";

                // Duplicate on before
                foreach ($exist as $k => $i) {
                    if ($i == $moduleLabel) {
                        $data[$k] = $i . " ({$existName[$k]})";
                    }
                }
            } else {
                $data[$id] = $moduleLabel;
                $exist[$id] = $moduleLabel;
                $existName[$id] = $moduleName;
            }
        }

        return $data;
    }

    public function getModules()
    {
        $data = array();
        $inventoryModules = getInventoryModules();
        $arrConfigs = $this->getQuotingToolConfigs();
        $enableModuleWithRelated = $arrConfigs['enableModuleWithRelated'];
        $guestBlocks = $arrConfigs['guestBlocks'];

        foreach ($enableModuleWithRelated as $module => $relatedModules) {
            $moduleModel = Vtiger_Module_Model::getInstance($module);

            if (!$moduleModel) {
                // Ignore if invalid module
                continue;
            }

            $moduleInfo = $this->parseModule($moduleModel);
            $relations = $this->getRelatedModules($moduleModel, true);
            $moduleInfo['related_modules'] = array();
            $moduleInfo['final_details'] = array();
            $moduleInfo['guest_blocks'] = array();

            // related_modules
            foreach ($relations as $relation) {
                if (!$relation || !in_array($relation->name, $relatedModules)) {
                    // Ignore if invalid module
                    continue;
                }
                $moduleInfo['related_modules'][] = $this->parseModule($relation, true);
            }

            // picklist
            $moduleInfo['picklist'] = $this->getPicklistFields($module);

            // final_details
            if (in_array($module, $inventoryModules)) {
                $moduleInfo['final_details'] = $this->getTotalFields($module);
            }

            // guest_blocks
            if (array_key_exists($module, $guestBlocks)) {
                $moduleInfo['guest_blocks'] = $this->getGuestBlockFields($module, $guestBlocks[$module]);
            }

            $data[] = $moduleInfo;
        }

        return $data;
    }

    public function getCustomFunctions()
    {
        $data = array();
        $ready = false;
        $function_name = "";
        $function_params = array();
        $functions = array();

        $files = glob('modules/QuotingTool/resources/functions/*.php');
        foreach ($files as $file) {
            $filename = $file;
            $source = fread(fopen($filename, "r"), filesize($filename));
            $tokens = token_get_all($source);
            foreach ($tokens as $token) {
                if (is_array($token)) {
                    if ($token[0] == T_FUNCTION)
                        $ready = true;
                    elseif ($ready) {
                        if ($token[0] == T_STRING && $function_name == "")
                            $function_name = $token[1];
                        elseif ($token[0] == T_VARIABLE)
                            $function_params[] = $token[1];
                    }
                } elseif ($ready && $token == "{") {
                    $ready = false;
                    $functions[$function_name] = $function_params;
                    $function_name = "";
                    $function_params = array();
                }
            }
        }

        foreach ($functions as $funcName => $funcParams) {
            $strPrams = implode("|", $funcParams);
            $customFunction = trim($funcName . "|" . str_replace("$", "", $strPrams), "|");
            $data[] = array(
                'token' => '[CUSTOMFUNCTION|' . $customFunction . '|CUSTOMFUNCTION]',
                'name' => $funcName,
                'label' => vtranslate($funcName, self::MODULE_NAME),
            );
        }

        return $data;
    }

    public function getCustomFields()
    {
        $customBlock = array(
            'name' => 'LBL_CUSTOM_BLOCK',
            'fields' => array(
                array(
                    'name' => 'custom_proposal_link',
                ),
                array(
                    'name' => 'custom_user_signature'
                )
            )
        );

        $blocks = array();
        $blocks[] = $customBlock;
        $data = $this->fillBlockFields('', $blocks);

        return $data;
    }

    /**
     * @param string $rel_module
     * @return array
     */
    public function getPicklistFields($rel_module)
    {
        $data = array();
        $moduleModel = Vtiger_Module_Model::getInstance($rel_module);
        $fields = $moduleModel->getFields();

        /**
         * @var string $name
         * @var Vtiger_Field_Model $field
         */
        $exist = array();
        foreach ($fields as $name => $field) {
            $fieldModel = Vtiger_Field_Model::getInstance($field->get('id'));
            if ($fieldModel->isViewableInDetailView()) {
                $fieldDataType = $fieldModel->getFieldDataType();

                if ($fieldDataType != 'picklist' && $fieldDataType != 'multipicklist') {
                    continue;
                }

                $picklist = $fieldModel->getPicklistValues();
                $fieldLabel = vtranslate($fieldModel->get('label'), $rel_module);
                $fieldName = $fieldModel->get('name');
                if (in_array($fieldLabel, $exist)) {
                    $resultLabel = $fieldLabel . " ({$fieldName})";
                    foreach ($data as $key => $value) {
                        if ($value["label"] == $fieldLabel) {
                            $data[$key]["label"] = $fieldLabel . " ({$value["name"]})";
                        }
                    }
                } else {
                    $exist[$fieldName] = $fieldLabel;
                    $resultLabel = $fieldLabel;
                }
                if (!empty($picklist)) {
                    $data[] = array(
                        'id' => $fieldModel->get('id'),
                        'name' => $fieldModel->get('name'),
                        'label' => $resultLabel,
                        'values' => $picklist
                    );
                }
            }
        }
        return $data;
    }

    /**
     * @param string $moduleName
     * @param array $blocks
     * @return array
     */
    public function fillBlockFields($moduleName, $blocks)
    {
        $data = array();

        foreach ($blocks as $block) {
            $blockId = isset($block['id']) ? $block['id'] : 0;
            $blockName = $block['name'];
            $blockLabel = isset($block['label']) ? $block['label'] : vtranslate($blockName, self::MODULE_NAME);
            $fields = $block['fields'];

            foreach ($fields as $field) {
                $fieldId = isset($field['id']) ? $field['id'] : 0;
                $uitype = isset($field['uitype']) ? $field['uitype'] : 0;
                $datatype = isset($field['datatype']) ? $field['datatype'] : 'text';
                $fieldName = $field['name'];
                $fieldLabel = isset($field['label']) ? $field['label'] : vtranslate($fieldName, self::MODULE_NAME);
                $token = isset($field['token']) ? $field['token'] : $this->convertFieldToken($fieldName, $moduleName);

                $data[] = array(
                    'id' => $fieldId,
                    'name' => $fieldName,
                    'uitype' => $uitype,
                    'datatype' => $datatype,
                    'label' => $fieldLabel,
                    'token' => $token,
                    'block' => array(
                        'id' => $blockId,
                        'name' => $blockName,
                        'label' => $blockLabel,
                    )
                );
            }
        }

        return $data;
    }

    /**
     * @param string $moduleName
     * @return array
     */
    public function getTotalFields($moduleName = null)
    {
        $data = array();
        // Hardcode from: modules/Inventory/views/Detail.php
        $totalBlock = array(
            'name' => 'LBL_TOTAL_BLOCK',
            'fields' => array(
                array(
                    'name' => 'hdnSubTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_ITEMS_TOTAL', $moduleName)
                ),
                array(
                    'name' => 'discountTotal_final',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_DISCOUNT', $moduleName)
                ),
                array(
                    'name' => 'shipping_handling_charge',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_SHIPPING_AND_HANDLING_CHARGES', $moduleName)
                ),
                array(
                    'name' => 'preTaxTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_PRE_TAX_TOTAL', $moduleName)
                ),
                array(
                    'name' => 'tax_totalamount',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_TAX', $moduleName)
                ),
                array(
                    'name' => 'shtax_totalamount',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_TAX_FOR_SHIPPING_AND_HANDLING', $moduleName)
                ),
                array(
                    'name' => 'adjustment',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_ADJUSTMENT', $moduleName)
                ),
                array(
                    'name' => 'grandTotal',
                    'datatype' => 'currency',
                    'label' => vtranslate('LBL_GRAND_TOTAL', $moduleName)
                )
            )
        );

        if ($moduleName) {
            $blocks = array();
            $blocks[] = $totalBlock;
            $data = $this->fillBlockFields($moduleName, $blocks);
        } else {
            $inventoryModules = getInventoryModules();

            foreach ($inventoryModules as $moduleName) {
                $blocks = array();
                $blocks[] = $totalBlock;
                $data = array_merge($data, $this->fillBlockFields($moduleName, $blocks));
            }
        }

        return $data;
    }

    /**
     * @param string $moduleName
     * @param array $blocks
     * @return array
     */
    public function getGuestBlockFields($moduleName, $blocks = null)
    {
        foreach ($blocks as $b => $block) {
            // translate
            $blocks[$b]['label'] = vtranslate($block['label'], $moduleName);
            // all fields of block
            $fields = $block['fields'];

            foreach ($fields as $f => $field) {
                $blocks[$b]['fields'][$f]['label'] = vtranslate($field['label'], $moduleName);
                $blocks[$b]['fields'][$f]['token'] = $this->convertFieldToken($field['name'], $moduleName);
            }
        }

        return $blocks;
    }

    /**
     * Get configurations from database
     * @return array
     */
    public function getQuotingToolConfigs()
    {
        global $adb;

        $arrConfigs = array();
        $specialGuestBlocks = $this->specialGuestBlocks;
        $results = $adb->pquery("SELECT * from `vtiger_quotingtool_configurations` WHERE `isactive`= 1", array());

        if ($adb->num_rows($results) > 0) {
            $enableModules = array();
            $relatedModules = array();
            $guestBlocks = array();

            while ($rowConfig = $adb->fetchByAssoc($results)) {
                $moduleName = $rowConfig['module'];
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $allRelatedModules = $this->getRelatedModules($moduleModel);
                $related_modules = array();

                /** @var Vtiger_Record_Model $rm */
                foreach ($allRelatedModules as $rm) {
                    $related_modules[] = $rm->getName();
                }

                $guestBlocksDetail = array();
                $enableModules[] = $moduleName;

                // Get guest blocks
                $iblock = 1;
                $rsGuestModules = $adb->pquery("select DISTINCT guestmodule from vtiger_guestmodulerel WHERE hostmodule=? AND active=1;", array($moduleName));

                if ($adb->num_rows($rsGuestModules) > 0) {
                    while ($rowGuestModule = $adb->fetch_array($rsGuestModules)) {
                        $block_fields = array();
                        $guestBlockDetail = array();
                        $guestBlock = $rowGuestModule['guestmodule'];
                        $guestBlockModel = Vtiger_Module_Model::getInstance($guestBlock);

                        if ($guestBlockModel && $guestBlockModel->isActive()) {
                            $guestBlockFieldModels = $guestBlockModel->getFields();
                            $ifield = 1;

                            /**
                             * @var string $fieldName
                             * @var Vtiger_Field_Model $fieldModel
                             */
                            foreach ($guestBlockFieldModels as $fieldName => $fieldModel) {
                                if ($fieldModel->isViewableInDetailView()) {
                                    $fieldDetail = array(
                                        'id' => $ifield,
                                        'name' => $fieldName,
                                        'label' => vtranslate($fieldModel->get('label'), $guestBlock),
                                    );
                                    $block_fields[] = $fieldDetail;
                                    $ifield++;
                                }
                            }

                            $guestBlockDetail['id'] = $iblock;
                            $guestBlockDetail['name'] = $guestBlock;
                            $guestBlockDetail['label'] = vtranslate($guestBlock, $guestBlock);
                            $guestBlockDetail['fields'] = $block_fields;
                            $guestBlocksDetail[] = $guestBlockDetail;
                            $iblock++;
                        }
                    }
                }

                // Include custom guest blocks
                if ($moduleName == 'Orders' || $moduleName == 'Opportunities') {
                    $guestBlockDetail = $specialGuestBlocks['ParticipatingAgents'];
                    $guestBlockDetail['id'] = $iblock;
                    $guestBlocksDetail[] = $guestBlockDetail;
                } elseif ($moduleName == 'OrdersTask') {
                    /** @var OrdersTask_Record_Model $ordersTaskRecordModel */
                    $ordersTaskRecordModel = Vtiger_Record_Model::getCleanInstance('OrdersTask');
                    $extraBlocks = $ordersTaskRecordModel::getExtraBlockConfig();
                    $guestBlockModel = Vtiger_Module_Model::getInstance('OrdersTask');
                    $guestModules = array_keys($extraBlocks);

                    foreach ($guestModules as $guestBlock) {
                        $blockModel = Vtiger_Block_Model::getInstance($guestBlock, $guestBlockModel);
                        if (!$blockModel) {
                            continue;
                        }
                        $guestBlockFieldModels = $blockModel->getFields();
                        $block_fields          = [];
                        $ifield                = 1;
                        /**
                         * @var string             $fieldName
                         * @var Vtiger_Field_Model $fieldModel
                         */
                        foreach ($guestBlockFieldModels as $fieldName => $fieldModel) {
                            $fieldDetail    = [
                                'id'    => $ifield,
                                'name'  => $fieldName,
                                'label' => vtranslate($fieldModel->get('label'), 'OrdersTask'),
                            ];
                            $block_fields[] = $fieldDetail;
                            $ifield++;
                        }
                        $guestBlockDetail['id']     = $iblock;
                        $guestBlockDetail['name']   = $guestBlock;
                        $guestBlockDetail['label']  = vtranslate($guestBlock, 'OrdersTask');
                        $guestBlockDetail['fields'] = $block_fields;
                        $guestBlocksDetail[]        = $guestBlockDetail;
                        $iblock++;
                    }
                }

                $relatedModules[$moduleName] = $related_modules;
                $guestBlocks[$moduleName] = $guestBlocksDetail;
            }

            $arrConfigs['enableModules'] = $enableModules;
            $arrConfigs['enableModuleWithRelated'] = $relatedModules;
            $arrConfigs['guestBlocks'] = $guestBlocks;

        }

        return $arrConfigs;
    }
}
