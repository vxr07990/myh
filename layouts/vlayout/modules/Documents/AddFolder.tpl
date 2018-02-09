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
    <div class="modelContainer" style='min-width:350px;'>
        <div class="modal-header contentsBackground">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>{vtranslate('LBL_ADD_NEW_FOLDER', $MODULE)}</h3>
        </div>
        <form class="form-horizontal" id="addDocumentsFolder" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="Folder" />
            <input type="hidden" name="mode" value="save" />
            <div class="modal-body">
                <div class="row-fluid">
                   <div class="control-group">
                        <label class="control-label">
                            <span class="redColor">*</span>
                            {vtranslate('LBL_OWNER', $MODULE)}
                        </label>
                        <div class="controls" style="position:absolute">
                            <select data-validator='{Zend_Json::encode([['name'=>'Owner']])}' data-validation-engine="validate[required, funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" id = "agentmanager_id" name="agentmanager_id" class="chzn-select" style="width: 100%">
                                <option selected value="default">
                                    <p> --Select Owner--</p>
                                </option>
                                <optgroup label="Agents">
                                {foreach from=$LIST_AGENTMANAGER key=AGENT_ID item=AGENT_NAME}
                                    {assign var=agentRecordModel value=Vtiger_Record_Model::getInstanceById($AGENT_ID, 'AgentManager')}
                                    {assign var=displayValue value=$agentRecordModel->get('agency_name')|cat:'  ('|cat:$agentRecordModel->get('agency_code')|cat:')'}
                                    <option value="{$AGENT_ID}">{$displayValue}</option>
                                {/foreach}
                                <optgroup label="Vanlines">
                                {foreach from=$LIST_VANLINES key=AGENT_ID item=AGENT_NAME}
                                    {assign var=vanlineRecordModel value=Vtiger_Record_Model::getInstanceById($AGENT_ID, 'VanlineManager')}
                                    {assign var=displayValue value=$vanlineRecordModel->get('vanline_name')}
                                    <option value="{$AGENT_ID}">{$displayValue}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">
                            <span class="redColor">*</span>
                            {vtranslate('LBL_FOLDER_NAME', $MODULE)}
                        </label>
                        <div class="controls">
                            <input class="span3" data-validator='{Zend_Json::encode([['name'=>'FolderName']])}' data-validation-engine="validate[required, funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" id="documentsFolderName" name="foldername" class="span12" type="text" value=""/>
                        </div>
                    </div>
                    <!-- 
                    <div class="control-group">
                        <label class="control-label">
                            {vtranslate('LBL_FOLDER_DESCRIPTION', $MODULE)}
                        </label>
                        <div class="controls">
                            <textarea rows="1" class="input-xxlarge fieldValue span3" name="folderdesc" id="description"></textarea>
                        </div>
                    </div>
                    -->
                </div>
            </div>
            {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
        </form>
    </div>
{/strip}