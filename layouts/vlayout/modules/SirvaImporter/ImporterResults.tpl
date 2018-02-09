<div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
    <h3 style="padding-bottom: 1em;text-align: center">{vtranslate('Sirva Importer Module', $MODULE)}</h3>

    <div>
        <h4 style="margin-top: 4em;margin-bottom: 0.5em;">{vtranslate('Results of your importing process', $MODULE)}</h4>

        <table class="table table-bordered table-condensed themeTableColor" style="margin-top: 1em;">
            <thead>
                <tr class="blockHeader">
                    <th colspan="4" class="mediumWidthType"><span class="alignMiddle">{vtranslate('Sirva Importer Results', $MODULE)}</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('Rows Uploaded', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        {$UPLOADED}


                    </td>
                </tr>
                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('Success', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        {$SUCCESS}
                    </td>
                </tr>

                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('Failed', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        {$FAILED}
                    </td>
                </tr>

                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('Log File Link', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        {if $LOG neq ''}
                        <a target="_blank" href="{$LOG}">{vtranslate('Download Log file', $MODULE)}</a>
                        {else}
                            No error log file.
                        {/if}    
                    </td>
                </tr>

            </tbody>
        </table>

    </div>
</div>
