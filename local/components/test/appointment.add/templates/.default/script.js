"use strict";

BX.ready(function() {
	const btns = document.querySelectorAll('button');
	btns.length && btns.forEach(btn => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			const action = e.target.dataset.action;
			let data = {};
			switch (action) {
				case 'add':
					data = {
						'sessid': BX.bitrix_sessid(),
						'params': {
							'CLINIC_TITLE': 'TEST__CLINIC_TITLE',
							'SPECIALTY': 'TEST__SPECIALTY',
							'DOCTOR_NAME': 'TEST__DOCTOR_NAME',
							'SERVICE_TITLE': 'TEST__SERVICE_TITLE',
							'DATETIME_VISIT': '2022-12-21T14:30:00',
							'PATIENT_NAME': 'TEST__USER_NAME',
							'PATIENT_PHONE': 'TEST__USER_PHONE',
							'PATIENT_EMAIL': 'TEST__USER_EMAIL',
							'COMMENT': 'TEST__COMMENT',
							'STATUS_1C': 'TEST__STATUS_1C',
						}
					}
					break;
				case 'update':
					data = {
						'sessid': BX.bitrix_sessid(),
						'id': 3,
						'params': {
							'STATUS_1C': 'updated item',
							'USER_ID': 1
						}
					}
					break;
				case 'delete':
					data = {
						id: 6
					}
					break;
			}

			const request = BX.ajax.runComponentAction('test:appointment.add', action, {
				mode:'class',
				data: data
			});

			btn.classList.add('ui-btn-clock')
			request.then((response) => {
				if (!response){
					console.log('response is empty');
					return;
				}

				if (response.status === 'success'){
					if (response.data?.error){
						console.log(response.data.error)
					}else{
						console.log(response)
					}
				}
				else{
					console.log(response.errors);
				}
				btn.classList.remove('ui-btn-clock');
			})
				.catch(e => console.log(e))
		})
	})

});