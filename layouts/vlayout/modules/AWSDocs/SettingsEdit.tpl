
<div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
    <h3 style="padding-bottom: 1em;text-align: center">{vtranslate('AWS_SETTINGS', $MODULE)}</h3>
    
    <form class="form-horizontal" id="VGSBackups" name="VGSBackups" method="post" action="index.php">
        <input type="hidden" name="module" value="{$MODULE}" />
        <input type="hidden" name="action" value="SaveSettings" />
        <div>
            <h4 style="margin-top: 4em;margin-bottom: 0.5em;">{vtranslate('AWS_SETTINGS', $MODULE)}</h4>
            

            <a class="cancelLink pull-right padding1per" type="reset" onClick="javascript:window.history.back()">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            <button class="btn btn-success pull-right" id="customViewSubmit" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
             {if $CREDENTIALS_ERROR eq true}
            <div class="alert alert-error" style="width: 60%;">
                {vtranslate('Could not connect to AWS. Please check your credentials', $MODULE)}
            </div>
            {/if}
            
             {if $BUCKET_ERROR eq true}
            <div class="alert alert-error" style="width: 60%;">
                {vtranslate('Bucket does not exist. Please check your bucket name', $MODULE)}
            </div>
            {/if}
            
   
            
            <table class="table table-bordered table-condensed themeTableColor" style="margin-top: 1em;">
                <thead>
                    <tr class="blockHeader">
                        <th colspan="2" class="mediumWidthType"><span class="alignMiddle">{vtranslate('Please insert your amazon credentials.', $MODULE)}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_KEY', $MODULE)}</label></td><td style="border-left: none;"><input type="text" name="aws_key" value="{$AWS_SETTINGS.aws_key}"></input></td></tr>
                    <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_SECRET', $MODULE)}</label></td><td style="border-left: none;"><input type="text" name="aws_secret" value="{$AWS_SETTINGS.aws_secret}"></input></td></tr>
                    <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_BUCKET', $MODULE)}</label></td><td style="border-left: none;"><input type="text" name="aws_bucket" value="{$AWS_SETTINGS.aws_bucket}"></input></td></tr>

                </tbody>
            </table>
        </div>

    </form>

</div>


