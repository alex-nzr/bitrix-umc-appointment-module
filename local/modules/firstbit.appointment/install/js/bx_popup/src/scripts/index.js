'use strict';
import {AppointmentSteps} from './appointment/app';

BX.ajax.runComponentAction('firstbit:appointment.add', 'getResult', {
    mode: 'ajax',
    data: {
        sessid: BX.bitrix_sessid()
    }
})
.then(function (response)
{
    const AppPlace = BX.namespace('FirstBit.Appointment');
    AppPlace.AppointmentSteps = new AppointmentSteps(response.data);
    AppPlace.AppointmentSteps.run();
})
.catch(function (e)
{
    if (e.errors && BX.type.isArray(e.errors))
        {
            let errorText = '';
            response.errors.forEach(error => {
                errorText = `${errorText} ${error.code} - ${error.message};`;
            })
            console.log(errorText)
        }
        else
        {
            console.log('app data loading error', e);
        }
});