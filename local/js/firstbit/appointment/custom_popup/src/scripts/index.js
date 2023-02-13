/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - index.js
 * 10.07.2022 23:48
 * ==================================================
 */
'use strict';
import {AppointmentSteps} from './appointment/app';

BX.ajax.runComponentAction('anz:appointment.add', 'getResult', {
    mode: 'ajax',
    data: {
        sessid: BX.bitrix_sessid()
    }
})
.then(function (response)
{
    const AppPlace = BX.namespace('Anz.Appointment');
    AppPlace.AppointmentSteps = new AppointmentSteps(response.data);
    AppPlace.AppointmentSteps.run();
})
.catch(function (e)
{
    if (e.errors && BX.type.isArray(e.errors))
        {
            let errorText = '';
            e.errors.forEach(error => {
                errorText = `${errorText} ${error.code} - ${error.message};`;
            })
            console.log(errorText)
        }
        else
        {
            console.log('app data loading error', e);
        }
});