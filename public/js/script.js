document.addEventListener('readystatechange', () => {
	if (document.readyState === 'complete') {
		document.querySelectorAll('form.dynamic-select').forEach(form => {
			form.querySelectorAll('select').forEach(select => {
				select.onchange = () => {
					form.submit();
				}
			});
		});

		const simulationToggle = document.getElementById('simulation');
		const simulationId = simulationToggle.dataset['party'];
		const simulationModeAll = document.getElementById('simulation-mode-all');
		const simulationModeProblems = document.getElementById('simulation-mode-problems');
		const simulationText = document.querySelector('#simulation pre');
		let simulation;
		let simulationMode = 'problems';

		const switchSimulationMode = function () {
			let text;
			if (simulationMode === 'problems') {
				text = '';
				let unit = '', messages = [];
				simulation.split("\n").forEach((line) => {
					if (line === "") {
						if (messages.length) {
							if (text) {
								text += "\n";
							}
							text += unit + "\n" + messages.join("\n") + "\n";
							messages = [];
						}
					} else {
						if (line.charAt(0) === '[') {
							if (line.charAt(1) !== ' ') {
								messages.push(line);
							}
						} else {
							unit = line;
						}
					}
				});
				simulationMode = 'all';
				simulationModeProblems.classList.add('visually-hidden');
				simulationModeAll.classList.remove('visually-hidden');
			} else {
				text = simulation;
				simulationMode = 'problems';
				simulationModeAll.classList.add('visually-hidden');
				simulationModeProblems.classList.remove('visually-hidden');
			}
			simulationText.textContent = text;
		};

		const fetchSimulation = function (event) {
			fetch('/befehle-simulieren/' + simulationId)
				.then((response) => response.text())
				.then((text) => {
					simulation = text;
					switchSimulationMode();
					simulationText.classList.remove('visually-hidden');
				}).finally(() => {
				document.querySelector('#simulation .spinner-border').classList.add('d-none');
			});
			simulationToggle.removeEventListener('show.bs.collapse', fetchSimulation);
		};

		simulationToggle && simulationToggle.addEventListener('show.bs.collapse', fetchSimulation);
		simulationModeAll && simulationModeAll.addEventListener('click', switchSimulationMode);
		simulationModeProblems && simulationModeProblems.addEventListener('click', switchSimulationMode);
	}
});
