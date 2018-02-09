{* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** *}
 
<div class="container-fluid">
    <div class="contentHeader row-fluid">
        <h3 class="span8 textOverflowEllipsis">
            <a href="index.php?module=ModuleManager&parent=Settings&view=List">&nbsp;{vtranslate('MODULE_MANAGEMENT',$QUALIFIED_MODULE)}</a>&nbsp;>&nbsp;{vtranslate('LBL_SETTING_HEADER', $QUALIFIED_MODULE)}
        </h3>
    </div>
    <hr>
    <div class="clearfix"></div>

    <div class="listViewContentDiv row-fluid" id="listViewContents">
        <div class="marginBottom10px">
            <span class="row btn-toolbar">
                <button type="button" data-url="index.php?module=RelatedRecordCount&view=EditViewAjax&parent=Settings" class="btn addButton editButton">
                    <i class="icon-plus"></i>&nbsp;
                    <strong>{vtranslate('LBL_ADD_MORE_BTN', $QUALIFIED_MODULE)}</strong>
                </button>
            </span>
        </div>
        <div class="marginBottom10px">
            <table class="table table-bordered listViewEntriesTable vte-related-record-count">
                <thead>
                    <tr class="listViewHeaders">
                        <th class="medium"></th>
                        <th class="medium">{vtranslate('LBL_MODULE_NAME_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_RELATED_MODULE_NAME_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_LABEL_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium" colspan="2">{vtranslate('LBL_STATUS_HEADER', $QUALIFIED_MODULE)}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $COUNT_ENTITY gt 0}
                        {foreach item=ENTITY from=$ENTITIES}
                            <tr>
                                <td class="listViewEntryValue" width="5%">
                                    <i class="icon-move alignMiddle" title="{vtranslate('LBL_MOVE_BTN', $QUALIFIED_MODULE)}" data-record="{$ENTITY.id}"></i>
                                </td>
                                <td class="listViewEntryValue" width="10%">{vtranslate($ENTITY.modulename, $ENTITY.modulename)}</td>
                                <td class="listViewEntryValue" width="10%">{vtranslate($ENTITY.related_modulename, $ENTITY.related_modulename)}</td>
                                <td class="listViewEntryValue" width="55%">
                                    <a style="color: {$ENTITY.color};" class="editButton" href="javascript:void(0)" data-url="index.php?module=RelatedRecordCount&view=EditViewAjax&parent=Settings&record={$ENTITY.id}">
                                    {$ENTITY.label}
                                    </a>
                                </td>
                                <td class="listViewEntryValue" width="10%">{$ENTITY.status}</td>
                                <td class="listViewEntryValue" width="10%">
                                    <div class="actions pull-right">
                                        <span class="actionImages">
                                            <a data-url="index.php?module=RelatedRecordCount&action=DuplicateAjax&parent=Settings&record={$ENTITY.id}" class="duplicateButton" href="javascript: void(0);">
                                                <i class="icon-book alignMiddle" title="{vtranslate('LBL_DUPLICATE_BTN', $QUALIFIED_MODULE)}"></i>
                                            </a>
                                            <a data-url="index.php?module=RelatedRecordCount&view=EditViewAjax&parent=Settings&record={$ENTITY.id}" class="editButton" href="javascript: void(0);">
                                                <i class="icon-pencil alignMiddle" title="{vtranslate('LBL_EDIT_BTN', $QUALIFIED_MODULE)}"></i>
                                            </a>
                                            <a data-url="index.php?module=RelatedRecordCount&action=DeleteAjax&parent=Settings&record={$ENTITY.id}" class="deleteButton" href="javascript: void(0);">
                                                <i class="icon-trash alignMiddle" title="{vtranslate('LBL_DELETE_BTN', $QUALIFIED_MODULE)}"></i>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>

