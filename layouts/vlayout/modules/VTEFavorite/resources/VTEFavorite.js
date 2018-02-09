/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("VTEFavorite_Js",{

},{
    recordId: null,
	isActiveVTEFavoriteModule: null,
	thisModuleIsConfig : null,
    moduleEditName: null,
    controlElementTd: null,
    dataEdit: null,
    VTEFavoriteCallBacks: [],


	registerModulesChangeEvent : function() {
		var ulFav=	document.getElementById('selAddView_customlist');
		if(ulFav==null) return;
		var _val = jQuery('#selAddModule_customlist').val();
		var actionParams = {
								"type":"GET",
								"dataType":"html",
								"data" : {
									'module':'VTEFavorite',
									'view':'Settings',
									'type':'getcustomview',
									'smodule':_val
								}
							};
			AppConnector.request(actionParams).then(
				function (data) {
					ulFav.innerHTML = data;

				},
				function (error) {

					//alert('error');
					//TODO : Handle error
				}
			);

		jQuery('#selAddModule_customlist').on('change',function(e){
            var element = jQuery(e.currentTarget);
            var selectedModule = jQuery(e.currentTarget).val();
            if(selectedModule.length <= 0) {
                Settings_Vtiger_Index_Js.showMessage({'type': 'error','text':app.vtranslate('JS_PLEASE_SELECT_MODULE')});
                return;
            }


			var actionParams = {
								"type":"GET",
								"dataType":"html",
								"data" : {
									'module':'VTEFavorite',
									'view':'Settings',
									'type':'getcustomview',
									'smodule':selectedModule
								}
							};
			AppConnector.request(actionParams).then(
				function (data) {
					ulFav.innerHTML = data;

				},
				function (error) {

					//alert('error');
					//TODO : Handle error
				}
			);
		});
	},
    registerVTEFavoriteEvent: function(_stars){
        var thisInstance=this;
		var qstings=getUrlVars();

        jQuery('div.contentHeader').each(function(){
            var inputhtml='';
            for(i=1; i<6; i++)
            {
                inputhtml += '<input type="radio" name="vte_fav" class="rating" value="' + i + '" ' +(i===_stars?'checked':'') +' />'
            }

            jQuery(this).append('<span id="spanfav" class="containerfav pull-right" style="margin:5px 10px 0 0;" >' + inputhtml + '</span>');

        });
        jQuery('div.detailViewTitle').each(function(){
            var inputhtml='';
            for(i=1; i<6; i++)
            {
                inputhtml += '<input type="radio" name="vte_fav" class="rating" value="' + i + '" ' +(i===_stars?'checked':'') +' />'
            }

            jQuery(this).find('div.detailViewButtoncontainer').after('<div class="pull-right" style="margin:14px 10px 0 0;"><span id="spanfav" class="containerfav" >' + inputhtml + '</span></div>');

        });
        jQuery('div.detailViewTitle , div.contentHeader').each(function(){

			$('#spanfav').rating(function(vote, event)
			{
					var progressIndicatorElement = jQuery.progressIndicator({
									'position' : 'html',
									'blockInfo' : {
										'enabled' : true
									}
								});
					var surl =window.location.href;
					var smodule =qstings['module'];
					var sview =qstings['view'];
					var srecord =qstings['record'];
					var stars =vote;

					var actionParams = {
						"type":"POST",
						"dataType":"json",
						"data" : {
							'smodule':smodule,
							'sview':sview,
							'srecord':srecord,
							'surl':surl,
							'stars':stars,

							'module':'VTEFavorite',
							'action':'ActionAjax',
							'mode':'addFavorite'
						}
					};
					AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
								progressIndicatorElement.progressIndicator({'mode' : 'hide'});
								//alert('success');
							}
						},
						function(error) {
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
							//alert('error');
							//TODO : Handle error
						}
					);
		    });
        });
    },

	regVTEFavoriteCheckAndGetModulesActive: function(_module,_sview,_srecord,_surl){
		// thisInstance.isActiveVTEFavoriteModule: null,
		// thisInstance.thisModuleIsConfig : null,
        var thisInstance=this;
		// var progressIndicatorElement = jQuery.progressIndicator({
									// 'position' : 'html',
									// 'blockInfo' : {
										// 'enabled' : true
									// }
								// });

	    var actionParams = {
			"type":"POST",
			"dataType":"json",
			"data" : {
				'smodule':_module,
				'module':'VTEFavorite',
				'action':'ActionAjax',
				'mode':'CheckAndGetModulesActive'
			}
		};
		AppConnector.request(actionParams).then(
			function(data) {
				if(data['success']) {
					// progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					//alert('success');
					// 3.1 show star icon on top :
					var ul = document.getElementById('ul_favorite');
					if(ul==null)
					{
						var hdlb=	document.getElementById('headerLinksBig');

						var ihtml = '<span class="dropdown span settingIcons"> <a onclick="getList(\'favorite\');" id="favorite" class="dropdown-toggle " data-toggle="dropdown" href="#"><img src="layouts/vlayout/modules/VTEFavorite/resources/Places-favorites-icon.png" style="width:18px" alt="favorites" title="favorites"></a> <div id="ul_favorite" class=" vteFav" style="display:none;">waiting...</div> </span>';
						 ihtml =ihtml + '<span class="dropdown span settingIcons"> <a onclick="getList(\'recently\');" id="recently" class="dropdown-toggle " data-toggle="dropdown" href="#"><img src="layouts/vlayout/modules/VTEFavorite/resources/icon-recent.png" style="width:18px" alt="recent" title="recent"></a> <div id="ul_recently" class="dropdown-menu vteFav" >waiting...</div> </span>';
						 ihtml =ihtml + '<span class="dropdown span settingIcons"> <a onclick="getList(\'customlist\');" id="customlist" class="dropdown-toggle " data-toggle="dropdown" href="#"><img src="layouts/vlayout/modules/VTEFavorite/resources/list-512.png" style="width:18px" alt="customlist" title="customlist"></a> <div id="ul_customlist" class="dropdown-menu vteFav" >waiting...</div> </span>';
						//var ihtml = '<ul class="nav" ><li class="dropdown span settingIcons" id="moreMenu2"><a class="dropdown-toggle" data-toggle="dropdown"href="#moreMenu2"><img src="layouts/vlayout/modules/VTEFavorite/resources/Places-favorites-icon.png" style="width:18px" alt="favorites" title="favorites"></a><div class="dropdown-menu moreMenus">abc</div></li> </ul>';
						hdlb.innerHTML = ihtml + hdlb.innerHTML ;
						//alert($( "#headerLinksBig" ).innerHTML);
					}

					if( (_sview=="Edit" || _sview=="Detail" ) && data.result.favisactive &&  data.result.favisactive==1)
					{
						//3.2 this module is config:
						var actionParams = {
								"type":"POST",
								"dataType":"json",
								"data" : 	{
									'smodule':_module,
									'sview':_sview,
									'srecord':_srecord,
									'surl':_surl,

									'module':'VTEFavorite',
									'action':'ActionAjax',
									'mode':'getFavorite'
											}
									 };
						AppConnector.request(actionParams).then(
											function(data) {
												if(data['success'] && data['success']==true)
												{
												//
													if (data.result.isshow === 1) {
														if(data.result.stars===null){data.result.stars=0;}
														thisInstance.registerVTEFavoriteEvent(parseInt(data.result.stars));
													}
												}
											},
											function(error) {
												//err:
												//TODO : Handle error
											}
						);
					}
					//========recently==================
					if( (_sview=="Edit" || _sview=="Detail" ) &&(data.result.recisactive &&  data.result.recisactive==1))
					{
						//3.2 this module is config:
						var actionParams = {
								"type":"POST",
								"dataType":"json",
								"data" : 	{
									'smodule':_module,
									'sview':_sview,
									'srecord':_srecord,
									'surl':_surl,

									'module':'VTEFavorite',
									'action':'ActionAjax',
									'mode':'addRecently'
											}
									 };
						AppConnector.request(actionParams).then(
											function(data) {
												if(data['success'] && data['success']==true)
												{
												//
													// if (data.result.isshow === 1) {
														// if(data.result.stars===null){data.result.stars=0;}
														// thisInstance.registerVTEFavoriteEvent(parseInt(data.result.stars));
													// }
												}
											},
											function(error) {
												//err:
												//TODO : Handle error
											}
						);
					}

				}
			},
			function(error) {
				// progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				//alert('error');
				//TODO : Handle error
			}
		);

    },
	registerSortableEvent : function(container,id) {
		var thisInstance = this;
		var contents = container.find('.ui-sortable');
		var table = contents.find('.blockFieldsList');
		contents.sortable({
		    'containment' : contents,
		    'items' : table,
		    'revert' : true,
		    'tolerance':'pointer',
		    'cursor' : 'move',
		    'helper' : function(e,ui){
			//while dragging helper elements td element will take width as contents width
			//so we are explicity saying that it has to be same width so that element will not
			//look like distrubed
			ui.children().each(function(index,element){
			    element = jQuery(element);
			    element.width(element.width());
			});
			return ui;
		    },
		    'update' : function(e, ui) {
			//thisInstance.updateLineDataElementByOrder(id);
		    }
		});
	    },
	makeFieldsListSortable : function() {
		var thisInstance = this;
		var contents = jQuery('#layoutEditorContainer').find('.contents');
		var table = contents.find('.editFieldsTable');
		table.find('ul[name=sortable1], ul[name=sortable2]').sortable({
			'containment' : '#moduleBlocks',
			'revert' : true,
			'tolerance':'pointer',
			'cursor' : 'move',
			'connectWith' : '.connectedSortable',
			'update' : function(e, ui) {
				var currentField = ui['item'];
				thisInstance.showSaveFieldSequenceButton();
				thisInstance.createUpdatedBlocksList(currentField);
				// rearrange the older block fields
				if(ui.sender) {
					var olderBlock = ui.sender.closest('.editFieldsTable');
					thisInstance.reArrangeBlockFields(olderBlock);
				}
			}
		});
	},

	makeItemsListSortable : function() {
		 var thisInstance = this;

		 jQuery('.select2').each(function(){
			var selectElement = thisInstance.getListSelectElement(this.id);
			var select2Element = app.getSelect2ElementFromSelect(selectElement);
			var select2ChoiceElement = select2Element.find('ul.select2-choices');
			select2ChoiceElement.sortable({
                'containment': select2ChoiceElement,
                start: function() {
				//jQuery('#selectedMenus').select2("onSortStart");
				},
                update: function() {
					//jQuery('#selectedMenus').select2("onSortEnd");
					//If sorting happened save button should show
					//thisInstance.showSaveButton();
				}
				});
			});
		// var thisInstance = this;
		// var contents = jQuery('#layoutEditorContainer').find('.contents');
		// var table = contents.find('.editFieldsTable');
		// table.find('ul[name=sortable1], ul[name=sortable2]').sortable({
			// 'containment' : '#moduleBlocks',
			// 'revert' : true,
			// 'tolerance':'pointer',
			// 'cursor' : 'move',
			// 'connectWith' : '.connectedSortable',
			// 'update' : function(e, ui) {
				// var currentField = ui['item'];
				// thisInstance.showSaveFieldSequenceButton();
				// thisInstance.createUpdatedBlocksList(currentField);
				// // rearrange the older block fields
				// if(ui.sender) {
					// var olderBlock = ui.sender.closest('.editFieldsTable');
					// thisInstance.reArrangeBlockFields(olderBlock);
				// }
			// }
		// });
	},
	getListSelectElement : function(id) {
		return jQuery('#'+id);
	},
	registerEvents: function() {
        this.registerVTEFavoriteEvent();
		this.registerModulesChangeEvent();
		this.makeItemsListSortable();
		this.regVTEFavoriteCheckAndGetModulesActive();

    }
});
function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}
function getList(mode)
{
	var ulFav=	document.getElementById('ul_'+mode);
    $(".vteFav").hide();
    $('#'+mode).next(".vteFav, .vteFav *").show();
    ulFav.innerHTML = '<img class="loadinImg alignMiddle" src="layouts/vlayout/skins/twilight/images/loading.gif">';
	$(".vteFav").css('left',($(window).width() - $('#ul_'+mode).width()) + 'px');
    var org_top = $('#topMenus').position().top;
    var org_height = $('#topMenus').height();
    $(".vteFav").css("top",(org_top + org_height)+"px");
	//$("#page").append('<style> .vteFav:after, .vteFav:before {left: 435px !important;}</style> ');
	//$("#page").css('left','437px !important');
	var actionParams = {
        "type":"GET",
        "dataType":"html",
        "data" : {
            'module':'VTEFavorite',
            'view':'FavoriteList',
            'mode':mode
        }
    };
    AppConnector.request(actionParams).then(
		function (data) {
            ulFav.innerHTML = data;
		    $(".vteFavDiv").css('max-height', ($(window).height() - 39) + 'px');
		    $("tr").not(':first').hover(
              function () {
                  $(this).find(".vteFavHoverMenu").css("display", "block");
              },
              function () {
                  $(this).find(".vteFavHoverMenu").css("display", "none");
              }
            );
		},
		function (error) {
		    //TODO : Handle error
		}
	);
}
function delRecord(type,id)
{
	var actionParams = {
                "type":"POST",
                "dataType":"json",
                "data" : {
                    'id':id,
                    'type':type,
                    'module':'VTEFavorite',
                    'action':'ActionAjax',
                    'mode':'delRecord'
				}
             };
	            AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
                                $('table.vtetable tr#tr_'+id).remove();
                                console.log('tr_'+id);
                               // getList('favorite');
							//
								//window.location.reload();
							}
						},
						function(error) {
							//err:
							//TODO : Handle error
						}

					);

}
function createli(t,l,star)
{

	var li = document.createElement("div");
	var a = document.createElement("a");
	a.setAttribute("href",l);


	var atext = document.createTextNode(t );
	a.appendChild(atext);
	//a.appendChild(createStar());
	/* for(i=0;i<star;i++)
	{
		a.appendChild(createStar());
	} */
	li.appendChild(a);
	return li;
}
function createStar()
{
	var star = document.createElement("img");
	star.setAttribute("src","layouts/vlayout/modules/VTEFavorite/resources/Places-favorites-icon.png");
	star.setAttribute("style","width:12px");
	return star;
}

