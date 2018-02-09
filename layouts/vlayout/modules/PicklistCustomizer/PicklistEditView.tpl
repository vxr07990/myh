<div class='container-fluid editViewContainer' style="min-height:500px; height:100vh;">
    <div class="contentHeader row-fluid">
        {assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
    </div>
    <div>
        <table style="width:300px;">
            <tr>
                <td style="width: 50px"><span class="redColor">*</span>Owner</td>
                <td>
                    <select id = "agentmanager_id" name="agentmanager_id" class="chzn-select" style="width: 100%">
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
                </td>
            </tr>
            <tr>
                <td style="width: 50px"><span class="redColor">*</span>Module</td>
                <td>
                    {assign var=PICKLIST_MODULES value=PicklistCustomizer_Module_Model::getPickListModules()}
                      <select id="module_select" name="module_select" class="chzn-select" style="width: 100%">
                            <option selected value="default">
                                <p> --Select Module--</p>
                            </option>
                        {foreach from=$PICKLIST_MODULES key=TAB_LABEL item=TAB_NAME}
                            <option value="{$TAB_LABEL}">{$TAB_NAME}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td style="width: 50px"><span class="redColor">*</span>Field</td>
                <td>
                    <select id="field_select" name="field_select" class="chzn-select" style="width: 100%">
                        <option selected value="default">
                            <p></p>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

    </div>
    <div class="listViewContentDiv" id="listViewContents" style="padding: 1%;">
        <br>


        <div id="modulePickListValuesContainer">
        </div>
    </div>
</div>