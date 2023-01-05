$(function () {
	/*
	const simulation = document.getElementById('simulation');
	simulation && simulation.addEventListener('show.bs.collapse', event => {
		event.target.removeEventListener('show.bs.collapse');
		fetch('/order/simulation')
			.then((response) => response.text())
			.then((text) => {
				document.querySelector('#simulation pre').textContent = text;
			}).finally(() => {
				document.querySelector('#simulation .spinner-border').classList.add('d-none');
			});
	});
	*/

	$('#simulation').on('show.bs.collapse', () => {
		$(this).off('show.bs.collapse');
		fetch('/order/simulation')
			.then((response) => response.text())
			.then((text) => {
				document.querySelector('#simulation pre').textContent = text;
			}).finally(() => {
				document.querySelector('#simulation .spinner-border').classList.add('d-none');
			});
	});

	document.querySelectorAll('form.dynamic-select').forEach(form => {
		form.querySelectorAll('select').forEach(select => {
			select.onchange = () => {
				form.submit();
			}
		});
	});
});
