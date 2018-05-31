(function ($) {
	
	var onMouseEnterHandler = function(event) {
			var targetId = $(event.currentTarget).attr('id');
			
			// Prevent selected link from being hilited
			if (typeof selected != 'undefined') {
				if (targetId == selected)
					return;
			}
			
			// Add hover css class
			$(event.currentTarget).addClass('selection-container-hover');
		},
		onMouseLeaveHandler = function(event) {
			var targetId = $(event.currentTarget).attr('id');
			
			// Dismiss if selected link
			if (typeof selected != 'undefined') {
				if (targetId == selected)
					return;
			}
			
			// Remove hover css class
			$(event.currentTarget).removeClass('selection-container-hover');
		},
		onClickHandler = function(event) {
			var targetId = $(event.currentTarget).attr('id');
			
			// Prevent selected link from being reselected
			if (typeof selected != 'undefined') {
				if (targetId == selected)
					return;
			}
			
			// Send selection to server
			simFormSubmit('AdminShell.php', document, { 'selected': targetId });
		};

	// Hook mouse event handlers up to selection items
	var links = $('ul.selection-panel').children('li');
	for (var i = 0; i < links.length; ++i) {
		$(links[i]).mouseover(onMouseEnterHandler);
		$(links[i]).mouseleave(onMouseLeaveHandler);
//		$(links[i]).click(onClickHandler);
	}
	
	// If admin tool selection has been made, hilight it
	if (typeof selected != 'undefined')
		$('#' + selected).addClass('selection-container-selected');
	
	// Set up back to main site link
	$('.admin-logo-foreground').click(function () {
		simFormSubmit('index.php', document, {});
	});
	
})(jQuery);