
{strip}
    <div class="modelContainer">
        <div class="modal-header contentsBackground">
            <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
            <h3>{vtranslate('MovePolicies', 'MovePolicies')}</h3>
        </div>
        {if $RECORD_ID > 0}
            {include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
            {include file='DetailViewTariffItems.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
            <br>
            {include file='DetailViewBlockViewNotes.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
        {else}
            <table class="emptyRecordsDiv">
                <tbody>
                <tr>
                    <td>
                        <span style="font-size: 14px">
                            {vtranslate('No Policies are set up for this account',$MODULE_NAME)}
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        {/if}
    </div>

{/strip}
