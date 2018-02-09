

{*<table class="table table-bordered equalSplit detailview-table">*}
    {*<thead>*}
        {*<tr>*}
            {*<th class="blockHeader" colspan="6">*}
                {*<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" >*}
                {*<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">*}
                {*&nbsp;&nbsp;{vtranslate('Tariff Items',{$MODULE_NAME})}*}
            {*</th>*}
        {*</tr>*}
    {*</thead>*}
    {*<tbody>*}
        {*<tr>*}
            {*<td style="width: 7%; text-align: center;">{vtranslate('Item Code',{$MODULE_NAME})}</td>*}
            {*<td style="width: 23%; text-align: center;">{vtranslate('Description',{$MODULE_NAME})}</td>*}
            {*<td style="width: 15%; text-align: center;">{vtranslate('Authorization',{$MODULE_NAME})}</td>*}
            {*<td style="width: 15%; text-align: center;">{vtranslate('Authorization Limits',{$MODULE_NAME})}</td>*}
            {*<td style="width: 35%; text-align: center;">{vtranslate('Remarks',{$MODULE_NAME})}</td>*}
        {*</tr>*}
        {*{foreach item=SECTION_ITEMS key=SECTION_NAME from=$TARIFF_ITEMS}*}
            {*<tr>*}
                {*<td colspan="6">{$SECTION_NAME}</td>*}
            {*</tr>*}
            {*{foreach item=SECTION_ITEM from=$SECTION_ITEMS}*}
                {*<tr>*}
                    {*<td style="width: 5%;">{$SECTION_ITEM.item_code}</td>*}
                    {*<td>{$SECTION_ITEM.item_des}</td>*}
                    {*<td>{$SECTION_ITEM.item_auth}</td>*}
                    {*<td>{$SECTION_ITEM.item_auth_limits}</td>*}
                    {*<td>{$SECTION_ITEM.item_remarks}</td>*}
                {*</tr>*}
            {*{/foreach}    *}

        {*{/foreach}    *}


    {*</tbody>*}
{*</table>*}

<div style="margin-top:3%;">
<table class="table table-bordered equalSplit detailview-table">
    <thead>
        <tr>
            <th class="blockHeader" colspan="6">
                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" >
                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
                &nbsp;&nbsp;{vtranslate('Misc Tariff Items',{$MODULE_NAME})}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            {*<td style="width: 7%; text-align: center;">{vtranslate('Item Code',{$MODULE_NAME})}</td>*}
            <td style="width: 23%; text-align: center;">{vtranslate('Description',{$MODULE_NAME})}</td>
            <td style="width: 15%; text-align: center;">{vtranslate('Authorization',{$MODULE_NAME})}</td>
            <td style="width: 15%; text-align: center;">{vtranslate('Authorization Limits',{$MODULE_NAME})}</td>
            <td style="width: 35%; text-align: center;">{vtranslate('Remarks',{$MODULE_NAME})}</td>
        </tr>
        {foreach item=SECTION_ITEM key=SECTION_NAME from=$MISC_TARIFF_ITEMS}
             <tr>
                    {*<td style="width: 5%;">Misc Items</td>*}
                    <td>{$SECTION_ITEM.item_des}</td>
                    <td>{$SECTION_ITEM.item_auth}</td>
                    <td>{$SECTION_ITEM.item_auth_limits}</td>
                    <td>{$SECTION_ITEM.item_remarks}</td>
                </tr>
            

        {/foreach}    


    </tbody>
</table>
</div>