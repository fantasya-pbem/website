$(function () {
	$('#simulation').on('show.bs.collapse', function () {
		$(this).off('show.bs.collapse');
		$.get('/order/simulation', function (text) {
			$('#simulation pre').text(text);
		}).always(function () {
			$('#simulation .spinner-border').hide();
		});
	});

	$('form.dynamic-select').each(function (i, element) {
		const form = $(element);
		form.find('select').change(function () {
			form.submit();
		});
	});

	$('#newbie-race').change(function (event) {
		const resources = {
			'Aquaner':  [30, 20, 40],
			'Elf':      [10, 30, 50],
			'Halbling': [30, 35, 25],
			'Mensch':   [30, 30, 30],
			'Ork':      [25, 20, 45],
			'Troll':    [50, 20, 20],
			'Zwerg':    [30, 25, 35]
		}
		const race     = event.target.value;
		const resource = resources[race];
		$('#newbie-wood').val(resource[0]);
		$('#newbie-stone').val(resource[1]);
		$('#newbie-iron').val(resource[2]);
	}).change();

	$('#newbie-form .resource').change(function (event) {
		const resource = $(event.target);
		const id       = resource.attr('id');
		var count    = parseInt(resource.val());
		if (count < 0) {
			count = 0;
		} else if (count > 90) {
			count = 90;
		}
		resource.val(count);

		var other1, other2;
		if (id === 'newbie-wood') {
			other1 = $('#newbie-stone');
			other2 = $('#newbie-iron');
		} else if (id === 'newbie-stone') {
			other1 = $('#newbie-wood');
			other2 = $('#newbie-iron');
		} else {
			other1 = $('#newbie-wood');
			other2 = $('#newbie-stone');
		}
		var count1 = parseInt(other1.val()), count2 = parseInt(other2.val());

		var sum = count + count1 + count2;
		while (sum-- > 90) {
			if (count2 > count1) {
				count2--;
			} else {
				count1--;
			}
		}
		other1.val(count1);
		other2.val(count2);
	});
});
