<div class="container-fluid">
    <table class="table table-bordered blockContainer showInlineTable equalSplit" style="margin:2% auto;" id="colorsettings">
        <thead>
            <tr>
                <th class="blockHeader" colspan="5">Color Settings</th>
            </tr>
        </thead>
        <tbody>
            <tr class="not_for_json">
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_ASSIGNED',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10">Assigned</span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%" colspan="2"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolorasignacion" value="{$colorasignacion}" style="cursor:pointer;background-color: {$colorasignacion};color:white;width: 20%;margin-left: 1%;"></span></div></td>
            </tr>
            <tr class="not_for_json">
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_APU',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10">APU</span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%" colspan="2"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolorapu" value="{$colorapu}" style="cursor:pointer;background-color: {$colorapu};color:white;width: 20%;margin-left: 1%;"></span></div></td>
            </tr>
            <tr class="not_for_json">
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SHORT_HAUL',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10">Short Haul</span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%" colspan="2"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolorshorthaulcolor" value="{$colorshorthaul}" style="cursor:pointer;background-color: {$colorshorthaul};color:white;width: 20%;margin-left: 1%;"></span></div></td>
            </tr>
            <tr class="not_for_json">
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_OVERFLOW',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10">Overflow</span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%" colspan="2"><div class="row-fluid"><span class="span12"><input type="text" id="pickcoloroverflow" value="{$coloroverflow}" style="cursor:pointer;background-color: {$coloroverflow};color:white;width: 20%;margin-left: 1%;"></span></div></td>
            </tr>
            {foreach from=$CP_ARRAY key=myId item=i}
            <tr>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_DAYS_TO_PUDATE',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="daytopu{$myId}" type="hidden" class="input-large nameField" name="daytopu{$myId}" value="{$i.daystopudate}"><select id="sdiastopudate{$myId}"><option value="-1">Overdue</option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option></select></span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor{$myId}" value="{$i.color}" style="cursor:pointer;background-color: {$i.color};color:white;width: 20%;margin-left: 1%;"></span></div></td>
                <td style="width:5%;padding-top: 1.5%;padding-left: 1.5%;"><i class="icon-remove"></i></td>
            </tr>
            {foreachelse}
                <tr>
                    <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_DAYS_TO_PUDATE',$MODULE_NAME)}</label></td>
                    <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="daytopu0" type="hidden" class="input-large nameField" name="daytopu0" value="1"><select id="sdiastopudate0"><option value="-1">Overdue</option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option></select></span></div></td>
                    <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                    <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor0" value="#CCCCCC" style="cursor:pointer;background-color: #CCCCCC;color:white;width: 20%;margin-left: 1%;"></span></div></td>
                    <td style="width:5%;padding-top: 1.5%;padding-left: 1.5%;"><i class="icon-remove"></i></td>
                </tr>
            {/foreach}
        </tbody>
    </table>  
    <div class="row-fluid">
        <div class="pull-right">
            <button class="btn btn-info" id="add_colorsetting"><strong>+</strong></button>
            <button class="btn btn-success" id="guardar_datos"><strong>Save</strong></button>
            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">Cancel</a>
        </div>
        <div class="clearfix"></div> 
    </div>
</div>
<script>
    {literal}
        jQuery(document).ready(function(){
            jQuery('[id^="sdiastopudate"]').each(function(){
                console.log(this);
                var nro = jQuery(this).attr('id').replace("sdiastopudate","");
                jQuery(this).find('option[value="'+jQuery('#daytopu'+nro).val()+'"]').attr('selected',true);
            });
        });
    {/literal}
</script>