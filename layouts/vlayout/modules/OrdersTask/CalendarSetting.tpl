<div class="container-fluid">
    <table class="table table-bordered blockContainer showInlineTable equalSplit" style="margin:2% auto;" id="colorspercentage">
        <thead>
            <tr>
                <!--<th class="blockHeader" colspan="5">Calendar Settings</th>-->
                <th class="blockHeader" colspan="4">Color/Percentage Settings</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_PERCENTAGE',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="percentage1" type="hidden" class="input-large nameField" name="percentage1" value="{$SETTINGS.percentage_1}"><select id="spercentage1"><option value="5">5%</option><option value="10">10%</option><option value="15">15%</option><option value="20">20%</option><option value="25">25%</option><option value="30">30%</option><option value="35">35%</option><option value="40">40%</option><option value="45">45%</option><option value="50">50%</option><option value="55">55%</option><option value="60">60%</option><option value="65">65%</option><option value="70">70%</option><option value="75">75%</option><option value="80">80%</option><option value="85">85%</option><option value="90">90%</option><option value="95">95%</option><option value="100">100%</option> </select></span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor1" value="{$SETTINGS.color_1}" style="cursor:pointer;background-color: {$SETTINGS.color_1};color:white;width: 20%;margin-left: 1%;"></span></div></td>
                <!--<td style="width:5%"><i class="icon-remove"></i></td>-->
            </tr>
            <tr>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_PERCENTAGE',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="percentage2" type="hidden" class="input-large nameField" name="percentage2" value="{$SETTINGS.percentage_2}"><select id="spercentage2"><option value="5">5%</option><option value="10">10%</option><option value="15">15%</option><option value="20">20%</option><option value="25">25%</option><option value="30">30%</option><option value="35">35%</option><option value="40">40%</option><option value="45">45%</option><option value="50">50%</option><option value="55">55%</option><option value="60">60%</option><option value="65">65%</option><option value="70">70%</option><option value="75">75%</option><option value="80">80%</option><option value="85">85%</option><option value="90">90%</option><option value="95">95%</option><option value="100">100%</option> </select></span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor2" value="{$SETTINGS.color_2}" style="cursor:pointer;background-color: {$SETTINGS.color_2};color:white;width: 20%;margin-left: 1%;"></span></div></td>
                <!--<td style="width:5%"><i class="icon-remove"></i></td>-->
            </tr>
            <tr>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_PERCENTAGE',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="percentage3" type="hidden" class="input-large nameField" name="percentage3" value="{$SETTINGS.percentage_3}"><select id="spercentage3"><option value="5">5%</option><option value="10">10%</option><option value="15">15%</option><option value="20">20%</option><option value="25">25%</option><option value="30">30%</option><option value="35">35%</option><option value="40">40%</option><option value="45">45%</option><option value="50">50%</option><option value="55">55%</option><option value="60">60%</option><option value="65">65%</option><option value="70">70%</option><option value="75">75%</option><option value="80">80%</option><option value="85">85%</option><option value="90">90%</option><option value="95">95%</option><option value="100">100%</option> </select></span></div></td>
                <td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_COLOR',$MODULE_NAME)}</label></td>
                <td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor3" value="{$SETTINGS.color_3}" style="cursor:pointer;background-color: {$SETTINGS.color_3};color:white;width: 20%;margin-left: 1%;"></span></div></td>
                <!--<td style="width:5%"><i class="icon-remove"></i></td>-->
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered blockContainer showInlineTable equalSplit" style="margin:2% auto;" id="specialdays">
        <thead>
            <tr>
                <!--<th class="blockHeader" colspan="5">Calendar Settings</th>-->
                <th class="blockHeader" colspan="4">Special Days Settings</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fieldLabel " style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_WORK_SATURDAYS',$MODULE_NAME)}</label></td>
                <td class="fieldValue " style="width:32.5%"><div class="row-fluid"><span class="span10">{if $SETTINGS.saturday_work_day eq 'Yes'}<input id="saturday" type="checkbox" class="input-large nameField" name="saturday" value="" checked>{else}<input id="saturday" type="checkbox" class="input-large nameField" name="saturday" value="">{/if}</span></div></td>
                <td class="fieldLabel " style="width:15%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_WORK_SUNDAYS',$MODULE_NAME)}</label></td>
                <td class="fieldValue " style="width:32.5%"><div class="row-fluid"><span class="span10">{if $SETTINGS.sunday_work_day eq 'Yes'}<input id="sunday" type="checkbox" class="input-large nameField" name="sunday" value="" checked>{else}<input id="sunday" type="checkbox" class="input-large nameField" name="sunday" value="">{/if}</span></div></td>
                <!--<td style="width:5%"><p></p></td>-->
            </tr>
        </tbody>
    </table>    
    <div class="row-fluid">
        <div class="pull-right">
            <!--<button class="btn btn-info" id="add_color_percentage"><strong>+</strong></button>-->
            <button class="btn btn-success" id="save_data"><strong>Save</strong></button>
            <a class="cancelLink" type="reset" onclick="javascript:window.close();">Cancel</a>
        </div>
        <div class="clearfix"></div> 
    </div>
</div>
<script>
    {literal}
        jQuery(document).ready(function(){
            jQuery('[id^="spercentage"]').each(function(){
                console.log(this);
                var nro = jQuery(this).attr('id').replace("spercentage","");
                jQuery(this).find('option[value="'+jQuery('#percentage'+nro).val()+'"]').attr('selected',true);
            });
        });
    {/literal}
</script>