function AddModule(_type)
{
	var progressIndicatorElement = jQuery.progressIndicator({
									'position' : 'html',
									'blockInfo' : {
										'enabled' : true
									}
								});
	var _val = jQuery('#selAddModule_'+_type).val();
	var viewid=jQuery('#selAddView_customlist').val();
	var actionParams = {
			"type":"POST",
			"dataType":"json",
			"data" : {
				'smodule':_val,
				'type':_type,
				'viewid':viewid,
				'module':'VTEFavorite',
				'action':'ActionAjax',
				'mode':'addModule'
				}
			     };
	AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
							//reload page:
							//	window.location.reload();
							var param="&type=" + _type;
							var _url = location.href;
							_url=_url.split("&type=")[0] + param;

							window.location.assign(_url);

							}
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						},
						function(error) {
							//err:
							//TODO : Handle error
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						}
					);

}
function saveFields(_type,moduleid,customid)
{
	var progressIndicatorElement = jQuery.progressIndicator({
									'position' : 'html',
									'blockInfo' : {
										'enabled' : true
									}
								});
	//=========
	var selectElement = jQuery('#fieldSelectElement_' + _type +'_' + moduleid + '_' + customid);
	var select2Element = app.getSelect2ElementFromSelect(selectElement);

	var selectedValuesByOrder = {};
	var selectedOptions = selectElement.find('option:selected');
	var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
	var i = 1;
	orderedSelect2Options.each(function(index,element){
		var chosenOption = jQuery(element);
		selectedOptions.each(function(optionIndex, domOption){
			var option = jQuery(domOption);
			if(option.html() == chosenOption.html()) {
				selectedValuesByOrder[i++] = option.val();
				return false;
			}
		});
	});

	var _val = JSON.stringify(selectedValuesByOrder);
	var parsed =$.parseJSON(_val);
	var _limit=jQuery('#limitedRecord_' + _type +'_' + moduleid+ '_' + customid).val();
	var arr = [];

	for(var x in parsed){
	  arr.push(parsed[x]);
	}
	_val=arr.join();
	//=============

	var actionParams = {
			"type":"POST",
			"dataType":"json",
			"data" : {
				'fields':_val,
				'type':_type,
				'limit':_limit,
				'smodule':moduleid,
				'id':customid,
				'module':'VTEFavorite',
				'action':'ActionAjax',
				'mode':'saveFields'
				}
			     };
	AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
							}
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						},
						function(error) {
							//err:
							//TODO : Handle error
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						}
					);

}
function deleModule(_type,moduleid)
{
	var progressIndicatorElement = jQuery.progressIndicator({
									'position' : 'html',
									'blockInfo' : {
										'enabled' : true
									}
								});
	var actionParams = {
			"type":"POST",
			"dataType":"json",
			"data" : {
				'smodule':moduleid,
				'type':_type,
				'module':'VTEFavorite',
				'action':'ActionAjax',
				'mode':'deleModule'
				}
			     };
	AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
							//
							var param="&type=" + _type;
							var _url = location.href;
							_url=_url.split("&type=")[0] + param;

							window.location.assign(_url);
							}
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						},
						function(error) {
							//err:
							//TODO : Handle error
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						}
					);

}
function activeModuleFavorite(module,active)
{
	var qstings=getUrlVars();

	var type =qstings['type'];
	if(type==undefined){type='favorite';}
	if(active)
	{active=1}
	else
	{active=0}
	var progressIndicatorElement = jQuery.progressIndicator({
									'position' : 'html',
									'blockInfo' : {
										'enabled' : true
									}
								});
	var actionParams = {
			"type":"POST",
			"dataType":"json",
			"data" : {
				'smodule':module,
				'active':active,
				'module':'VTEFavorite',
				'action':'ActionAjax',
				'type':type,
				'mode':'activeModuleFavorite'
				}
			     };
	AppConnector.request(actionParams).then(
						function(data) {
							if(data['success']) {
							}
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						},
						function(error) {
							//err:
							//TODO : Handle error
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						}
					);

}
function loadScript(src, f,type) {
  var head = document.getElementsByTagName("head")[0];
  if(type=="css")
  {
	var css_link = $("<link>", {
				rel: "stylesheet",
				type: "text/css",
				href: src
				    });
	    css_link.appendTo('head');
  }
  else
  {
	  var script = document.createElement(type);//"script"
	  script.src = src;
	  var done = false;
	  script.onload = script.onreadystatechange = function() {
	    // attach to both events for cross browser finish detection:
	    if ( !done && (!this.readyState ||
	      this.readyState == "loaded" || this.readyState == "complete") ) {
	      done = true;
	      if (typeof f == 'function') f();
	      // cleans up a little memory:
	      script.onload = script.onreadystatechange = null;
	      head.removeChild(script);
	    }
	  };
	  head.appendChild(script);
  }
}



jQuery(document).ready(function(){
    var vteVTEFavorite = new VTEFavorite_Js();

	vteVTEFavorite.registerModulesChangeEvent();

	var qstings=getUrlVars();
	var surl =window.location.href;
	var smodule =qstings['module'];
	var sview =qstings['view'];
	var srecord =qstings['record'];
	if(smodule == "VTEFavorite" && sview=="Settings")
	{
		vteVTEFavorite.makeItemsListSortable();
		loadScript('layouts/vlayout/modules/VTEFavorite/resources/LayoutEditor.js', function() {
							 },"script");
		loadScript('libraries/jquery/select2/select2.css', function() {
							  },"css");

		$(".number").keypress(function (e) {
			 //if the letter is not digit then display error and don't type anything
			 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				//display error message
				//$("#errmsg").html("Digits Only").show().fadeOut("slow");
				return false;
			}
	   });
	}
	// 1. Check module on, and this module is configed

	vteVTEFavorite.regVTEFavoriteCheckAndGetModulesActive(smodule,sview,srecord,surl);//(_module_sview,_srecord,_surl)
    $(document).click(function(e) {
        if (!$(e.target).is('.vteFav, .vteFav *')) {
            e.stopPropagation();
            $(".vteFav").hide();
        }
    });
});

