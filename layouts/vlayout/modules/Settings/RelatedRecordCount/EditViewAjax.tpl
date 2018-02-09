{* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** *}
 
<div class="container-fluid" >
    <form class="form-inline" id="CustomView" name="CustomView" method="post" action="index.php">
        <input type=hidden name="record" id="record" value="{$RECORD_ID}" />
        <input type="hidden" name="module" value="{$MODULE_NAME}" />
        <input type="hidden" value="Settings" name="parent" />
        <input type="hidden" name="action" value="SaveSettings" />
        <input type="hidden" id="stdfilterlist" name="stdfilterlist" value=""/>
        <input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>

        <div class="row-fluid"  style="padding: 10px 0;">
            <h3 class="textAlignCenter">
                {if $RECORD_ID gt 0}
                    {vtranslate('LBL_EDIT_HEADER',$QUALIFIED_MODULE)}
                {else}
                    {vtranslate('LBL_NEW_HEADER',$QUALIFIED_MODULE)}
                {/if}
                <small aria-hidden="true" data-dismiss="modal" class="pull-right ui-condition-color-closer" style="cursor: pointer;" title="{vtranslate('LBL_MODAL_CLOSE',$QUALIFIED_MODULE)}">x</small>
            </h3>
        </div>
        <hr>
        <div class="clearfix"></div>

        <div class="listViewContentDiv row-fluid" id="listViewContents" style="height: 450px; overflow-y: auto; width: 900px;">
            <div class="row marginBottom10px vte-primary-box">
                <div class="row-fluid">
                    <div class="row marginBottom10px">
                        <div class="span4 textAlignRight">{vtranslate('LBL_MODULE_NAME',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue span6">
                            <select name="modulename" class="chzn-select">
                                {foreach item=MODULE from=$LIST_MODULES}
                                    <option value="{$MODULE.name}" {if $ENTITY.modulename eq $MODULE.name || $ACTIVE_MODULE eq $MODULE.name}selected{/if} >{$MODULE.tablabel}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row marginBottom10px vte-related-module-box">
                        <div class="span4 textAlignRight">{vtranslate('LBL_RELATED_MODULE_NAME',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue span6">
                            <select name="related_modulename" class="chzn-select">
                                {foreach item=MODULE from=$LIST_RELATED_MODULES}
                                    <option value="{$MODULE.modulename}" {if $ENTITY.related_modulename eq $MODULE.modulename || $ACTIVE_RELATED_MODULE eq $MODULE.modulename}selected{/if} >{$MODULE.tablabel}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row marginBottom10px">
                        <div class="span4 textAlignRight">{vtranslate('LBL_COLOR',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue span6">
                            <input type="text" name="color" value="{$ENTITY.color}" class="input-large" style="background-color: {$ENTITY.color}"/>
                        </div>
                    </div>
                    <div class="row marginBottom10px">
                        <div class="span4 textAlignRight">
                            {vtranslate('LBL_LABEL',$QUALIFIED_MODULE)}
                        </div>
                        <div class="fieldValue span6">
                            <input type="text" name="label" value="{$ENTITY.label}" class="input-large" />
                            <br />
                            <span class="alert alert-info">{vtranslate('LBL_LABEL_DESC',$QUALIFIED_MODULE)}</span>
                        </div>
                    </div>
                    <div class="row marginBottom10px">
                        <div class="span4 textAlignRight">{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue span6">
                            <select name="status" class="chzn-select">
                                <option value="Active" {if $ENTITY.status eq 'Active'}selected{/if} >{vtranslate('LBL_STATUS_ACTIVE',$QUALIFIED_MODULE)}</option>
                                <option value="Inactive" {if $ENTITY.status eq 'Inactive'}selected{/if} >{vtranslate('LBL_STATUS_INACTIVE',$QUALIFIED_MODULE)}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
            </div>
            <div class="row marginBottom10px vte-filter-box">
                <h4 class="filterHeaders textAlignCenter">{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS', $QUALIFIED_MODULE)}</h4>
                <span class="alert alert-info">{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS_DESC',$QUALIFIED_MODULE)}</span>
                <div class="filterConditionsDiv" style="padding: 20px;">
                    <div class="row-fluid">
                        <span class="span12 vte-advancefilter">
                            {include file='AdvanceFilter.tpl'|@vtemplate_path MODULE='Vtiger'}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="filterActions row" style="padding: 10px 0;">
            <button class="btn btn-success pull-right" id="save-condition-color" type="button"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
        </div>
    </form>
</div>

