{*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
 
{strip}
<div class="listViewContentDiv" id="listViewContents" style="padding: 1%;">
    <br>
    <div class="row-fluid">
        <label class="fieldLabel span3"><strong>{vtranslate('LBL_SETTING_CUSTOMMODULE', 'VTEFavorite')} </strong></label>
        <div class="span6 fieldValue">
            <select  class="marginBottom10px select2 span3" id="selAddModule_customlist">
                {foreach item=BLOCK_MODEL from=$CLT_ALL_MODULES}
                    <option value="{$BLOCK_MODEL}" >{vtranslate($BLOCK_MODEL, $BLOCK_MODEL)}</option>
                {/foreach}
            </select>
        </div>
    </div><br>
    <div class="row-fluid">
        <label class="fieldLabel span3"><strong>{vtranslate('LBL_SETTING_CUSTOMVIEW', 'VTEFavorite')} </strong></label>
        <div class="span6 fieldValue">
            <select  class="marginBottom10px select2 span3" id="selAddView_customlist"></select>
            <button class="btn marginBottom10px " onclick="AddModule('customlist');" type="button">{vtranslate('LBL_SETTING_CUSTOMLIST_ADDMODULE', 'VTEFavorite')}</button>
        </div>
    </div>
</div>
<div id="layoutEditorContainer">
	<div class="contents tabbable ui-sortable">
		<div class="container-fluid" id="menuEditorContainer">
		   <div id="moduleBlocks">
                {foreach item=BLOCK_MODEL from=$CLT_MODULE_CONFIG}
                    {assign var=BLOCK_ID value=$BLOCK_MODEL.moduleName}
                    {assign var=CUSTOMLIST_ID value=$BLOCK_MODEL.customlistid}
                    {assign var=ACTIVE value=$BLOCK_MODEL.active}
                    {assign var=LIMITED value=$BLOCK_MODEL.limitrecord}
                    {assign var=TABLABEL value=$BLOCK_MODEL.tablabel}
                    {assign var=VIEWNAME value=$BLOCK_MODEL.cvname}
                    {assign var=FIELDS_SELECTED value=$BLOCK_MODEL.fields}
                    {assign var=MFIELDS value=$CLT_ALL_FIELDS[$BLOCK_ID]}
                        <div id="block_{$BLOCK_ID}" class="editFieldsTable block_{$BLOCK_ID} marginBottom10px border1px blockSortable" data-block-id="{$BLOCK_ID}" data-id="{$CUSTOMLIST_ID}"  style="border-radius: 4px 4px 0px 0px;background: white;">
                            <div class="row-fluid layoutBlockHeader">
                                <div class="blockLabel span5 padding10 marginLeftZero">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                        <td style="width:300px" >
                                            <img class="alignMiddle" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;<input {if $ACTIVE}checked{/if} type="checkbox" onchange="activeModuleFavorite('{$BLOCK_ID}',this.checked)" />
                                            <strong>{vtranslate($TABLABEL, $TABLABEL)}</strong>
                                            &nbsp;&gt;&nbsp;<strong>{vtranslate($VIEWNAME, $TABLABEL)}</strong>
                                        </td>
                                        <td>
                                            {vtranslate('LBL_SETTING_CUSTOMLIMIT', 'VTEFavorite')}<input style="width:25px"  type="text" value="{$LIMITED}" id="limitedRecord_customlist_{$BLOCK_ID}_{$CUSTOMLIST_ID}" maxlength="3" class ="number" />
                                        </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="span6 marginLeftZero" style="float:right !important;">
                                <div class="pull-right btn-toolbar blockActions" style="margin: 4px;">
                                    <div class="btn-group">
                                    <button class="btn btn-success addCustomField" onclick="saveFields('customlist','{$BLOCK_ID}','{$CUSTOMLIST_ID}')" type="button"><strong>{vtranslate('LBL_SETTING_SAVE', 'VTEFavorite')}</strong></button>
                                    <button class="btn btn-success addCustomField" onclick="deleModule('customlist','{$CUSTOMLIST_ID}')" type="button"><strong>{vtranslate('LBL_SETTING_REMOVE', 'VTEFavorite')}</strong></button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div id="fields_{$BLOCK_ID}" class="blockFieldsList blockFieldsSortable  row-fluid " style="min-height: 27px">
                                <select name="sortable1" data-placeholder="Add new field" id="fieldSelectElement_customlist_{$BLOCK_ID}_{$CUSTOMLIST_ID}" class="select2 span12" multiple="" data-validation-engine="validate[required]" >
                                    <optgroup label='Select field' id="optgroup_{$BLOCK_ID}" name="optgroup">
                                        {foreach item=BLOCK_FIELD from=$MFIELDS}
                                            {assign var=BLOCK_FIELD_NAME value=$BLOCK_FIELD->get('name')}
                                                {if in_array( $BLOCK_FIELD_NAME, $FIELDS_SELECTED)}
                                                {elseif $BLOCK_FIELD->get('presence') neq 1}
                                                    <option value="{$BLOCK_FIELD_NAME}">{vtranslate({$BLOCK_FIELD->get('label')},{$BLOCK_ID})}</option>
                                                {/if}
                                        {/foreach}
                                        {foreach item=BLOCK_FIELD_VALUE from=$FIELDS_SELECTED}
                                            {$BLOCK_FIELD_VALUE}
                                            {foreach item=BLOCK_FIELD from=$MFIELDS}
                                                {if $BLOCK_FIELD_VALUE eq $BLOCK_FIELD->get('name')}
                                                    <option value="{$BLOCK_FIELD_VALUE}" selected >{vtranslate({$BLOCK_FIELD->get('label')},{$BLOCK_ID})}</option>
                                                    {break}
                                                {/if}
                                            {/foreach}
                                        {/foreach}
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        </BR>
                {/foreach}
			</div>
		</div>
	</div>
</div>
{/strip}
 
