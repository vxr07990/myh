{*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="vteFavDiv">
        {assign var=ARR_MODULES value=$RECORDS.arrModules}
        {if not $ARR_MODULES}
            <br/>{vtranslate("No favorite", "VTEFavorite")}<br/>
            <a href="?module=VTEFavorite&parent=Settings&view=Settings"> List needs to be configured. Click here to configure</a>
        {/if}
        {foreach from=$ARR_MODULES item=MODULE}
            {assign var=ARR_RECORDS value=$MODULE.arrRecords}
            {assign var=ARR_FIELDS value=$MODULE.arrFields}
            <div class="container-fluid">
                <div class="widget_header row-fluid">
                    <div class="span4">
                        <h3>{vtranslate({$MODULE.name}, {$MODULE.name})}</h3>
                    </div>
                </div>
            </div>
            {if ! $ARR_FIELDS}
                <a href="?module=VTEFavorite&parent=Settings&view=Settings"> Please add fields for show list.</a>
            {else}
                <table class="table table-bordered listViewEntriesTable vtetable">
                    <tr>
                        {foreach from=$ARR_FIELDS item=FIELD}
                            {if $FIELD->get('label') eq "Full Name"}
                                <th>{vtranslate({$FIELD->get('label')},{$MODULE.name})}</th>
                            {/if}
                        {/foreach}
                        {foreach from=$ARR_FIELDS item=FIELD}
                            {if $FIELD->get('label') neq "Full Name"}
                                <th>{vtranslate({$FIELD->get('label')},{$MODULE.name})}</th>
                            {/if}
                        {/foreach}
                        <th style="width: 110px;"></th>
                    </tr>
                    {foreach from=$ARR_RECORDS item=RECORD}
                        <tr class="vteFavdropdown" id="tr_{$RECORD['Metadata']['id']}">
                            {if $MODULE.name eq 'Contacts' || $MODULE.name eq 'Leads'}
                                {assign var=fullName value=$RECORD['Metadata']['label']}
                            {/if}
                            {foreach from=$ARR_FIELDS item=FIELD}
                                {if $FIELD->get('name') eq 'fullname'}
                                    <td class="listViewEntryValue medium ">
                                        <font color="{$BASE_COLOR}" >{$fullName}</font>
                                    </td>
                                {/if}
                            {/foreach}
                            {foreach from=$ARR_FIELDS item=FIELD}
                                {if $FIELD->get('name') eq 'fullname'}
                                {elseif $FIELD->isReferenceField()}
                                    {assign var=RModule value=$FIELD->getReferenceList()}
                                    {if $RECORD['Record']->get($FIELD->get('name'))}
                                        {assign var=RRecord value=Vtiger_Record_Model::getInstanceById($RECORD['Record']->get($FIELD->get('name')),$RModule[0])}
                                        <td class="listViewEntryValue medium ">
                                            <a href="{$RRecord->getDetailViewUrl()}">  {$RECORD['Record']->getDisplayValue($FIELD->get('name'))} </a>
                                        </td>
                                    {else}
                                        <td class="listViewEntryValue medium ">
                                            <a href="{$RECORD['Record']->getDetailViewUrl()}"> &nbsp </a></td>
                                    {/if}
                                {else}
                                    <td class="listViewEntryValue medium">
                                        {if strpos($RECORD['Record']->getDisplayValue($FIELD->get('name')), '0000')===false}
                                            {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD->getFieldInfo()))}
                                            {assign var="SPECIAL_VALIDATOR" value=$FIELD->getValidator()}
                                            {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                            <a href="{$RECORD['Record']->getDetailViewUrl()}"> {$RECORD['Record']->getDisplayValue($FIELD->get('name'))} </a>
                                        {/if}
                                    </td>
                                {/if}
                            {/foreach}

                            <td align="right" style="text-align: right;">
                                <div style="padding-top:4px;width: 120px;">
                                    <div class="vteFavStars  pull-right" style="width:{$RECORD['Metadata']['stars']*16}px">
                                    </div>
                                    <span class="vteFavdropdown-content">
										<i onclick="delRecord('favorite',{$RECORD['Metadata']['id']});" title="Delete"
                                           class="icon-trash alignMiddle"></i>
										&nbsp;<i onclick="window.location.href ='?module={$RECORD['Metadata']['module']}&view=Edit&record={$RECORD['Metadata']['record']}';"
                                                 class="icon-pencil" title="Edit"></i>
										</span>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            {/if}
        {/foreach}
    </div>
{/strip}