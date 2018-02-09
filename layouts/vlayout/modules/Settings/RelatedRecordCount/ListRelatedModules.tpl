{* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** *}
 
<select name="related_modulename" class="chzn-select">
    {foreach item=MODULE from=$LIST_RELATED_MODULES}
        <option value="{$MODULE.modulename}" {if $ACTIVE_RELATED_MODULE eq $MODULE.modulename}selected{/if} >{$MODULE.tablabel}</option>
    {/foreach}
</select>

