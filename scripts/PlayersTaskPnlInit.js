/*
 * Custom jQuery extension, selects all text in an element
 */
jQuery.fn.selectText = function() {
	// Get element from jQuery container obj
	var element = this[0];
//	console.log(this, element);	// Debug trace
	
	// Create new text range and set to element's contents if none exist
	if (document.body.createTextRange) {
		var range = document.body.createTextRange();
		range.moveToElementText(element);
		range.select();
	// Otherwise remove existing text range(s) and create new selection
	} else if (window.getSelection) {
		var selection = window.getSelection();        
		var range = document.createRange();
		range.selectNodeContents(element);
		selection.removeAllRanges();
		selection.addRange(range);
	}
};

/*
 * Custom jQuery extension, deslects all text in an element
 */
jQuery.fn.deselectText = function() {
	if (window.getSelection) {
		// Get element from jQuery container obj
		var element = this[0];
//		console.log(this, element);	// Debug trace
		
		// Remove existing text range(s)
		var selection = window.getSelection();
		selection.removeAllRanges();
	}
};

var buildSpreadsheet = function () {
	// Build spreadsheet header
	$('#spreadsheet-view-grid').append(
		'<div style="display: table-row;">' +
			'<div class="spreadsheet-header-cell"></div>' +
			'<div class="spreadsheet-header-cell"></div>' +
			'<div class="spreadsheet-header-cell">First Name</div>' +
			'<div class="spreadsheet-header-cell">Last Name</div>' +
			'<div class="spreadsheet-header-cell">Jersey</div>' +
			'<div class="spreadsheet-header-cell">Team</div>' +
		'</div>'
	);
	
	// Build spreadsheet grid from supplied data
	for (var i = 0; i < playersData.length; ++i) {
		$('#spreadsheet-view-grid').append(
			'<div id="row-' + i + '" data-PID="' + playersData[i]['PID'] + '" class="spreadsheet-row">' +
				'<div class="spreadsheet-header-cell">' + (i + 1) + '</div>' +
				'<div id="checkbox-' + i + '" class="spreadsheet-cell-nohilight" onmouseover="hiliteRow(this);" ' +
						'onmouseout="unhiliteRow(this);">' +
					'<input type="checkbox" onclick="selectRow( $(this).parent() );"></input>' +
				'</div>' +
				'<div id="firstName-' + i + '" class="spreadsheet-cell" contenteditable="false" ' +
						'onmouseover="hilightCell(this);" onmouseout="unhilightCell(this);" ' +
						'onclick="editTextCell(this);">' +
					playersData[i]['first_name'] +
				'</div>' +
				'<div id="lastName-' + i + '" class="spreadsheet-cell" contenteditable="false" ' +
						'onmouseover="hilightCell(this);" onmouseout="unhilightCell(this);" ' +
						'onclick="editTextCell(this);">' +
					playersData[i]['last_name'] +
				'</div>' +
				'<div id="number-' + i + '" class="spreadsheet-cell" style="text-align: right;" contenteditable="false" ' +
						'onmouseover="hilightCell(this);" onmouseout="unhilightCell(this);" ' +
						'onclick="editTextCell(this);">' +
					playersData[i]['number'] +
				'</div>' +
				'<div id="team-' + i + '" style="cursor: pointer;" class="spreadsheet-cell" ' +
						'onmouseover="hilightCell(this);" onmouseout="unhilightCell(this);" ' +
						'onclick="editDropDownCell(this, teamIdData);" data-teamId="' + playersData[i]['TID'] + '">' +
					teamIdData[ playersData[i]['TID'] ] +
				'</div>' +
			'</div>'
		);
	}
};

