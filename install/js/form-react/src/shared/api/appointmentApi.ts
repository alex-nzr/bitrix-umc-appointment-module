import {Doctor} from "../../entities/doctor/model";
import {Slot} from "../../entities/slot/model";
import {Appointment, Reserve} from "../../entities/appointment/model";
import {Clinic} from "../../entities/clinic/model";
import {Service} from "../../entities/service/model";

interface API_ERROR {
    code?: string|number
    message?: string
    customData: any
}

interface API_RESPONSE {
    status: string
    data?: object,
    errors: Array<API_ERROR>
}

const API_URL = '/bitrix/services/main/ajax.php';
const CONTROLLER = 'anz:appointment.Appointment';

// @ts-ignore
const sessid = window.BX?.bitrix_sessid();

const prepareResponse = async (res: Response) => {
    if (res.ok) {
        const data: API_RESPONSE = await res.json()
        // @ts-ignore
        return data.status !== 'success' ? Promise.reject(data.errors) : Promise.resolve(data.data);
    } else {
        return Promise.reject(`Api error. Status code ${res.status}`);
    }
}

export const appointmentApi = {
    async getClinics(): Promise<Clinic[]> {
        const action = `${CONTROLLER}.getClinics`;
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: '',
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async getDoctors(): Promise<Doctor[]> {
        const action = `${CONTROLLER}.getEmployees`;
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: '',
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async getServices(clinicUid: string): Promise<Service[]> {
        const action = `${CONTROLLER}.getServices`;
        const data = new FormData();
        data.set('clinicUid', clinicUid); //Сюда передать uid выбранной юзером клиники
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async getSlots(clinicUid: string, doctorUid: string): Promise<Slot[]> {
        const action = `${CONTROLLER}.getSchedule`;
        const data = new FormData();
        data.set('clinicUid', clinicUid); //Сюда передать uid выбранной юзером клиники
        data.set('employeeUid', doctorUid); //Сюда передать uid выбранного юзером доктора
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async bookSlot(reserve: Reserve) {
        const action = `${CONTROLLER}.bookSlot`;
        const data = new FormData();
        data.set('clinicUid', reserve.clinicUid);
        data.set('employeeUid', reserve.doctorUid);
        data.set('dateTimeBegin', `${reserve.date}T${reserve.timeBegin}:00`);
        data.set('serviceDuration', `${reserve.duration}`);
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async sendAppointment(appointment: Appointment) {
        const action = `${CONTROLLER}.sendAppointment`;
        const data = new FormData();
        data.set('clinicUid', appointment.clinicUid);
        data.set('employeeUid', appointment.doctorUid);
        data.set('dateTimeBegin', `${appointment.date}T${appointment.timeBegin}:00`);
        data.set('serviceDuration', `${appointment.duration}`);
        //todo fill other fields or send JSON.stringify(appointment)
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async sendConfirmCode(phone: string = '', email: string = '') {
        const action = `${CONTROLLER}.sendConfirmCode`;
        const data = new FormData();
        data.set('phone', phone);
        data.set('email', email);
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async verifyConfirmCode(code: string) {
        const action = `${CONTROLLER}.sendConfirmCode`;
        const data = new FormData();
        data.set('code', code);
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async deleteAppointment(uid: string, id: number = 0) {
        const action = `${CONTROLLER}.deleteAppointment`;
        const data = new FormData();
        data.set('uid', uid);
        data.set('id', `${id}`);
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async sendEmailNoteAction(appointment: Appointment) {

    }
}