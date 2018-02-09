{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
<div id="toggleButton" class="toggleButton" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
	<i id="tButtonImage" class="{if $LEFTPANELHIDE neq '1'}icon-chevron-right {else} icon-chevron-left{/if}"></i>
</div>&nbsp
    <div style="padding-left: 15px;">
        <form id="exportForm" class="form-horizontal row-fluid" method="post" action="index.php">
            <input type="hidden" name="module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="action" value="ExportData" />
            <input type="hidden" name="viewname" value="{$VIEWID}" />
            <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
            
            <div class="row-fluid">
                <div class="span">&nbsp;</div>
                <div class="span8">
                    <h4>{vtranslate('LBL_EXPORT_RECORDS',$MODULE)}</h4>
                    <div class="well exportContents marginLeftZero">
                        <div class="row-fluid">
                            <div class="row-fluid" style="height:30px">
                                <div class="span6 textAlignRight row-fluid">
                                    <div class="span8">{$QTY} records where selected to export.&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="textAlignCenter">
                        <button class="btn btn-success" type="submit"><strong>{vtranslate($MODULE, $MODULE)}&nbsp;{vtranslate($SOURCE_MODULE, $MODULE)}</strong></button>
                        <a class="cancelLink" type="reset" onclick='window.history.back()'>{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{/strip}