(function ($) {
	
	// Build spreadsheet data view
	buildSpreadsheet();
	
	// Rectify left border height
	$('.selection-panel-lift').css('height', '396px');
	
	// Set up delete function, hook to delete button
	$('#delete-button').click(function () {
		var rows = Object.keys(selectedRows);
		for (var i = 0; i < rows.length; ++i) {
			var rowNum = rows[i].split('-')[1],
				date = new Date(),
				sysHours = date.getHours(),
				sysMinutes = date.getMinutes(),
				sysSeconds = date.getSeconds();
			
			// Get player summary info and message to console
			$('#task-console').append(
				'<div class="console-line-level1">' +
					'(' + ((sysHours < 10) ? '0' + sysHours : sysHours) + ':' +
					((sysMinutes < 10) ? '0' + sysMinutes : sysMinutes) + ':' +
					((sysSeconds < 10) ? '0' + sysSeconds : sysSeconds) + ')' +
					' Deleting record for ' + $('#lastName-' + rowNum).text() + ', ' + $('#firstName-' + rowNum).text() + '...' +
				'</div>'
			);
			
			// Remove player data row
			$('#row-' + rowNum).remove();
		}
		
		// Renumber rows (interface)
		var rows = $('#spreadsheet-view-grid').children('div');
		for (var i = 1; i < rows.length; ++i) {
			$(rows[i]).find('>:first-child').text(i);
		}
		
		$('#task-console').append(
			'<div class="console-line-level0">' +
				'(' + ((sysHours < 10) ? '0' + sysHours : sysHours) + ':' +
				((sysMinutes < 10) ? '0' + sysMinutes : sysMinutes) + ':' +
				((sysSeconds < 10) ? '0' + sysSeconds : sysSeconds) + ')' +
				' Deletion successful...Ready.' +
			'</div>'
		);

		// Empty selected rows registry, disable delete button and scroll to bottom of console
		selectedRows = {};
		$('#delete-button').prop('disabled', true);
		$('#task-console').scrollTop( $('#task-console')[0].scrollHeight )
	});
	
	// Set up update submit function, hook to apply changes button
	$('#submit-button').click(function () {
		var changes = Object.keys(changeHistory),
			updates = {},
			date = new Date(),
			sysHours = date.getHours(),
			sysMinutes = date.getMinutes(),
			sysSeconds = date.getSeconds();
		
		// Organize updates by row
		for (var i = 0; i < changes.length; ++i) {
			var row = changes[i].split('-')[1];		// Get next row changed
			
			if (!updates.hasOwnProperty(row))
				updates[row] = {};
			updates[row][changes[i]] = changeHistory[changes[i]];
		}
		
		// Process updates and post update messages to console
		for (var row in updates) {
			$('#task-console').append(
				'<div class="console-line-level1">' +
					'(' + ((sysHours < 10) ? '0' + sysHours : sysHours) + ':' +
					((sysMinutes < 10) ? '0' + sysMinutes : sysMinutes) + ':' +
					((sysSeconds < 10) ? '0' + sysSeconds : sysSeconds) + ')' +
					' Updating record for ' + $('#lastName-' + row).text() + ', ' + $('#firstName-' + row).text() + '...' +
				'</div>'
			);			
		}
		$('#task-console').append(
			'<div class="console-line-level0">' +
				'(' + ((sysHours < 10) ? '0' + sysHours : sysHours) + ':' +
				((sysMinutes < 10) ? '0' + sysMinutes : sysMinutes) + ':' +
				((sysSeconds < 10) ? '0' + sysSeconds : sysSeconds) + ')' +
				' Update successful...Ready.' +
			'</div>'
		);
		
		// Empty changeHistory cache
		// Disable apply changes button
		changeHistory = {};
		$('#submit-button').prop('disabled', true);
		$('#task-console').scrollTop( $('#task-console')[0].scrollHeight );
	});
	
	// Set welcome message in console
	var date = new Date(),
		sysHours = date.getHours(),
		sysMinutes = date.getMinutes(),
		sysSeconds = date.getSeconds();
	$('#task-console').append(
		'<div class="console-line-level0">' +
			'(' + ((sysHours < 10) ? '0' + sysHours : sysHours) + ':' +
			((sysMinutes < 10) ? '0' + sysMinutes : sysMinutes) + ':' +
			((sysSeconds < 10) ? '0' + sysSeconds : sysSeconds) + ')' +
			' Successfully connected to TBD Park District system...Ready.' +
		'</div>'
	);
	
})(jQuery);