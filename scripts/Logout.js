(function ($) {
	
//	$('#logout-button').click(function (event) {
//		simFormSubmit('index.php', document, { 'logout': 'true' });
//	});
	
	$('#admin-button').click(function () {
		simFormSubmit('AdminShell.php', document, {});
	})
	
})(jQuery);