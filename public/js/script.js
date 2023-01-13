document.addEventListener('readystatechange', () => {
	console.log('readyState is ' + document.readyState);

	if (document.readyState === 'complete') {
		document.querySelectorAll('form.dynamic-select').forEach(form => {
			form.querySelectorAll('select').forEach(select => {
				select.onchange = () => {
					form.submit();
				}
			});
		});

		const simulation = document.getElementById('simulation');
		const fetchSimulation = function () {
			fetch('/befehle-simulieren')
				.then((response) => response.text())
				.then((text) => {
					document.querySelector('#simulation pre').textContent = text;
				}).finally(() => {
				document.querySelector('#simulation .spinner-border').classList.add('d-none');
			});
			simulation.removeEventListener('show.bs.collapse', fetchSimulation);
		};
		simulation && simulation.addEventListener('show.bs.collapse', fetchSimulation);
	}
});
