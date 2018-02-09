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
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
    {include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    {if getenv('INSTANCE_NAME') != 'graebel'}
        {* Tag Cloud block starts
        <input type="hidden" id="userId" value="{$LINKED_USER_MODEL->getId()}" />
        <table class="table table-bordered equalSplit detailview-table">
            <thead>
                <tr>
                    <th class="blockHeader" colspan="4">
                        {vtranslate('LBL_TAG_CLOUD_DISPLAY', 'Users')}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_tagCloud">
                        <label class="muted pull-right marginRight10px">
                            {vtranslate('LBL_TAG_CLOUD', 'Users')}
                        </label>
                    </td>
                    <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_tagCloud">
                        {assign var=TAG_CLOUD value=$LINKED_USER_MODEL->getTagCloudStatus()}
                        {if $TAG_CLOUD}
                            <img src={"prvPrfSelectedTick.gif"|vimage_path} alt="{vtranslate('LBL_SHOWN', 'Users')}" title="{vtranslate('LBL_SHOWN', 'Users')}" height="12" width="12">&nbsp;&nbsp;{vtranslate('LBL_SHOWN', 'Users')}
                        {else}
                            <img src={"no.gif"|vimage_path} alt="{vtranslate('LBL_HIDDEN', 'Users')}" title="{vtranslate('LBL_HIDDEN', 'Users')}" height="12" width="12">&nbsp;&nbsp;{vtranslate('LBL_HIDDEN', 'Users')}
                        {/if}
                    </td><td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                </tr>
            </tbody>
        </table>
        <br>
        {* Tag Clous block ends *}
    {/if}
{/strip}
