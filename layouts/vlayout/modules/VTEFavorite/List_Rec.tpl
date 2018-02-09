{*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <style>
        .vteFavDiv a{
            color:{$BASE_COLOR} !important;
        }
    </style>
    <div class="vteFavDiv">
        {assign var=ARR_MODULES value=$RECORDS.arrModules}
        {if not $ARR_MODULES}
            {vtranslate("No recently", "VTEFavorite")}
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
                <a href="?module=VTEFavorite&parent=Settings&view=Settings&type=recently" > Please add fields for show
                    list.</a>
            {else}
                <table class="table table-bordered listViewEntriesTable vtetable">
                    <tr>
                        {foreach from=$ARR_FIELDS item=FIELD}
                            <th>{vtranslate({$FIELD->get('label')},{$MODULE.name})}</th>
                        {/foreach}
                        <th style="width: 110px;">{vtranslate('Modified Time',$MODULE)}</th>
                    </tr>
                    {foreach from=$ARR_RECORDS item=RECORD}
                        <tr class="vteFavdropdown">
                            {foreach from=$ARR_FIELDS item=FIELD}
                                {if $FIELD->isReferenceField()}
                                    {assign var=RModule value=$FIELD->getReferenceList()}
                                    {if $RECORD['Record']->get($FIELD->get('name'))}
                                        {assign var=RRecord value=Vtiger_Record_Model::getInstanceById($RECORD['Record']->get($FIELD->get('name')),$RModule[0])}
                                        <td class="listViewEntryValue medium ">
                                            <a href="{$RRecord->getDetailViewUrl()}" >  {$RECORD['Record']->getDisplayValue($FIELD->get('name'))} </a>
                                        </td>
                                    {else}
                                        <td class="listViewEntryValue medium ">
                                            <a href="{$RECORD['Record']->getDetailViewUrl()}" > &nbsp </a></td>
                                    {/if}
                                {else}
                                    <td class="listViewEntryValue medium">
                                    {if strpos($RECORD['Record']->getDisplayValue($FIELD->get('name')), '0000')===false}
                                        {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD->getFieldInfo()))}
                                        {assign var="SPECIAL_VALIDATOR" value=$FIELD->getValidator()}
                                        {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                        <a href="{$RECORD['Record']->getDetailViewUrl()}" > {$RECORD['Record']->getDisplayValue($FIELD->get('name'))} </a>
                                    {/if}
                                    </td>
                                {/if}
                            {/foreach}
                            {*{assign var=temp value=$RECORD['Record']['module']['field']}

                            {foreach from=$temp item=FIELD}
                                {$FIELD->get('name')}
                            {/foreach}*}
                            <td align="right" style="text-align: left;width: 150px;">
                                <div style="padding-top:5px;">
                                    {assign var=dateFormat value=$USER_MODEL->get('date_format')}
                                    {if $dateFormat == "dd-mm-yyyy"}
                                        {assign var=dateFormat value='d-m-Y H:i:s'}
                                    {elseif $dateFormat == "mm-dd-yyyy"}
                                        {assign var=dateFormat value='m-d-Y H:i:s'}
                                    {else}
                                        {assign var=dateFormat value='Y-m-d H:i:s'}
                                    {/if}
                                    {$RECORD['Metadata']['update']|date_format:$dateFormat}
                                    <span class="vteFavdropdown-content" style="left:0px;">
                                     <i onclick="window.location.href ='?module={$RECORD['Metadata']['module']}&view=Edit&record={$RECORD['Metadata']['record']}';"
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

