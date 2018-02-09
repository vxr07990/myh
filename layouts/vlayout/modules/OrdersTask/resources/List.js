Vtiger_List_Js("OrdersTask_List_Js", {}, {
    registerChangeCustomFilterEvent : function(){
	var thisInstance = this;
	var filterSelectElement = this.getFilterSelectElement();
	filterSelectElement.change(function(e){
	    jQuery('#pageNumber').val("1");
	    jQuery('#pageToJump').val('1');
	    jQuery('#orderBy').val('');
	    jQuery("#sortOrder").val('');
	    var cvId = thisInstance.getCurrentCvId();
	    selectedIds = new Array();
	    excludedIds = new Array();

	    var urlParams ={
		"viewname" : cvId,
		//to make alphabetic search empty
		"search_key" : thisInstance.getAlphabetSearchField(),
		"search_value" : "",
		"search_params" : ""
	    }
	    //Make the select all count as empty
	    jQuery('#recordsCount').val('');
	    //Make total number of pages as empty
	    jQuery('#totalPageCount').text("");
	    thisInstance.getListViewRecords(urlParams).then (function(){
			thisInstance.ListViewPostOperation();
			thisInstance.calculatePages().then(function(){
				thisInstance.updatePagination();
			});
			thisInstance.registerRowClickEvent();		
			thisInstance.registerCustomTooltipEvents();

	    });
	});
    },
    registerListSearch : function() {
      var listViewPageDiv = this.getListViewContainer();
      var thisInstance = this;
      listViewPageDiv.on('click','[data-trigger="listSearch"]',function(e){
			thisInstance.getListViewRecords({'page': '1'}).then(
					function(data){
                        //To unmark the all the selected ids
                        jQuery('#deSelectAllMsg').trigger('click');

                         jQuery('#recordsCount').val('');
                        //To Set the page number as first page
                        jQuery('#pageNumber').val('1');
                        jQuery('#pageToJump').val('1');
                        jQuery('#totalPageCount').text("");
						thisInstance.calculatePages().then(function(){
							thisInstance.updatePagination();
						});
						thisInstance.registerRowClickEvent();
						thisInstance.registerCustomTooltipEvents();
					},

					function(textStatus, errorThrown){
					}
			);
      })

      listViewPageDiv.on('keypress','input.listSearchContributor',function(e){
          if(e.keyCode == 13){
              var element = jQuery(e.currentTarget);
              var parentElement = element.closest('tr');
              var searchTriggerElement = parentElement.find('[data-trigger="listSearch"]');
              searchTriggerElement.trigger('click');
          }
      });
	},
	registerPageNavigationEvents : function(){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		jQuery('#listViewNextPageButton').on('click',function(){
			var pageLimit = jQuery('#pageLimit').val();
			//console.dir('pageLimit: ' + pageLimit);
			var noOfEntries = jQuery('#noOfEntries').val();
			//console.dir('noOfEntries: ' + noOfEntries);
			if(noOfEntries == pageLimit){
				var orderBy = jQuery('#orderBy').val();
				var sortOrder = jQuery("#sortOrder").val();
				var cvId = thisInstance.getCurrentCvId();
				var urlParams = {
					"orderby": orderBy,
					"sortorder": sortOrder,
					"viewname": cvId
				}
				var pageNumber = jQuery('#pageNumber').val();
				//console.dir('pageNumber: ' + pageNumber);
				var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
				//console.dir('nextPageNumber: ' + nextPageNumber);
				jQuery('#pageNumber').val(nextPageNumber);
				jQuery('#pageToJump').val(nextPageNumber);
				thisInstance.getListViewRecords(urlParams).then(
					function(data){
						thisInstance.calculatePages().then(function(){
							thisInstance.updatePagination();
						});
						thisInstance.registerRowClickEvent();
						thisInstance.registerCustomTooltipEvents();		
						aDeferred.resolve();
					},

					function(textStatus, errorThrown){
						aDeferred.reject(textStatus, errorThrown);
					}
				);
			}
			return aDeferred.promise();
		});
		jQuery('#listViewPreviousPageButton').on('click',function(){
			var aDeferred = jQuery.Deferred();
			var pageNumber = jQuery('#pageNumber').val();
			if(pageNumber > 1){
				var orderBy = jQuery('#orderBy').val();
				var sortOrder = jQuery("#sortOrder").val();
				var cvId = thisInstance.getCurrentCvId();
				var urlParams = {
					"orderby": orderBy,
					"sortorder": sortOrder,
					"viewname" : cvId
				}
				var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;
				jQuery('#pageNumber').val(previousPageNumber);
				jQuery('#pageToJump').val(previousPageNumber);
				thisInstance.getListViewRecords(urlParams).then(
					function(data){
						thisInstance.calculatePages().then(function(){
							thisInstance.updatePagination();
						});
						thisInstance.registerRowClickEvent();	
						thisInstance.registerCustomTooltipEvents();			
						aDeferred.resolve();
					},

					function(textStatus, errorThrown){
						aDeferred.reject(textStatus, errorThrown);
					}
				);
			}
		});

		jQuery('#listViewPageJump').on('click',function(e){
            if(typeof Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(jQuery('#pageToJump'))!= 'undefined') {
                var pageNo = jQuery('#pageNumber').val();
                jQuery("#pageToJump").val(pageNo);
            }
			jQuery('#pageToJump').validationEngine('hideAll');
			var element = jQuery('#totalPageCount');
			var totalPageNumber = element.text();
			if(totalPageNumber == ""){
				var totalCountElem = jQuery('#totalCount');
				var totalRecordCount = totalCountElem.val();
				if(totalRecordCount != '') {
					var recordPerPage = jQuery('#pageLimit').val();
					if(recordPerPage == '0') recordPerPage = 1;
					pageCount = Math.ceil(totalRecordCount/recordPerPage);
					if(pageCount == 0){
						pageCount = 1;
					}
					element.text(pageCount);
					return;
				}
				element.progressIndicator({});
				thisInstance.getPageCount().then(function(data){
					var pageCount = data['result']['page'];
					totalCountElem.val(data['result']['numberOfRecords']);
					if(pageCount == 0){
						pageCount = 1;
					}
					element.text(pageCount);
					element.progressIndicator({'mode': 'hide'});
			});
		}
		})

		jQuery('#listViewPageJumpDropDown').on('click','li',function(e){
			e.stopImmediatePropagation();
		}).on('keypress','#pageToJump',function(e){
			if(e.which == 13){
				e.stopImmediatePropagation();
				var element = jQuery(e.currentTarget);
				var response = Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(element);
				if(typeof response != "undefined"){
					element.validationEngine('showPrompt',response,'',"topLeft",true);
				} else {
					element.validationEngine('hideAll');
					var currentPageElement = jQuery('#pageNumber');
					var currentPageNumber = currentPageElement.val();
					var newPageNumber = parseInt(jQuery(e.currentTarget).val());
					var totalPages = parseInt(jQuery('#totalPageCount').text());
					if(newPageNumber > totalPages){
						var error = app.vtranslate('JS_PAGE_NOT_EXIST');
						element.validationEngine('showPrompt',error,'',"topLeft",true);
						return;
					}
					if(newPageNumber == currentPageNumber){
						var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+newPageNumber;
						var params = {
							text: message,
							type: 'info'
						};
						Vtiger_Helper_Js.showMessage(params);
						return;
					}
					currentPageElement.val(newPageNumber);
					thisInstance.getListViewRecords().then(
						function(data){
							thisInstance.registerRowClickEvent();		
							thisInstance.registerCustomTooltipEvents();					
							thisInstance.calculatePages().then(function(){
								thisInstance.updatePagination();
							});
							element.closest('.btn-group ').removeClass('open');
						},
						function(textStatus, errorThrown){
						}
					);
				}
				return false;
			}
		});
	},
	registerRowClickEvent: function(){
		jQuery('div.listViewPageDiv').on('click','.listViewEntries',function(e){
			if(jQuery(e.target, jQuery(e.currentTarget)).is('td:first-child')) return;
			if(jQuery(e.target).is('input[type="checkbox"]')) return;
			var elem = jQuery(e.currentTarget);
			var recordUrl = elem.data('recordurl');
			if(typeof recordUrl == 'undefined') {
				return;
			}
			window.location.href = recordUrl;
		});
	},
    registerCustomTooltipEvents: function() {
	var references = jQuery('td.customToolTip > span');
	var lastPopovers = [];

	// Fetching reference fields often is not a good idea on a given page.
	// The caching is done based on the URL so we can reuse.
	var CACHE_ENABLED = true; // TODO - add cache timeout support.

	function prepareAndShowTooltipView() {
	    hideAllTooltipViews();

	    var el = jQuery(this);
	    var field = (jQuery(this).closest('.customToolTip').hasClass('total_estimated_personnel')) ? 'personnel' : 'vehicles';
	    var id = jQuery(this).closest("tr.listViewEntries").data("id");

	    var url = '?module=OrdersTask&view=TooltipAjax&record='+id+'&customTooltip='+field;
	    var cachedView = CACHE_ENABLED ? jQuery('[data-url-cached="'+url+'"]') : null;
	    if (cachedView && cachedView.length) {
		    showTooltip(el, cachedView.html());
	    } else {
		AppConnector.request(url).then(function(data){
		    cachedView = jQuery('<div>').css({display:'none'}).attr('data-url-cached', url);
		    cachedView.html(data);
		    jQuery('body').append(cachedView);
		    showTooltip(el, data);
		});
	    }
	}
	
	function get_popover_placement(el) {
	    var width = window.innerWidth;
	    var left_pos = jQuery(el).offset().left;
	    if (width - left_pos > 400) return 'right';
	    return 'left';
	}

	function showTooltip(el, data) {
	    var title = (jQuery(el).closest('.customToolTip').hasClass('total_estimated_personnel')) ? 'Estimated Personnel' : 'Estimated Vehicles';
	    var the_placement = get_popover_placement(el);
	    el.popover({
		title: title,
		trigger: 'manual',
		content: data,
		animation: false,
		placement:  the_placement,
		template: '<div class="popover popover-tooltip"><div class="arrow"></div><div class="popover-inner"><button name="vtTooltipClose" class="close" style="color:white;opacity:1;font-weight:lighter;position:relative;top:3px;right:3px;">x</button><h3 class="popover-title"></h3><div class="popover-content"><div></div></div></div></div>'
	    });
	    lastPopovers.push(el.popover('show'));
	    registerToolTipDestroy();
	}

	function hideAllTooltipViews() {// Hide all previous popover
	    var lastPopover = null;
	    while (lastPopover = lastPopovers.pop()) {
		lastPopover.popover('hide');
	    }
	}

	references.each(function(index, el){
	    if(jQuery(el).text().trim() == ""){
		el = jQuery(el).closest('td');
	    }
	    
	    // unbind the hoverIntent
	    jQuery(el).unbind("mouseenter").unbind("mouseleave");
	    jQuery(el).removeProp('hoverIntent_t');
	    jQuery(el).removeProp('hoverIntent_s');

	    jQuery(el).hoverIntent({
		interval: 100,
		sensitivity: 7,
		timeout: 10,
		over: prepareAndShowTooltipView,
		out: hideAllTooltipViews
	    });
	});

	function registerToolTipDestroy() {
	    jQuery('button[name="vtTooltipClose"]').on('click', function(e){
		var lastPopover = lastPopovers.pop();
		lastPopover.popover('hide');
	    });
	}
	},
	registerEvents : function() {
        this._super();
    }
});