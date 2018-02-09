
<div class="tariff_items_table">
    <input type="hidden" value="{$ITEMS_COUNT}" name="items_count">
    <table class="table table-bordered equalSplit detailview-table">
        <thead>
            <tr>
                <th class="blockHeader" colspan="6">
                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" >
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
                    &nbsp;&nbsp;{vtranslate('Tariff Items',{$MODULE_NAME})}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-weight:700;">
                <td style="width: 5%; text-align: center;">&nbsp;</td>
                <td style="width: 7%; text-align: center;">{vtranslate('Item Code',{$MODULE_NAME})}</td>
                <td style="width: 23%; text-align: center;">{vtranslate('Description',{$MODULE_NAME})}</td>
                <td style="width: 15%; text-align: center;">{vtranslate('Authorization',{$MODULE_NAME})}</td>
                <td style="width: 15%; text-align: center;">{vtranslate('Authorization Limits',{$MODULE_NAME})}</td>
                <td style="width: 35%; text-align: center;">{vtranslate('Remarks',{$MODULE_NAME})}</td>
            </tr>
            <tr>
                <td style="" colspan="3" style="background-color: #ECECEC;">{vtranslate('Update selected rows',{$MODULE_NAME})}</td>
               
                <td style="width: 15%;">
                    <select name="mass_update_auth" class="chzn-select ">
                            <option value=""></option>
                            <option value="Authorized">Authorized</option>
                            <option value="Seek Approval">Seek Approval</option>
                            <option value="Not Authorized">Not Authorized</option>
                        </select>
                </td>
                <td style="width: 15%; text-align: center;">&nbsp;</td>
                <td style="width: 35%; text-align: center;">&nbsp;</td>
            </tr>
            
            
            {foreach item=SECTION_ITEMS key=SECTION_NAME from=$TARIFF_ITEMS}
                <tr>
                    <td colspan="6">{$SECTION_NAME}</td>
                </tr>
                {foreach item=SECTION_ITEM from=$SECTION_ITEMS}
                    <tr>

                        {if $IS_NEW eq true}
                    <input type="hidden" name="tariff_item_id_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.RatingItemID}"/> 
                    <input type="hidden" name="tariff_id_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.TariffID}"/> 
                    <input type="hidden" name="tariff_code_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.ItemCode}"/> 
                    <input type="hidden" name="tariff_des_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.Description}"/> 
                    <input type="hidden" name="tariff_section_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.SectionID}"/> 
                    <td style="width: 5%;">
                        <input class="authchecks" type="checkbox" name="checkbox" value="{$SECTION_ITEM.tmp_id}">
                    </td>
                    <td style="width: 5%;">{$SECTION_ITEM.ItemCode}</td>
                    <td>{$SECTION_ITEM.Description}</td>
                    <td>
                        <select name="items_auth_{$SECTION_ITEM.tmp_id}" class="chzn-select ">
                            <option value=""></option>
                            <option value="Authorized">Authorized</option>
                            <option value="Seek Approval">Seek Approval</option>
                            <option value="Not Authorized">Not Authorized</option>
                        </select>
                    </td>
                    <td><input style="width: 60%;" type="text" name="items_authlimit_{$SECTION_ITEM.tmp_id}" value=""/></td>
                    <td><input type="text" name="items_remarks_{$SECTION_ITEM.tmp_id}" value=""/></td>

                {else}
                    <input type="hidden" name="tariff_item_dbid_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.id}"/> 
                    <input type="hidden" name="tariff_id_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.tariff_id}"/> 
                    <input type="hidden" name="tariff_code_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.item_code}"/> 
                    <input type="hidden" name="tariff_des_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.item_id}"/> 
                    <input type="hidden" name="tariff_section_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.tariff_section}"/> 

                    <td style="width: 5%;">
                        <input class="authchecks" type="checkbox" value="{$SECTION_ITEM.tmp_id}">
                    </td>
                    <td style="width: 5%;">{$SECTION_ITEM.item_code}</td>
                    <td>{$SECTION_ITEM.item_des}</td>
                    <td>
                        <select name="items_auth_{$SECTION_ITEM.tmp_id}" class="chzn-select ">
                            <option value=""></option>
                            <option value="Authorized" {if $SECTION_ITEM.item_auth eq 'Authorized'} selected {/if}>Authorized</option>
                            <option value="Seek Approval" {if $SECTION_ITEM.item_auth eq 'Seek Approval'} selected {/if}>Seek Approval</option>
                            <option value="Not Authorized" {if $SECTION_ITEM.item_auth eq 'Not Authorized'} selected {/if}>Not Authorized</option>
                        </select>
                    </td>
                    <td><input style="width: 60%;" type="text" name="items_authlimit_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.item_auth_limits}"/></td>
                    <td><input type="text" name="items_remarks_{$SECTION_ITEM.tmp_id}" value="{$SECTION_ITEM.item_remarks}"/></td>
                    {/if}
                </tr>
            {/foreach}    

        {/foreach}    


        </tbody>
    </table>
</div>