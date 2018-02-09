{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<div id='ms_sync_message' class='row-fluid'>
    <div class='padding10 span11'>
        {if !$FIRSTTIME}
            <input type="hidden" id="ms_firsttime" value='no'/>
        {else}
            <input type="hidden" id="ms_firsttime" value='yes'/>
        {/if}
        <div id='ms_sync_details'></div>
        {if $STATE eq 'home'}
            {if !$FIRSTTIME}
                <p class="muted" id='ms_synctime'>
                    <small title="{$SYNCTIME}">{vtranslate('LBL_SYNCRONIZED',$MODULE_NAME)}
                        : {$SYNCTIME}</small>
                </p>
            {else}
                <p class="muted" id='ms_synctime'>
                    <small>{vtranslate('LBL_NOT_SYNCRONIZED',$MODULE_NAME)}</small>
                </p>
            {/if}
        {/if}
        <div class='row-fluid'>
            <span class='span0'>&nbsp;</span>
            <button id="ms_sync_button" class="btn btn-success span9"
                    data-url='index.php?module=Exchange&view=List&operation=sync&sourcemodule={$SOURCEMODULE}'>
                <b>{vtranslate('LBL_SYNC_BUTTON',$MODULE_NAME)}</b></button>
            {*<span class="span0">
                <i class="icon-question-sign pushDown" id="ms_popid" data-placement="right" rel="popover"></i>
            </span>*}
        </div>
        <br/>
        {*
        <div class='row-fluid {if $FIRSTTIME}hide {/if}' id="ms_removeSyncBlock">
            <span class='span0'>&nbsp;</span>
            <button id="ms_remove_sync" class="btn btn-danger span9"
                    data-url='index.php?module=Exchange&view=List&operation=removeSync&sourcemodule={$SOURCEMODULE}'>
                <b>{vtranslate('LBL_REMOVE_SYNC',$MODULE_NAME)}</b></button>
            <span class="span0">
                <i class="icon-question-sign pushDown" id="ms_removePop" data-placement="right" rel="popover"></i>
            </span>
        </div>
*}
    </div>

</div>

{if $STATE eq 'CLOSEWINDOW'}
    {if $DENY}
        <script>
            window.close();
        </script>
    {else}
        <script>
            window.opener.sync();
            window.close();
        </script>
    {/if}
{/if}

