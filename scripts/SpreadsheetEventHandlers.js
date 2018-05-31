/*
 * Spreadsheet element event handlers
 * Author: Joshua Boley
 */

var editingCell = {},
	editingDropDown = {},
	selectedRows = {},
	changeHistory = {},
	hiliteRow = function(element) {
		// Abort row hilight if user is editing cell contents
		if (Object.keys(editingCell).length > 0 || Object.keys(editingDropDown).length > 0)
			return;
		
		$(element).parent().addClass('spreadsheet-row-hilight');
	},
	unhiliteRow = function(element) {
		// Abort row unhilight if user is editing cell contents
		if (Object.keys(editingCell).length > 0 || Object.keys(editingDropDown).length > 0)
			return;
		
		$(element).parent().removeClass('spreadsheet-row-hilight');
	},
	selectRow = function(element) {
		// Abort row selection if user is editing cell contents
		if (Object.keys(editingCell).length > 0)
			return;
		
		// Toggle row selection, enable or disable 'delete' button
		var parent = $(element).parent();
		if (parent.hasClass('spreadsheet-row-selected')) {
			parent.removeClass('spreadsheet-row-selected');
			delete selectedRows[ $(element).attr('id') ];
			if (Object.keys(selectedRows).length == 0)
				$('#delete-button').prop('disabled', true);
		}
		else {
			parent.addClass('spreadsheet-row-selected');
			selectedRows[ $(element).attr('id') ] = true;
			$('#delete-button').prop('disabled', false);
		}
	},
	hilightCell = function(element) {
		// Abort if cell is currently being edited
		if (Object.keys(editingCell).length > 0 && element === editingCell['cell']['obj'] ||
				Object.keys(editingDropDown).length > 0 && element === editingDropDown['cell']['obj'])
			return;
		
		$(element).addClass('spreadsheet-cell-hilight');
	},
	unhilightCell = function(element) {
		// Abort if cell is currently being edited
		if (Object.keys(editingCell).length > 0 && element === editingCell['cell']['obj'] ||
				Object.keys(editingDropDown).length > 0 && element === editingDropDown['cell']['obj'])
			return;
		
		$(element).removeClass('spreadsheet-cell-hilight');
	},
	editTextCell = function(element) {
		// Abort if rows have been selected
		if (Object.keys(selectedRows).length > 0)
			return;
		
		// Change edit to current cell if user was editing another
		if (Object.keys(editingCell).length > 0) {
			var oldElement = $(editingCell['cell']['obj']);
			oldElement.removeClass('spreadsheet-cell-edit');
			oldElement.attr('contenteditable', false);
			oldElement.keypress(function () {});
			delete editingCell['cell'];
		}

		// Restore previous value if user was editing a drop-down cell
		if (Object.keys(editingDropDown).length > 0) {
			// Abort if click registered on drop-down cell in edit mode
			if (editingDropDown['cell']['obj'] === element)
				return;
			
			// Destroy drop-down and restore old drop-down cell value
			var oldElement = $(editingDropDown['cell']['obj']);
			oldElement.empty();
			oldElement.text(editingDropDown['cell']['oldVal']);
			oldElement.removeClass('spreadsheet-cell-edit');
			delete editingDropDown['cell'];
		}

		// Prevent cell edit if row is hilighted
		if (Object.keys(selectedRows).length > 0)
			return;
		
		// Make text cell editable and set editing flag
		$(element).attr('contenteditable', true);
		$(element).removeClass('spreadsheet-cell-hilight');
		$(element).addClass('spreadsheet-cell-edit');
		$(element).selectText();
		editingCell['cell'] = {
			'obj': element,
			'oldVal': $(element).text()
		};
		
		// Set text cell to exit edit mode when 'enter' key is pressed
		$(element).keypress(function (event) {
			if (event.which == 13) {
				event.preventDefault();	// Prevent enter keypress event from bubbling up to DOM

				// Abort second event, Chrome appears to double-fire keypress events (down + up?)
				if (!editingCell.hasOwnProperty('cell'))
					return;
				
				$(this).attr('contenteditable', false);
				$(this).keypress(function () {});
				$(element).removeClass('spreadsheet-cell-edit');
				$(element).deselectText();
				
				// Add change to history, including element id and new text value, activate update button
				if ($(this).text() != editingCell['cell']['oldVal']) {
					changeHistory[ $(this).attr('id') ] = $(this).text();
					$('#submit-button').prop('disabled', false);
				}
				
				delete editingCell['cell'];
			}
		});
	},
	editDropDownCell = function(element, optionsList) {
		// Abort if rows have been selected
		if (Object.keys(selectedRows).length > 0)
			return;

		var teamId = $(element).attr('data-teamId');
		
		// Exit edit mode if user was editing text cell
		if (Object.keys(editingCell).length > 0) {
			var oldElement = $(editingCell['cell']['obj']);
			oldElement.removeClass('spreadsheet-cell-edit');
			oldElement.attr('contenteditable', false);
			oldElement.keypress(function () {});
			delete editingCell['cell'];
		}
		
		// Restore previous value if user was editing another drop-down cell
		if (Object.keys(editingDropDown).length > 0) {
			// Abort if click registered on drop-down cell in edit mode
			if (editingDropDown['cell']['obj'] === element)
				return;
			
			// Destroy drop-down and restore old drop-down cell value
			var oldElement = $(editingDropDown['cell']['obj']);
			oldElement.empty();
			oldElement.text(editingDropDown['cell']['oldVal']);
			oldElement.removeClass('spreadsheet-cell-edit');
			delete editingDropDown['cell'];
		}
		
		var oldTextVal = $(element).text();
		$(element).removeClass('spreadsheet-cell-hilight');
		$(element).addClass('spreadsheet-cell-edit');
		$(element).empty();
		editingDropDown['cell'] = {
			'obj': element,
			'oldVal': oldTextVal
		};
		
		var optionCodes = Object.keys(optionsList),
			contentStr = '<select id="drop-down">';
		for (var i = 0; i < optionCodes.length; ++i) {
			contentStr += '<option value="' + optionCodes[i] + '"';
			if (oldTextVal == optionsList[ optionCodes[i] ])
				contentStr += ' selected="selected"'
			contentStr += '>' + optionsList[ optionCodes[i] ] + '</option>';
		}
		contentStr += '</select>';
		$(element).append(contentStr);
		
		$(element).keypress(function (event) {
			if (event.which == 13) {
				event.preventDefault();	// Prevent enter key press event from bubbling up through DOM
				
				// Get new value, destroy drop-down and set as drop-down cell text
				var newTextVal = teamIdData[ $('#drop-down').val() ];
				if (typeof newTextVal == 'undefined')		// Chrome mis-fires event twice, prevents
					return;									//  second fire from destroying selected value
				$(this).empty();
				$(element).text(newTextVal);
				$(element).removeClass('spreadsheet-cell-edit');
				$(this).keypress(function () {});
				
				// Add change to history, including element id and new text value
				if (newTextVal != editingDropDown['cell']['oldVal']) {
					changeHistory[ $(this).attr('id') ] = $(this).text();
					$('#submit-button').prop('disabled', false);
				}
				
				delete editingDropDown['cell'];	// Remove old val cache obj
			}
		});
	};