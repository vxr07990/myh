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
    <div class="detailViewInfo">
        <table class="table table-bordered equalSplit detailview-table" style="table-layout:fixed">
            {foreach item=FIELD_MODEL key=FIELD_NAME from=$RECORD_STRUCTURE['TOOLTIP_FIELDS'] name=fieldsCount}
                {if $smarty.foreach.fieldsCount.index % 2 == 0}<tr>{/if}
                    <td class="fieldLabel wideWidthType" nowrap>
                        <label class="muted">{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
                    </td>
                    <td class="fieldValue wideWidthType">
                        <span class="value">
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                        </span>
                    </td>
                    {if $smarty.foreach.fieldsCount.index % 2 != 0}</tr>{/if}
                {/foreach}
                {if $smarty.foreach.fieldsCount.index is not even}</tr>{/if}
            {if count($EXTRA_ROWS) gt 0}
                <tr>
                    <td colspan="4">{vtranslate('Estimate Information',$MODULE)}</td>
                </tr>
            {/if}
            {foreach key=ROW_NRO item=EXTRA from=$EXTRA_ROWS name=otroloop}
                {if $smarty.foreach.otroloop.index is even}<tr>{/if}
                    <td class="fieldLabel wideWidthType" nowrap>
                        <label class="muted">{$EXTRA[0]}</label>
                    </td>
                    <td>
                        <span class="value" data-field-type="reference">{$EXTRA[1]}</span>
                    </td>
                    {if $smarty.foreach.otroloop.index is not even}</tr>{/if}
                {/foreach}
                {if $smarty.foreach.otroloop.index is even}</tr>{/if}
            {if count($PARTICIPANT_ROWS) gt 0}
                <tr>
                    <td colspan="4">{vtranslate('Parcitipant Agent Information',$MODULE)}</td>
                </tr>
            {/if}
            {foreach key=ROW_NUM item=PARTICIPANT from=$PARTICIPANT_ROWS name=otherloop}
                {if $smarty.foreach.otherloop.index is even}<tr>{/if}
                    <td class="fieldLabel wideWidthType" nowrap>
                        {*assign var=PICKLIST_VALUES value=['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent']*}
                        {assign var=PICKLIST_VALUES value=ParticipatingAgents_Module_Model::getParticipantPicklistValues()}
                        <label class="muted">{$PICKLIST_VALUES[$PARTICIPANT['agent_type']]}</label>
                    </td>
                    <td>
                        <span class="value" data-field-type="reference">{$PARTICIPANT['agentsLink']}</span>
                    </td>
                    {if $smarty.foreach.otherloop.index is not even}</tr>{/if}
                {/foreach}
                {if $smarty.foreach.otherloop.index is even}</tr>{/if}
        </table>
    </div>
{/strip}
