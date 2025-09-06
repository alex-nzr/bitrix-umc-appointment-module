'use strict';
import {ClassicForm} from './scripts/app';
import {Extension} from 'main.core';

BX.ready(() => {
    try
    {
        const namespace = BX.namespace('BX.Anz.Appointment');
        namespace.ClassicForm = new ClassicForm(Extension.getSettings('anz.appointment.bx_popup'));
        namespace.ClassicForm.run();
    }
    catch (e)
    {
        console.log('App extension error - ', e)
    }
});