/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

;(function ($, window) {
	var intervals = {};
	var removeListener = function (selector) {

		if (intervals[selector]) {
			window.clearInterval(intervals[selector]);
			intervals[selector] = null;
		}
	};
	var found = 'waitUntilExists.found';

	/**
	 * @function
	 * @property {object} jQuery plugin which runs handler function once specified
	 *           element is inserted into the DOM
	 * @param {function|string} handler
	 *            A function to execute at the time when the element is inserted or
	 *            string "remove" to remove the listener from the given selector
	 * @param {bool} shouldRunHandlerOnce
	 *            Optional: if true, handler is unbound after its first invocation
	 * @example jQuery(selector).waitUntilExists(function);
	 */
	$.fn.waitUntilExists = function (handler, shouldRunHandlerOnce, isChild) {
		var selector = this.selector;
		var $this = $(selector);
		var $elements = $this.not(function () { return $(this).data(found); });

		if (handler === 'remove') {

			// Hijack and remove interval immediately if the code requests
			removeListener(selector);
		} else {

			// Run the handler on all found elements and mark as found
			$elements.each(handler).data(found, true);

			if (shouldRunHandlerOnce && $this.length) {

				// Element was found, implying the handler already ran for all
				// matched elements
				removeListener(selector);
			} else if (!isChild) {

				// If this is a recurring search or if the target has not yet been
				// found, create an interval to continue searching for the target
				intervals[selector] = window.setInterval(function () {
					$this.waitUntilExists(handler, shouldRunHandlerOnce, true);
				}, 500);
			}
		}

		return $this;
	};
 }(jQuery, window));

// -------------------------------------------------------------------------- //

Vtiger_Detail_Js("Documents_Detail_Js", {

	//It stores the CheckFileIntegrity response data
	checkFileIntegrityResponseCache : {},

	/*
	 * function to trigger CheckFileIntegrity action
	 * @param: CheckFileIntegrity url.
	 */
	checkFileIntegrity : function(checkFileIntegrityUrl) {
		Documents_Detail_Js.getFileIntegrityResponse(checkFileIntegrityUrl).then(
			function(data){
				Documents_Detail_Js.displayCheckFileIntegrityResponse(data);
			}
		);
	},

	/*
	 * function to get the CheckFileIntegrity response data
	 */
	getFileIntegrityResponse : function(params){
		var aDeferred = jQuery.Deferred();

		//Check in the cache
		if(!(jQuery.isEmptyObject(Documents_Detail_Js.checkFileIntegrityResponseCache))) {
			aDeferred.resolve(Documents_Detail_Js.checkFileIntegrityResponseCache);
		}
		else{
			AppConnector.request(params).then(
				function(data) {
					//store it in the cache, so that we dont do multiple request
					Documents_Detail_Js.checkFileIntegrityResponseCache = data;
					aDeferred.resolve(Documents_Detail_Js.checkFileIntegrityResponseCache);
				}
			);
		}
		return aDeferred.promise();
	},

	/*
	 * function to display the CheckFileIntegrity message
	 */
	displayCheckFileIntegrityResponse : function(data) {
		var result = data['result'];
		var success = result['success'];
		var message = result['message'];
		var params = {};
		if(success) {
			params = {
				text: message,
				type: 'success'
			}
		} else {
			params = {
				text: message,
				type: 'error'
			}
		}
		Documents_Detail_Js.showNotify(params);
	},

	//This will show the messages of CheckFileIntegrity using pnotify
	showNotify : function(customParams) {
		var params = {
			title: app.vtranslate('JS_CHECK_FILE_INTEGRITY'),
			text: customParams.text,
			type: customParams.type,
			width: '30%',
			delay: '2000'
		};
		Vtiger_Helper_Js.showPnotify(params);
	},

	triggerSendEmail : function(recordIds) {
		var params = {
			"module" : "Documents",
			"view" : "ComposeEmail",
			"documentIds" : recordIds
		};
		var emailEditInstance = new Emails_MassEdit_Js();
		emailEditInstance.showComposeEmailForm(params);
	}

},{
	getQueryVariable : function(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0; i<vars.length; i++) {
			var pair = vars[i].split("=");
			if(pair[0] == variable) {return pair[1];}
		}
		return(false);
	},

    showDocumentDetailView : function() {
        var dataURL = '/?module=Documents&action=GetSource&record='
                    + Documents_Detail_Js.prototype.getQueryVariable('record');

        AppConnector.request(dataURL).then(function (data) {
			if (data.success) {
                jQuery('#DocumentRenderer').waitUntilExists(function () {
                    $(this).attr('src', data.result.source);
                });
            }
       	});
    },

    registerDocumentDetailsClickEvent: function() {
		jQuery('[title="Document Details"]').on('click', this.showDocumentDetailView);
    },

    registerEvents : function(){
            this._super();
            this.showDocumentDetailView();
            this.registerDocumentDetailsClickEvent();
    }
});
