/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js('OPList_Edit_Js', {}, {
	showAlertBox : function(data){
		var aDeferred = jQuery.Deferred();
		var bootBoxModal = bootbox.alert(data['message'], function(result) {
			if(result){
				aDeferred.reject(); //we only want the button to make the modal box disappear
			} else{
				aDeferred.reject();
			}
		});
		bootBoxModal.on('hidden',function(e){
			//In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
			// modal open
			if(jQuery('#globalmodal').length > 0) {
				// Mimic bootstrap modal action body state change
				jQuery('body').addClass('modal-open');
			}
		})
		return aDeferred.promise();
	},
    //stolen from inventory edit.js because it's incredibly useful
    getQueryVariable: function(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0; i<vars.length; i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable) {return pair[1];}
        }
        return(false);
    },
	registerAssignedToChange: function(){
        var thisInstance = this;
        jQuery('select[name="agentid"]').on('change', function(){
            //grab what was selected before
            var prevSelected = [];
            jQuery('li.select2-search-choice').each(function(){
                console.dir(jQuery.trim(jQuery(this).find('div').html()));
                prevSelected.push(jQuery.trim(jQuery(this).find('div').html()));
            });
            console.dir('prevSelected : ');
            console.dir(prevSelected);
            var dataURL = 'index.php?module=OPList&action=GetOpListTypes&smowner='+jQuery(this).find('option:selected').val()+'&record='+thisInstance.getQueryVariable('record');
            AppConnector.request(dataURL).then(
                function(data) {
                    if (data.success) {
                        jQuery('select[name="op_move_type[]"]').find('option').remove();
                        for(var i in data.result){
                            var html = '<option value="'+i+'">'+data.result[i]+'</option>';
                            if (prevSelected.indexOf(data.result[i].replace('&','&amp;')) >= 0){
                                html = '<option value="'+i+'" selected>'+data.result[i]+'</option>';
                            }
                            jQuery('select[name="op_move_type[]"]').append(html);
                        }
                        jQuery('select[name="op_move_type[]"]').select2("destroy");
                        jQuery('select[name="op_move_type[]"]').select2();
                    }
                    else {
                        //alert(data.error.code + ": " + data.error.message);
                        thisInstance.showAlertBox({'message': data.error.code + ": " + data.error.message}).then(
                            function (e) {
                            },
                            function (error, err) {
                            }
                        );
                    }
                });
        }).trigger('change');
	},
	registerAddSection: function(){
		var thisInstance = this;
		jQuery('button[name="addSection"]').off('click').on('click', function() {
			var defaultClass = 'defaultSection';
			var sectionContainer = jQuery('div.sectionsContainer');
			var defaultSection = jQuery('table.'+defaultClass+'[name="opListSectionBlock"]');
			var newSection = defaultSection.clone(true,true);
			var numSections = jQuery('input[name="numSections"]').val();
			numSections++;
			var sectionOrder = numSections - jQuery('table[name^="del_section_"]').length;
			jQuery('input[name="numSections"]').val(numSections);
			newSection.removeClass('hide '+defaultClass);
			newSection.find('input[name="sectionOrder"], button, i[name="deleteSectionButton"]').each(function() {
				if(jQuery(this).attr('name')){
					jQuery(this).attr('name', jQuery(this).attr('name')+'_'+numSections);
				}
			});
			newSection.find('input[name="sectionOrder_'+numSections+'"]').val(sectionOrder);
			newSection.find('input[name="numQuestions"]').attr('name', 'numQuestions_'+numSections).val(0);
			newSection.find('tr.defaultQuestion').remove();
			newSection.find('tr[class^="defaultAnswer_"]').remove();
			newSection.find('input[name="section_name"]').attr('name','section_name_'+numSections).attr('id','section_name_'+numSections);
			newSection.find('.opListSection').removeClass('opListSection').addClass('opListSection_'+numSections);
			newSection.find('tr.sectionRow').removeClass('defaultSectionRow sectionRow').addClass('sectionRow_'+numSections);

			//add it to the container
			newSection.appendTo(sectionContainer).after('<br>');
			//register the add question buttons
			jQuery('button[name="addQuestion1_'+numSections+'"]').off('click').on('click', function(){thisInstance.addQuestion(jQuery(this));});
			jQuery('button[name="addQuestion2_'+numSections+'"]').off('click').on('click', function(){thisInstance.addQuestion(jQuery(this));});
			//register the delete button
			jQuery('i[name="deleteSectionButton_'+numSections+'"]').off('click').on('click', function(){thisInstance.deleteSection(numSections);});
			//register the reorder buttons
			jQuery('button[name="moveSectionUp_'+numSections+'"]').off('click').on('click', function(){thisInstance.reorderSection(numSections,'Up');});
			jQuery('button[name="moveSectionDown_'+numSections+'"]').off('click').on('click', function(){thisInstance.reorderSection(numSections,'Down');});
			//rename the table so we can find it easier later
			jQuery('tbody.opListSection_'+numSections).closest('table[name="opListSectionBlock"]').attr('name', 'opListSectionBlock_'+numSections);
		});
	},
	deleteSection : function(sectionNum){
		jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').next('br').remove();
var sectionToDelete = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]');
sectionToDelete.html('<input type="hidden" id="delete_section_'+sectionNum+'" name="delete_section_'+sectionNum+'" value="1">');
sectionToDelete.attr('name', 'del_opListSectionBlock_'+sectionNum);
sectionToDelete.addClass('hide');
jQuery('table[name^="opListSectionBlock_"]').last().next('br').after(sectionToDelete);
},
reorderSection : function(sectionNum, direction){
	if(direction == 'Up'){
		//select the last previous opListSectionBlock_
		var prevSection = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').prevAll('table[name^="opListSectionBlock_"]').first();
		//check if there is a prev section if there is do the move
		if(prevSection.length){
			//select the section to move
			var sectionToMove = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').add(jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').prev());
			//detach it so it can be moved
			sectionToMove = sectionToMove.detach();
			//put it before the last previous section to move it.
			prevSection.prev('br').before(sectionToMove);
			//change the section orders to show this move for when we save
			var thisSectionOrder = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').find('input[name="sectionOrder_'+sectionNum+'"]');
			thisSectionOrder.val(parseInt(thisSectionOrder.val())-1);
			var prevSectionOrder = prevSection.find('input[name^="sectionOrder_"]');
			prevSectionOrder.val(parseInt(prevSectionOrder.val())+1);
		}
	} else if (direction == 'Down'){
		//select the first next opListSectionBlock_
		var nextSection = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').nextAll('table[name^="opListSectionBlock_"]').first();
		//check if there is a next section if there is do the move
		if(nextSection.length){
			//select the section to move
			var sectionToMove = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').add(jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').prev());
			//detach it so it can be moved
			var sectionToMove = sectionToMove.detach();
			//put it after the first next section to move it
			nextSection.after(sectionToMove);
			//change the section orders to show this move for when we save
			var thisSectionOrder = jQuery('table[name="opListSectionBlock_'+sectionNum+'"]').find('input[name="sectionOrder_'+sectionNum+'"]');
			thisSectionOrder.val(parseInt(thisSectionOrder.val())+1);
			var nextSectionOrder = nextSection.find('input[name^="sectionOrder_"]');
			nextSectionOrder.val(parseInt(nextSectionOrder.val())-1);
		}
	}
},
addQuestion : function(field) {
	var thisInstance = this;
	var sectionNum = field.attr('name').split('_')[1];
	var questionNum = jQuery('input[name="numQuestions_'+sectionNum+'"]').val();
	var questionOrder = jQuery('input[name="numQuestions_'+sectionNum+'"]').val() - jQuery('tr[class^="del_question_'+sectionNum+'"]').length + 1;
	questionNum++;
	newQuestion = jQuery('.defaultQuestion').clone(true,true);
	newQuestion.find('input, select, button, textarea, i').each(function(){
		if(jQuery(this).attr('name') ==  'question_order'){
			jQuery(this).val(questionOrder);
		}
		jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sectionNum+'_'+questionNum);
		if(jQuery(this).attr('id')){
			jQuery(this).attr('id', jQuery(this).attr('id')+'_'+sectionNum+'_'+questionNum);
		}
	});
	jQuery('.opListSection_'+sectionNum).children('tr:not([class^="del_question_"])').last().after(newQuestion);
	jQuery('.opListSection_'+sectionNum).find('tr.defaultQuestion').removeClass('defaultQuestion').addClass('question_'+sectionNum+'_'+questionNum);
	jQuery('select#question_type_'+sectionNum+'_'+questionNum).chosen();
	jQuery('select#question_type_'+sectionNum+'_'+questionNum).siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
		thisInstance.addQuestionTypeFields(jQuery(this).find('.result-selected'));
	});
	jQuery('input[name="numQuestions_'+sectionNum+'"]').val(questionNum);

	//register the delete button for this question
	jQuery('tr.question_'+sectionNum+'_'+questionNum).find('i[name="deleteQuestionButton_'+sectionNum+'_'+questionNum+'"]').on('click', function() {
		thisInstance.deleteQuestion(sectionNum,questionNum);
	});
	//register the reorder buttons for this question
	jQuery('tr.question_'+sectionNum+'_'+questionNum).find('button[name="moveQuestionUp_'+sectionNum+'_'+questionNum+'"]').on('click', function() {
		thisInstance.reorderQuestion(sectionNum,questionNum,'Up');
	});
	jQuery('tr.question_'+sectionNum+'_'+questionNum).find('button[name="moveQuestionDown_'+sectionNum+'_'+questionNum+'"]').on('click', function() {
		thisInstance.reorderQuestion(sectionNum,questionNum,'Down');
	});
},
deleteQuestion: function(sectionNum,questionNum){
	//everything after what we want to delete's order should decrease by one
	jQuery('tr.question_'+sectionNum+'_'+questionNum).nextAll('tr').find('input[name^="question_order_'+sectionNum+'_"]').each(function() {
		jQuery(this).val(parseInt(jQuery(this).val())-1);
	});
	//keep the label row for out notifier to delete the question on save
	var deletedQuestion = jQuery('tr.questionLabelRow.question_'+sectionNum+'_'+questionNum).detach();
	deletedQuestion.html('<input type="hidden" id="delete_question_'+sectionNum+'_'+questionNum+'" name="delete_question_'+sectionNum+'_'+questionNum+'" value="1">');
	deletedQuestion.removeClass('questionLabelRow question_'+sectionNum+'_'+questionNum).addClass('del_question_'+sectionNum+'_'+questionNum);
	//get rid of everything else that has to do with the question
	jQuery('tr.question_'+sectionNum+'_'+questionNum).remove();
	jQuery('tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]').remove();
	//move it to the end so it's out of the way of reorder
	jQuery('tbody.opListSection_'+sectionNum).find('tr').last().after(deletedQuestion);
},
reorderQuestion: function(sectionNum,questionNum,direction){
	if(direction == 'Up'){
		var prevRow = jQuery('tr.question_'+sectionNum+'_'+questionNum).first().prev('tr');
		var prevRowClass = prevRow.attr('class');
		if(prevRowClass){
			if(prevRowClass.substring(0,8) == 'question' || prevRowClass.substring(0,6) == 'answer' ){
				var parts = prevRowClass.split('_');
				var firstPrevRow = jQuery('tr.questionLabelRow.question_'+parts[1]+'_'+parts[2]);
				var thisQuestion = jQuery('tr.question_'+sectionNum+'_'+questionNum+', tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]').detach();
				firstPrevRow.before(thisQuestion);
				var thisOrderCount = jQuery('tr.question_'+sectionNum+'_'+questionNum).find('input[name="question_order_'+sectionNum+'_'+questionNum+'"]');
				thisOrderCount.val(parseInt(thisOrderCount.val())-1);
				var nextOrderCount = jQuery('tr[class="question_'+parts[1]+'_'+parts[2]+'"]').find('input[name="question_order_'+parts[1]+'_'+parts[2]+'"]');
				nextOrderCount.val(parseInt(nextOrderCount.val())+1);
			}
		}
	} else if (direction == 'Down'){
		var nextRow = jQuery('tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]').last().next('tr').next('tr')?jQuery('tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]').last().next('tr').next('tr'):jQuery('tr.question_'+sectionNum+'_'+questionNum).last().next('tr').next('tr');
		var nextRowClass = nextRow.attr('class');
		if(nextRowClass){
			if(nextRowClass.substring(0,8) == 'question'){
				var parts = nextRowClass.split('_');
				var lastNextRow = jQuery('tr[class^="answer_'+parts[1]+'_'+parts[2]+'_"]').last().length?jQuery('tr[class^="answer_'+parts[1]+'_'+parts[2]+'_"]').last():jQuery('tr.question_'+parts[1]+'_'+parts[2]).last();
				jQuery('tr.questionLabelRow.question_'+parts[1]+'_'+parts[2]);
				var thisQuestion = jQuery('tr.question_'+sectionNum+'_'+questionNum+', tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]').detach();
				lastNextRow.after(thisQuestion);
				var thisOrderCount = jQuery('tr.question_'+sectionNum+'_'+questionNum).find('input[name="question_order_'+sectionNum+'_'+questionNum+'"]');
				thisOrderCount.val(parseInt(thisOrderCount.val())+1);
				var nextOrderCount = jQuery('tr[class="question_'+parts[1]+'_'+parts[2]+'"]').find('input[name="question_order_'+parts[1]+'_'+parts[2]+'"]');
				nextOrderCount.val(parseInt(nextOrderCount.val())-1);
			}
		}
	}
},
addQuestionTypeFields: function(field) {
	var thisInstance = this;
	var parts = field.attr('id').split('_');
	var sectionNum = parts[2];
	var questionNum = parts[3];
	var selectName = parts[0]+'_'+parts[1]+'_'+sectionNum+'_'+questionNum;
	var selected = jQuery('select[name="'+selectName+'"]').val();
	var questionType = '';
	switch(selected){
		case 'Text':
			questionType = 'text';
			break;
		case 'Yes/No':
			questionType = 'bool';
			break;
		case 'Date':
			questionType = 'date';
			break;
		case 'Date and Time':
			questionType = 'datetime';
			break;
		case 'Time':
			questionType = 'time';
			break;
		case 'Quantity':
			questionType = 'number';
			break;
		case 'Multiple Choice':
			questionType = 'multi';
			break;
		default:
			break;
	}
	var defaultAnswer = jQuery('tr.defaultAnswer_'+questionType).clone(true,true);
	defaultAnswer.find('input, select, button, textarea').each(function(){
		jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sectionNum+'_'+questionNum);
		if(jQuery(this).attr('id')){
			jQuery(this).attr('id', jQuery(this).attr('id')+'_'+sectionNum+'_'+questionNum);
		}
	});
	var currentAnswerRow = jQuery('tr.question_'+sectionNum+'_'+questionNum).next('tr[class^="answer_'+sectionNum+'_'+questionNum+'_"]');
	if(currentAnswerRow){
		if(currentAnswerRow.attr('class') == 'answer_'+sectionNum+'_'+questionNum+'_'+questionType){
			return; //we already have an answer of this type for this question
		} else if (currentAnswerRow.attr('class') == 'answer_'+sectionNum+'_'+questionNum+'_multi') {
			currentAnswerRow.next('tr[class="answer_'+sectionNum+'_'+questionNum+'_multi"]').remove();
			currentAnswerRow.remove();
		} else {
			currentAnswerRow.remove();
		}
	}
	jQuery('tr.question_'+sectionNum+'_'+questionNum).last().after(defaultAnswer);
	jQuery('tr.question_'+sectionNum+'_'+questionNum).siblings('tr[class^="defaultAnswer_"]').each(function() {
		jQuery(this).removeClass('defaultAnswer_'+questionType).addClass('answer_'+sectionNum+'_'+questionNum+'_'+questionType);
	});
	if(questionType == 'multi'){
		jQuery('tr.answer_'+sectionNum+'_'+questionNum+'_'+questionType).find('tr.defaultMultiOption').remove();
		jQuery('button[name="addMultiOption1_'+sectionNum+'_'+questionNum+'"]').off('click').on('click', function() {
			thisInstance.addMultiOption(sectionNum, questionNum);
		}).trigger('click');
		jQuery('button[name="addMultiOption2_'+sectionNum+'_'+questionNum+'"]').off('click').on('click', function() {
			thisInstance.addMultiOption(sectionNum, questionNum);
		});
		jQuery('input:checkbox[name="default_answer_select_multiple_'+sectionNum+'_'+questionNum+'"]').off('change').on('change', function() {
			thisInstance.multipleAllowedToggle(sectionNum, questionNum);
		});
	}
	app.registerEventForDatePickerFields();
},
addMultiOption: function(sectionNum, questionNum) {
    var thisInstance = this;
	var multiAnswerRoot = jQuery('tr.answer_'+sectionNum+'_'+questionNum+'_multi');
	var multipleAllowed = jQuery('input:checkbox[name="default_answer_select_multiple_'+sectionNum+'_'+questionNum+'"]').prop('checked');
	var optionNum = multiAnswerRoot.find('input[name^="numOptions"]').val();
	optionNum++;
	var optionOrder = optionNum - jQuery('tr[class^="del_option_'+sectionNum+'_'+questionNum+'_"]').length;
	multiAnswerRoot.find('input[name^="numOptions"]').val(optionNum);

	newOption = jQuery('tr.defaultMultiOption').clone(true,true);
	newOption.find('input, select, button, textarea, i').each(function(){
		if(jQuery(this).attr('name') != 'defaultMultiOption' && jQuery(this).attr('name') != 'defaultMultiOption_prev'){
			if(jQuery(this).attr('name') ==  'option_order'){
				jQuery(this).val(optionOrder);
			}
			jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sectionNum+'_'+questionNum+'_'+optionNum);
			if(jQuery(this).attr('id')){
				jQuery(this).attr('id', jQuery(this).attr('id')+'_'+sectionNum+'_'+questionNum);
			}
		} else {
			if(jQuery(this).attr('name') == 'defaultMultiOption'){
				jQuery(this).val(optionNum);
			}
			jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sectionNum+'_'+questionNum);
			if(jQuery(this).attr('id')){
				jQuery(this).attr('id', jQuery(this).attr('id')+'_'+sectionNum+'_'+questionNum);
			}
		}
	});
	if(multipleAllowed){
		if(!newOption.find('td.multipleNotAllowed').hasClass('hide')){
			newOption.find('td.multipleNotAllowed').addClass('hide');
		}
		if(newOption.find('td.multipleAllowed').hasClass('hide')){
			newOption.find('td.multipleAllowed').removeClass('hide');
		}
	}
	//insert the new option into the dom
	multiAnswerRoot.find('tr:not([class^="del_option_"])').last().after(newOption);
	//register delete button
	multiAnswerRoot.find('i[name="deleteMultiOption_'+sectionNum+'_'+questionNum+'_'+optionNum+'"]').off('click').on('click', function() {
		thisInstance.deleteOption(sectionNum,questionNum,optionNum);
	});
	//register the reorder buttons
	multiAnswerRoot.find('button[name="moveOptionUp_'+sectionNum+'_'+questionNum+'_'+optionNum+'"]').off('click').on('click', function() {
		thisInstance.reorderOption(sectionNum,questionNum,optionNum,'Up');
	});
	multiAnswerRoot.find('button[name="moveOptionDown_'+sectionNum+'_'+questionNum+'_'+optionNum+'"]').off('click').on('click', function() {
		thisInstance.reorderOption(sectionNum,questionNum,optionNum,'Down');
	});
	multiAnswerRoot.find('tr.defaultMultiOption').removeClass('defaultMultiOption').addClass('option_'+sectionNum+'_'+questionNum+'_'+optionNum);
},
deleteOption: function(sectionNum,questionNum,optionNum){
	//everything after what we want to delete's order should decrease by one
	jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).nextAll('tr').find('input[name^="option_order_'+sectionNum+'_'+questionNum+'_"]').each(function() {
		jQuery(this).val(parseInt(jQuery(this).val())-1);
	});
	//delete the item by replaceing the TR with an input letting save know to delete it
	jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).removeClass('option_'+sectionNum+'_'+questionNum+'_'+optionNum).addClass('del_option_'+sectionNum+'_'+questionNum+'_'+optionNum).html('<input type="hidden" id="delete_option_'+sectionNum+'_'+questionNum+'_'+optionNum+'" name="delete_option_'+sectionNum+'_'+questionNum+'_'+optionNum+'" value="1">');
	//move it to the end so it's out of the way of reorder
	var deletedLine = jQuery('tr.del_option_'+sectionNum+'_'+questionNum+'_'+optionNum).detach();
	jQuery('tr[class^="option_'+sectionNum+'_'+questionNum+'_"]').last().after(deletedLine);

},
reorderOption: function(sectionNum,questionNum,optionNum,direction){
	if(direction == 'Up'){
		var prevRow = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).prev('tr');
		var prevRowClass = prevRow.attr('class');
		if(prevRowClass){
			if(prevRowClass.substring(0,6) == 'option'){
				var thisRow = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum);
				var copy = thisRow.clone(true,true);
				thisRow.remove();
				prevRow.before(copy);
				var thisOrderCount = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).find('input[name="option_order_'+sectionNum+'_'+questionNum+'_'+optionNum+'"]');
				thisOrderCount.val(parseInt(thisOrderCount.val())-1);
				var prevOrderCount = prevRow.find('input[name^="option_order_'+sectionNum+'_'+questionNum+'_"]');
				prevOrderCount.val(parseInt(prevOrderCount.val())+1);
			}
		}
	} else if (direction == 'Down'){
		var nextRow = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).next('tr');
		var nextRowClass = nextRow.attr('class');
		if(nextRowClass){
			if(nextRowClass.substring(0,6) == 'option'){
				var thisRow = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum);
				var copy = thisRow.clone(true,true);
				thisRow.remove();
				nextRow.after(copy);
				var thisOrderCount = jQuery('tr.option_'+sectionNum+'_'+questionNum+'_'+optionNum).find('input[name="option_order_'+sectionNum+'_'+questionNum+'_'+optionNum+'"]');
				thisOrderCount.val(parseInt(thisOrderCount.val())+1);
				var nextOrderCount = nextRow.find('input[name^="option_order_'+sectionNum+'_'+questionNum+'_"]');
				nextOrderCount.val(parseInt(nextOrderCount.val())-1);
			}
		}
	}
},
multipleAllowedToggle: function(sectionNum, questionNum) {
	var multiAnswerRoot = jQuery('tr.answer_'+sectionNum+'_'+questionNum+'_multi');
	var multipleAllowed = jQuery('input:checkbox[name="default_answer_select_multiple_'+sectionNum+'_'+questionNum+'"]').prop('checked');
	if(multipleAllowed){
		if(!multiAnswerRoot.find('td.multipleNotAllowed').hasClass('hide')){
			multiAnswerRoot.find('td.multipleNotAllowed').addClass('hide');
		}
		if(multiAnswerRoot.find('td.multipleAllowed').hasClass('hide')){
			multiAnswerRoot.find('td.multipleAllowed').removeClass('hide');
		}
	} else {
		if(!multiAnswerRoot.find('td.multipleAllowed').hasClass('hide')){
			multiAnswerRoot.find('td.multipleAllowed').addClass('hide');
		}
		if(multiAnswerRoot.find('td.multipleNotAllowed').hasClass('hide')){
			multiAnswerRoot.find('td.multipleNotAllowed').removeClass('hide');
		}
	}
	multiAnswerRoot.find('input[name="defaultMultiOption_'+sectionNum+'_'+questionNum+'"]').prop('checked',false);
	multiAnswerRoot.find('input[name^="default_multi_option_'+sectionNum+'_'+questionNum+'_"]').prop('checked',false);
},
registerLoadedItems: function() {
	console.dir('trying to do stuff');
	var thisInstance = this;
	jQuery('table[name^="opListSectionBlock_"]').each(function(){
		var sectionId = jQuery(this).attr('name').split('_')[1];
		thisInstance.registerSectionLevelButtons(sectionId);
		jQuery(this).find('tr[class^="question_'+sectionId+'_"]').each(function(){
			var questionId = jQuery(this).attr('class').split('_')[2];
			thisInstance.registerQuestionLevelButtons(sectionId,questionId);
			jQuery('tr[class^="option_'+sectionId+'_'+questionId+'_"]').each(function(){
				var optionId = jQuery(this).attr('class').split('_')[3];
				thisInstance.registerOptionLevelButtons(sectionId,questionId,optionId);
			});
		});
	});
},
registerSectionLevelButtons: function(sectionId){
	var thisInstance = this;
	//register the add question buttons
	jQuery('button[name="addQuestion1_'+sectionId+'"]').off('click').on('click', function(){thisInstance.addQuestion(jQuery(this));});
	jQuery('button[name="addQuestion2_'+sectionId+'"]').off('click').on('click', function(){thisInstance.addQuestion(jQuery(this));});
	//register the delete button
	jQuery('i[name="deleteSectionButton_'+sectionId+'"]').off('click').on('click', function(){thisInstance.deleteSection(sectionId);});
	//register the reorder buttons
	jQuery('button[name="moveSectionUp_'+sectionId+'"]').off('click').on('click', function(){thisInstance.reorderSection(sectionId,'Up');});
	jQuery('button[name="moveSectionDown_'+sectionId+'"]').off('click').on('click', function(){thisInstance.reorderSection(sectionId,'Down');});
},
registerQuestionLevelButtons: function(sectionId, questionId){
	var thisInstance = this;
	//register the delete button for this question
	jQuery('tr.question_'+sectionId+'_'+questionId).find('i[name="deleteQuestionButton_'+sectionId+'_'+questionId+'"]').off('click').on('click', function() {
		thisInstance.deleteQuestion(sectionId,questionId);
	});
	//register the reorder buttons for this question
	jQuery('tr.question_'+sectionId+'_'+questionId).find('button[name="moveQuestionUp_'+sectionId+'_'+questionId+'"]').off('click').on('click', function() {
		thisInstance.reorderQuestion(sectionId,questionId,'Up');
	});
	jQuery('tr.question_'+sectionId+'_'+questionId).find('button[name="moveQuestionDown_'+sectionId+'_'+questionId+'"]').off('click').on('click', function() {
		thisInstance.reorderQuestion(sectionId,questionId,'Down');
	});
	//register the question type changes
	jQuery('select#question_type_'+sectionId+'_'+questionId).siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
		thisInstance.addQuestionTypeFields(jQuery(this).find('.result-selected'));
	});
	//register multi option stuff
	jQuery('button[name="addMultiOption1_'+sectionId+'_'+questionId+'"]').off('click').on('click', function() {
		thisInstance.addMultiOption(sectionId, questionId);
	});
	jQuery('button[name="addMultiOption2_'+sectionId+'_'+questionId+'"]').off('click').on('click', function() {
		thisInstance.addMultiOption(sectionId, questionId);
	});
	jQuery('input:checkbox[name="default_answer_select_multiple_'+sectionId+'_'+questionId+'"]').off('change').on('change', function() {
		thisInstance.multipleAllowedToggle(sectionId, questionId);
	});
},
registerOptionLevelButtons: function(sectionId,questionId,optionId){
	var thisInstance = this;
	//register the delete button
	jQuery('i[name="deleteMultiOption_'+sectionId+'_'+questionId+'_'+optionId+'"]').off('click').on('click', function() {
		thisInstance.deleteOption(sectionId,questionId,optionId);
	});
	//register the reorder buttons
	jQuery('button[name="moveOptionUp_'+sectionId+'_'+questionId+'_'+optionId+'"]').off('click').on('click', function() {
		thisInstance.reorderOption(sectionId,questionId,optionId,'Up');
	});
	jQuery('button[name="moveOptionDown_'+sectionId+'_'+questionId+'_'+optionId+'"]').off('click').on('click', function() {
		thisInstance.reorderOption(sectionId,questionId,optionId,'Down');
	});
},
registerEvents: function(){
	this.registerAddSection();
	this.registerLoadedItems();
	//This is important for when the owner is changed they can't set another oplist per type
	this.registerAssignedToChange();
	this._super();
}
});
