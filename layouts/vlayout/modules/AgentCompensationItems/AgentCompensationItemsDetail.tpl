{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="AgentCompensationItemsTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate('LBL_AGENTCOMPENSATION_DISTRIBUTION', 'AgentCompensationGroup')}</th>
        </tr>
        </thead>
    </table>
    <div class="AgentCompensationItemsList" data-rel-module="AgentCompensationItems">
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <input type="hidden" name="numAgentItems" value="{($GROUPING_ITEMS|@count)}"/>
        {foreach from=$GROUPING_ITEMS item=ITEM_RECORD_MODEL name=related_records_block}
            {assign var=ROWNO value=$smarty.foreach.related_records_block.iteration}
            {assign var=FIELDS_LIST value=$ITEM_MODULE_MODEL->getFields()}
            {if $ITEM_RECORD_MODEL->get('agcomitem_name') eq 'Transportation' || $ITEM_RECORD_MODEL->get('agcomitem_name') eq 'Transportation - Other'}
                <div class="AgentCompensationItemsRecords"  data-row-no="{$ROWNO}" data-id = "{$ITEM_RECORD_MODEL->getId()}">
                    <table class="table table-bordered equalSplit detailview-table">
                        <thead>
                        <tr>
                            <th class="blockHeader" colspan="4">
                                &nbsp;&nbsp;
                                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ITEM_RECORD_MODEL->getId()}>
                                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ITEM_RECORD_MODEL->getId()}>
                                    <span class="AgentCompensationItemsTitle">&nbsp;&nbsp;
                                        {$ITEM_RECORD_MODEL->get('agcomitem_name')}
                                    </span>
                                <input type="hidden" name="itemsid_{$ROWNO}" value="{$ITEM_RECORD_MODEL->getId()}">
                                <input type="hidden" name="agcomitem_name_{$ROWNO}" value="{$ITEM_RECORD_MODEL->get('agcomitem_name')}">

                                {assign var=BOOKERDISTRIBUTION_FIELD value=$ITEM_RECORD_MODEL->getField('agcomitem_bookerdistribution')}
                                {assign var=ORIGINDISTRIBUTION_FIELD value=$ITEM_RECORD_MODEL->getField('agcomitem_origindistribution')}
                                {assign var=HAULINGDISTRIBUTION_FIELD value=$ITEM_RECORD_MODEL->getField('agcomitem_haulingdistribution')}
                                {assign var=GENERAL_OFFICEDISTRIBUTION_FIELD value=$ITEM_RECORD_MODEL->getField('agcomitem_general_officedistribution')}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="fieldLabel {$WIDTHTYPE}">{vtranslate($BOOKERDISTRIBUTION_FIELD->get('label'), 'AgentCompensationItems')}</td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {if $ITEM_RECORD_MODEL}
                                    {assign var=BOOKERDISTRIBUTION_FIELD value=$BOOKERDISTRIBUTION_FIELD->set('fieldvalue',$ITEM_RECORD_MODEL->get('agcomitem_bookerdistribution'))}
                                    {assign var=CUSTOM_FIELD_NAME value="agcomitem_bookerdistribution"|cat:"_"|cat:$ROWNO}
                                    {assign var=BOOKERDISTRIBUTION_FIELD value=$BOOKERDISTRIBUTION_FIELD->set('name',$CUSTOM_FIELD_NAME)}
                                    {assign var=BOOKERDISTRIBUTION_FIELD value=$BOOKERDISTRIBUTION_FIELD->set('noncustomname','agcomitem_bookerdistribution')}
                                {/if}
                                <span class="value" data-field-type="{$BOOKERDISTRIBUTION_FIELD->getFieldDataType()}" style="white-space:normal;" >
                                    {include file=vtemplate_path($BOOKERDISTRIBUTION_FIELD->getUITypeModel()->getDetailViewTemplateName(),'AgentCompensationItems') FIELD_MODEL=$BOOKERDISTRIBUTION_FIELD USER_MODEL=$USER_MODEL MODULE='AgentCompensationItems' RECORD=$ITEM_RECORD_MODEL}
                                </span>
                            </td>
                            <td class="fieldLabel {$WIDTHTYPE}">{vtranslate($ORIGINDISTRIBUTION_FIELD->get('label'), 'AgentCompensationItems')}</td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {if $ITEM_RECORD_MODEL}
                                    {assign var=ORIGINDISTRIBUTION_FIELD value=$ORIGINDISTRIBUTION_FIELD->set('fieldvalue',$ITEM_RECORD_MODEL->get('agcomitem_origindistribution'))}
                                    {assign var=CUSTOM_FIELD_NAME value="agcomitem_origindistribution"|cat:"_"|cat:$ROWNO}
                                    {assign var=ORIGINDISTRIBUTION_FIELD value=$ORIGINDISTRIBUTION_FIELD->set('name',$CUSTOM_FIELD_NAME)}
                                    {assign var=ORIGINDISTRIBUTION_FIELD value=$ORIGINDISTRIBUTION_FIELD->set('noncustomname','agcomitem_origindistribution')}
                                {/if}
                                <span class="value" data-field-type="{$ORIGINDISTRIBUTION_FIELD->getFieldDataType()}" style="white-space:normal;">
                                    {include file=vtemplate_path($ORIGINDISTRIBUTION_FIELD->getUITypeModel()->getDetailViewTemplateName(),'AgentCompensationItems') FIELD_MODEL=$ORIGINDISTRIBUTION_FIELD USER_MODEL=$USER_MODEL MODULE='AgentCompensationItems' RECORD=$ITEM_RECORD_MODEL}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel {$WIDTHTYPE}">{vtranslate($HAULINGDISTRIBUTION_FIELD->get('label'), 'AgentCompensationItems')}</td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {if $ITEM_RECORD_MODEL}
                                    {assign var=HAULINGDISTRIBUTION_FIELD value=$HAULINGDISTRIBUTION_FIELD->set('fieldvalue',$ITEM_RECORD_MODEL->get('agcomitem_haulingdistribution'))}
                                    {assign var=CUSTOM_FIELD_NAME value="agcomitem_haulingdistribution"|cat:"_"|cat:$ROWNO}
                                    {assign var=HAULINGDISTRIBUTION_FIELD value=$HAULINGDISTRIBUTION_FIELD->set('name',$CUSTOM_FIELD_NAME)}
                                    {assign var=HAULINGDISTRIBUTION_FIELD value=$HAULINGDISTRIBUTION_FIELD->set('noncustomname','agcomitem_haulingdistribution')}
                                {/if}
                                <span class="value" data-field-type="{$HAULINGDISTRIBUTION_FIELD->getFieldDataType()}" style="white-space:normal;" >
                                    {include file=vtemplate_path($HAULINGDISTRIBUTION_FIELD->getUITypeModel()->getDetailViewTemplateName(),'AgentCompensationItems') FIELD_MODEL=$HAULINGDISTRIBUTION_FIELD USER_MODEL=$USER_MODEL MODULE='AgentCompensationItems' RECORD=$ITEM_RECORD_MODEL}
                                </span>
                            </td>
                            <td class="fieldLabel {$WIDTHTYPE}">{vtranslate($GENERAL_OFFICEDISTRIBUTION_FIELD->get('label'), 'AgentCompensationItems')}</td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {if $ITEM_RECORD_MODEL}
                                    {assign var=GENERAL_OFFICEDISTRIBUTION_FIELD value=$GENERAL_OFFICEDISTRIBUTION_FIELD->set('fieldvalue',$ITEM_RECORD_MODEL->get('agcomitem_general_officedistribution'))}
                                    {assign var=CUSTOM_FIELD_NAME value="agcomitem_general_officedistribution"|cat:"_"|cat:$ROWNO}
                                    {assign var=GENERAL_OFFICEDISTRIBUTION_FIELD value=$GENERAL_OFFICEDISTRIBUTION_FIELD->set('name',$CUSTOM_FIELD_NAME)}
                                    {assign var=GENERAL_OFFICEDISTRIBUTION_FIELD value=$GENERAL_OFFICEDISTRIBUTION_FIELD->set('noncustomname','agcomitem_general_officedistribution')}
                                {/if}
                                <span class="value" data-field-type="{$GENERAL_OFFICEDISTRIBUTION_FIELD->getFieldDataType()}" style="white-space:normal;">
                                    {include file=vtemplate_path($GENERAL_OFFICEDISTRIBUTION_FIELD->getUITypeModel()->getDetailViewTemplateName(),'AgentCompensationItems') FIELD_MODEL=$GENERAL_OFFICEDISTRIBUTION_FIELD USER_MODEL=$USER_MODEL MODULE='AgentCompensationItems' RECORD=$ITEM_RECORD_MODEL}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            {else}
                <div class="AgentCompensationItemsRecords"  data-row-no="{$ROWNO}" data-id = "{$ITEM_RECORD_MODEL->getId()}">
                    <table class="table table-bordered equalSplit detailview-table">
                        <thead>
                        <tr>
                            <th class="blockHeader" colspan="4">
                                &nbsp;&nbsp;
                                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ITEM_RECORD_MODEL->getId()}>
                                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ITEM_RECORD_MODEL->getId()}>
                        <span class="AgentCompensationItemsTitle">&nbsp;&nbsp;
                            {$ITEM_RECORD_MODEL->get('agcomitem_name')}
                        </span>
                                <input type="hidden" name="itemsid_{$ROWNO}" value="{$ITEM_RECORD_MODEL->getId()}">

                                {assign var=DISTRIBUTION_FIELD value=$ITEM_RECORD_MODEL->getField('agcomitem_distribution')}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="fieldLabel {$WIDTHTYPE}">{vtranslate($DISTRIBUTION_FIELD->get('label'), 'AgentCompensationItems')}</td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {if $ITEM_RECORD_MODEL}
                                    {assign var=DISTRIBUTION_FIELD value=$DISTRIBUTION_FIELD->set('fieldvalue',$ITEM_RECORD_MODEL->get('agcomitem_distribution'))}
                                    {assign var=CUSTOM_FIELD_NAME value="agcomitem_distribution"|cat:"_"|cat:$ROWNO}
                                    {assign var=DISTRIBUTION_FIELD value=$DISTRIBUTION_FIELD->set('name',$CUSTOM_FIELD_NAME)}
                                    {assign var=DISTRIBUTION_FIELD value=$DISTRIBUTION_FIELD->set('noncustomname','agcomitem_distribution')}
                                {/if}
                                <span class="value" data-field-type="{$DISTRIBUTION_FIELD->getFieldDataType()}" style="white-space:normal;" >
                                    {include file=vtemplate_path($DISTRIBUTION_FIELD->getUITypeModel()->getDetailViewTemplateName(),'AgentCompensationItems') FIELD_MODEL=$DISTRIBUTION_FIELD USER_MODEL=$USER_MODEL MODULE='AgentCompensationItems' RECORD=$ITEM_RECORD_MODEL}
                                </span>
                            </td>
                            <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            {/if}
        {/foreach}
    </div>
{/strip}
