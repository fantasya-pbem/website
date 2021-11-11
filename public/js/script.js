$(function () {
	$('#simulation').on('show.bs.collapse', function () {
		$(this).off('show.bs.collapse');
		$.get('order/simulation', function (text) {
			$('#simulation pre').text(text);
		}).always(function () {
			$('#simulation .spinner-border').hide();
		});
	});
});
