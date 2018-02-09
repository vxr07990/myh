Vtiger_Edit_Js("WFInventory_Edit_Js", {
  articleFields: [
    jQuery('input[name="category"]'),
    jQuery('input[name="type"]'),
    jQuery('textarea[name="description"]'),
    jQuery('textarea[name="reader_description"]'),

    jQuery('input[name="attribute_1"]'),
    jQuery('input[name="attribute_2"]'),
    jQuery('input[name="attribute_3"]'),
    jQuery('input[name="attribute_4"]'),
    jQuery('input[name="attribute_5"]'),
    jQuery('input[name="attribute_6"]'),
    jQuery('input[name="attribute_7"]'),
    jQuery('input[name="attribute_8"]'),

    jQuery('input[name="manufacturer"]'),
    jQuery('input[name="manufacturer_part_num"]'),
    jQuery('input[name="vendor"]'),
    jQuery('input[name="vendor_num"]'),
    jQuery('input[name="part_num"]'),
    jQuery('input[name="width"]'),
    jQuery('input[name="depth"]'),
    jQuery('input[name="height"]'),
    jQuery('input[name="sq_ft"]'),
    jQuery('input[name="cu_ft"]'),
    jQuery('input[name="weight"]'),
  ],

  registerRules: function (isEditView) {
      var rules = {
        date_in: {
            conditions: [
                {
                    operator: 'always',
                    targetFields: [
                        {
                            name: 'date_in',
                            readonly: true,
                        },
                        {
                            name: 'date_out',
                            readonly: true,
                        },
                    ]
                }
            ]
        },
        inventory_number: {
          conditions: [
            {
              operator: 'set',
              targetFields: [
                {
                  name: 'article',
                  unmandatory: true,
                }
              ]
            }
          ]
        },
        article: {
          conditions: [
            {
              operator: 'set',
              targetFields: [
                {
                  name: 'inventory_number',
                  unmandatory: true,
                }
              ]
            }
          ]
        },
    };
    this.applyVisibilityRules(rules, isEditView);
  },

  populateArticleInfo: function() {
    var thisInstance = this;
    jQuery('input[name="article"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function() {
      var id = jQuery('input:hidden[name="article"]').val();

      var params = {
          'type': 'GET',
          'url': 'index.php',
          'data': {
              'module': 'WFArticles',
              'action': 'GetRecordToPopulate',
              'id': id
          }
      };
      AppConnector.request(params).then(
          function(data) {
              if (data.success) {
                  thisInstance.unbindArticleItems();
                  thisInstance.applyArticleItems(data.result.data);
              } else {
                  console.dir('error getting article');
              }
          },
          function(err) {
              console.dir('overall error getting article');
          }
      );
    });
  },

  getPopUpParams: function (container, e) {
		var params = this._super(container);
		var sourceFieldElement = jQuery('input[class="sourceField"]', container);
    if(['article','costcenter','wfcondition','order_id'].indexOf(sourceFieldElement.attr('name')) > -1){
      var parentElement = jQuery('input[name="wfaccount_display"]');
      if(this.accountCheck(parentElement)) {
        console.log(parentElement.val());
        params['accountId'] = parentElement.val();
      } else {
        return false;
      }
    }
		return params;
	},

  accountCheck: function(element) {
    if(element.val().length == 0) {
      var params = {
        title: app.vtranslate('JS_ERROR'),
        text: app.vtranslate('Account can not be null. Please select an Account first'),
        animation: 'show',
        type: 'error'
      };
      Vtiger_Helper_Js.showPnotify(params);
    } else {
      return true;
    }
  },

  unbindArticleItems: function() {
    jQuery.each(this.articleFields,function(){
      jQuery(this).val('');
    });
  },

  applyArticleItems: function(vals) {
    jQuery.each(this.articleFields,function(){
      jQuery(this).val(vals[jQuery(this).attr('name')]);
    });
  },

  defaultUDFs: function(which) {
    var block = jQuery('.block_LBL_WFINVENTORY_USER_DEFINED');
    if(which == 'all') {
      var udfArray = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
    } else {
      var udfArray = [which];
    }

    jQuery.each(udfArray,function(key,val) {
      block.find('label').filter(function() {
        return ($(this).text() === "LBL_WFINVENTORY_UDF_" + val || $(this).text() === "UDF " + val);
      }).text('UDF '+val);
    });
  },

  updateUDFs: function() {
    var thisInstance = this;
    var account = jQuery('input[name="wfaccount"]').val();
    //ugh
    var udfArray = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];

    if(account.length == 0) {
      thisInstance.defaultUDFs('all');
      return;
    }

    var params = {
        'type': 'GET',
        'url': 'index.php',
        'data': {
            'module': 'WFInventory',
            'action': 'GetUserDefinedFields',
            'id': account
        }
    };
    AppConnector.request(params).then(
        function(data) {
            if (data.success) {
              var labels = data.result.data;
              var block = jQuery('.block_LBL_WFINVENTORY_USER_DEFINED');

              jQuery.each(udfArray,function(key,val) {
                if(labels['udf'+val+'_label'].length == 0) {
                  thisInstance.defaultUDFs(val);
                  return true;
                }
                block.find('label').filter(function() {
                  return ($(this).text() === "LBL_WFINVENTORY_UDF_" + val || $(this).text() === "UDF " + val);
                }).text(labels['udf'+val+'_label']);
              });
            } else {
                console.dir('error getting field labels');
            }
        },
        function(err) {
            console.dir('overall error getting field labels');
        }
    );

  },

  registerUpdateUDFs: function() {
    var thisInstance = this;
    jQuery('input[name="wfaccount"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function() {
      thisInstance.updateUDFs();
    });
  },

  registerBasicEvents: function (container) {
      var isEditView = jQuery('#isEditView').length > 0;
      this._super(container);
      this.registerRules(isEditView);
      this.populateArticleInfo();
      this.updateUDFs();
      this.registerUpdateUDFs();
  }

});
