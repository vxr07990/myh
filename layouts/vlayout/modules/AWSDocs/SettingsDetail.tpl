<div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
    <h3 style="padding-bottom: 1em;text-align: center">AWS Documents Module</h3>
    

    <div>
        <h4 style="margin-top: 4em;margin-bottom: 0.5em;">{vtranslate('AWS_SETTINGS', $MODULE)}</h4>


        <button class="btn pull-right" style="margin-bottom: 0.5em;"  onclick="window.location.href = 'index.php?module=AWSDocs&view=SettingsEdit&parent=Settings'">Edit</button>

        <table class="table table-bordered table-condensed themeTableColor" style="margin-top: 1em;">
            <thead>
                <tr class="blockHeader">
                    <th colspan="2" class="mediumWidthType"><span class="alignMiddle">{vtranslate('AWS_SETTINGS', $MODULE)}</span></th>
                </tr>
            </thead>
            <tbody>
                <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_KEY', $MODULE)}</label></td><td style="border-left: none;">{$AWS_SETTINGS.aws_key}</td></tr>
                <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_SECRET', $MODULE)}</label></td><td style="border-left: none;">{$AWS_SETTINGS.aws_secret}</td></tr>
                <tr><td width="25%"><label class="muted pull-right marginRight10px">{vtranslate('AWS_BUCKET', $MODULE)}</label></td><td style="border-left: none;">{$AWS_SETTINGS.aws_bucket}</td></tr>

            </tbody>
        </table>
    </div>

</div>


