{strip}
    {assign var=MODULE_MODEL  value=$RECORD_MODEL->getModule()}
    {assign var=LIST_FIELD  value=$BLOCK_SETTING.fields}
    {assign var=FIELD_COUNT  value=$LIST_FIELD|@count}
    {assign var=COLSPAN  value=$FIELD_COUNT*2+1}
    {if !empty($RECORD_ID)}
        {assign var=LIST_ITEM  value=$RECORD_MODEL->getExtraBlockFieldValues($BLOCK_LABEL)}
    {else}
        {assign var=LIST_ITEM  value=$RECORD_MODEL->getDefaultValueForBlocks($BLOCK_LABEL)}
    {/if}
    {assign var=ROW_COUNT  value=$LIST_ITEM|@count}
    {if empty($ROW_COUNT)}
        {assign var=ROW_COUNT  value=0}
    {/if}
	
    <table  class='table table-bordered blockContainer showInlineTable dynamic_table' name="{$BLOCK_LABEL}" >
        <thead>
            <tr>
                <th class='blockHeader' colspan='{$COLSPAN}'>{vtranslate($BLOCK_LABEL, $MODULE)}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="fieldLabel">
                <td colspan="{$COLSPAN}">
                    <button type="button" class="addItem">+</button>
                    <input type="hidden" name="numItem_{$BLOCK_LABEL}" value="{$ROW_COUNT}"/>
                    <button type="button" class="addItem" style="clear:right;float:right">+</button>
                </td>
            </tr>
            <tr class="defaultItem hide">
                <td class="fieldValue" style="width: 4%;">
                    <i title="Delete" class="icon-trash removeItem"></i>
                    <input type="hidden" class="default" name="itemId" value="none" />
                </td>
                {foreach item = FIELD_NAME from = $LIST_FIELD}
                    {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)}
                    {if $FIELD_NAME == 'personnel_type'}
                        {assign var=FIELD_MODEL  value=$FIELD_MODEL->set('fieldvalue','-1')}
                    {elseif $FIELD_NAME == 'vehicle_type'}
                        {assign var=FIELD_MODEL  value=$FIELD_MODEL->set('fieldvalue','Any Vehicle Type')}
                    {/if}
					
                    <td class="fieldLabel" {if $BLOCK_LABEL eq "LBL_PERSONNEL"}style="width: 20%;"{else if $BLOCK_LABEL eq "LBL_VEHICLES"}style="width: 20%;"{/if}>
                        <label class="muted">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
                    </td>
                    <td class="fieldValue" {if $BLOCK_LABEL eq "LBL_PERSONNEL"}style="width: 28%;"{else if $BLOCK_LABEL eq "LBL_VEHICLES"}style="width: 28%;"{/if}>
                        <div class="row-fluid">
                            <span class="span10 {if $BLOCK_LABEL eq 'LBL_PERSONNEL'}personnelChange{elseif $BLOCK_LABEL eq 'LBL_VEHICLES'}vehicleChange{/if}">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL IS_BASE_FIELD = true}
                            </span>
                        </div>
                    </td>
                {/foreach}
            </tr>
            {foreach from = $LIST_ITEM key=ROW_NUM item=FIELD_VALUES}
                <tr class="itemRow" data-rowno = "{$ROW_NUM}">
                    <td class="fieldValue" style="width: 4%;">
                        <i title="Delete" class="icon-trash removeItem"></i>
                        <input type="hidden" name="itemId_{$BLOCK_LABEL}" value="{if $RECORD_ID}{$ROW_NUM}{else}none{/if}" />
                        <input type="hidden" name="itemDelete_{$BLOCK_LABEL}" value="" />
                    </td>
                    {foreach item = FIELD_NAME from = $LIST_FIELD}
                        {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)->set('fieldvalue',$FIELD_VALUES[$FIELD_NAME])}
                        <td class="fieldLabel" {if $BLOCK_LABEL eq "LBL_PERSONNEL"}style="width: 20%;"{else if $BLOCK_LABEL eq "LBL_VEHICLES"}style="width: 20%;"{/if}>
                            <label class="muted">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
                        </td>
                        <td class="fieldValue" {if $BLOCK_LABEL eq "LBL_PERSONNEL"}style="width: 28%;"{else if $BLOCK_LABEL eq "LBL_VEHICLES"}style="width: 28%;"{/if}>
                            <div class="row-fluid">
                            <span class="span10 {if $BLOCK_LABEL eq 'LBL_PERSONNEL'}personnelChange{elseif $BLOCK_LABEL eq 'LBL_VEHICLES'}vehicleChange{/if}">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL}
                            </span>
                            </div>
                        </td>
                    {/foreach}
                </tr>
            {/foreach}

        </tbody>
    </table>
    </br>
{/strip}
