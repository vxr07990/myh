{strip}
    <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
    <input type="hidden" id="module" value="Vtiger"/>
    <input type="hidden" id="sourceModule" value="{$SOURCE_MODULE}"/>
    <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
    <input type="hidden" id="popupView" value="AccountingIntegrationPopupAjax"/>
    <div class="modal-header contentsBackground" style='margin : -1px -20px 20px'>
        <h3>{vtranslate($SOURCE_MODULE, $MODULE_NAME)}</h3>
		<button data-dismiss="modal" class="close" style='margin-top : -25px' title="{vtranslate('LBL_CLOSE')}">x</button>
    </div>
    <div class="row-fluid">
		<div class="span2">
            &nbsp;
		</div>
        <div class="span6">
            <form class="form-horizontal popupSearchContainer" onsubmit="return false;" method="POST">
                <div class="control-group margin0px">
                    <input class="span2" type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" id="searchvalue"/>&nbsp;&nbsp;
                    <span><strong>{vtranslate('LBL_IN')}</strong></span>&nbsp;
                    <span>
                        <select style="width: 140px;" class="chzn-select" id="searchableColumnsList">
                            {foreach item=fieldInfo from=$LISTVIEW_HEADERS}
                                {if $fieldInfo.queryable == 'no'}{continue}{/if}
                                <optgroup>
                                    <option value="{$fieldInfo.title}">{$fieldInfo.title}</option>
                                </optgroup>
                            {/foreach}
                        </select>
                    </span>&nbsp;&nbsp;
                    <span id="popupSearchButton">
                        <button class="btn"><i class="icon-search " title="{vtranslate('LBL_SEARCH_BUTTON')}"></i></button>
                    </span>
                </div>
            </form>
        </div>
		<div class="span4">
            <div class="popupPaging">
                <div class="row-fluid">
						<span class="span3" style="float:right !important;min-width:230px">
							<span class="pull-right">
								<span class="pageNumbers">
									<span class="pageNumbersText">{if !empty($LISTVIEW_ENTRIES)}{$PAGING_MODEL->getRecordStartRange()} {vtranslate('LBL_to', $MODULE)} {$PAGING_MODEL->getRecordEndRange()}{else}<span>&nbsp;</span>{/if}</span>
									<span class="alignBottom">
										<span class="icon-refresh totalNumberOfRecords cursorPointer{if empty($LISTVIEW_ENTRIES)} hide{/if}" style="margin-left:5px"></span>
									</span>
								</span>&nbsp;&nbsp;
								<span class="btn-group pull-right">
									<button class="btn" id="listViewPreviousPageButton" {if !$PAGING_MODEL->isPrevPageExists()} disabled {/if}><span class="icon-chevron-left"></span></button>
									<button class="btn dropdown-toggle" type="button" id="listViewPageJump" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
                                        <i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$MODULE_NAME)}"></i>
                                    </button>
									<ul class="listViewBasicAction dropdown-menu" id="listViewPageJumpDropDown">
                                        <li>
											<span class="row-fluid">
												<span class="span3 pushUpandDown2per"><span class="pull-right">{vtranslate('LBL_PAGE',$MODULE_NAME)}</span></span>
												<span class="span4">
													<input type="text" id="pageToJump" class="listViewPagingInput" value="{$PAGE_NUMBER}"/>
												</span>
												<span class="span2 textAlignCenter pushUpandDown2per">
													{vtranslate('LBL_OF',$MODULE_NAME)}&nbsp;
												</span>
												<span class="span3 pushUpandDown2per" id="totalPageCount">{$PAGE_COUNT}</span>
											</span>
                                        </li>
                                    </ul>
									<button class="btn" id="listViewNextPageButton" {if (!$PAGING_MODEL->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if}><span class="icon-chevron-right"></span></button>
								</span>
							</span>
						</span>
                </div>
            </div>
		</div>
    </div>
{/strip}