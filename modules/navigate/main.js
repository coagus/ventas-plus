function show(controller, action, result, request) {
	request = request === '' ? '' : '&' + request;

	$.ajax({
		data: 'controller=' + controller + '&action=' + action + request,
		url: 'modules/navigate/driver.php',
		async: false,
		type: 'post',
		beforeSend: function() {
			$(result).html('<div style="height: 300px;"> <div id="view" class="center-nav"> <img src="images/progress.gif" class="img-responsive"> </div> </div>');
		},
		success: function(response) {
			$(result).html(response);
		}
	});
}
