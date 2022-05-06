'use strict';
BX.ajax.runComponentAction('firstbit:appointment.add', 'getResult', {
    mode: 'ajax',
    data: {}
}).then(function (response) {
    console.log(response)
}).catch(function (e) {
    console.log('error', e)
});