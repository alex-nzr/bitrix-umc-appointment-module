import {Application} from './Application/Application';
import {Extension} from 'main.core';

BX.ready(() => {
    try
    {
        const root = BX.create('div', {
            attrs: {
                id: 'anz-appointment-root'
            }
        });
        document.body.append(root);
        BX.Anz.Appointment.FormVue = new Application(root, Extension.getSettings('anz.appointment.form-vue'));
    }
    catch (e)
    {
        console.log('FormVue extension error', e)
    }
});