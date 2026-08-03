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

const buildDateTime = (date: string, value: string) => {
    if (!value) {
        return '';
    }

    if (value.includes('T')) {
        return value;
    }

    return `${date}T${value.length === 5 ? `${value}:00` : value}`;
}

const buildAppointmentPayload = (appointment: Appointment) => {
    const primaryService = appointment.services[0] ?? null;

    return {
        bookingUid: appointment.uid,
        clinicUid: appointment.clinicUid,
        clinicName: appointment.clinicName,
        specialtyUid: appointment.specialtyUid,
        specialty: appointment.specialtyName,
        doctorName: appointment.doctorName,
        employeeUid: appointment.doctorUid,
        serviceUid: primaryService?.uid ?? '',
        serviceName: primaryService?.name ?? '',
        services: appointment.services.map((service) => ({
            uid: service.uid,
            name: service.name,
        })),
        timeBegin: buildDateTime(appointment.date, appointment.timeBegin),
        timeEnd: buildDateTime(appointment.date, appointment.timeEnd),
        serviceDuration: appointment.duration,
        surname: appointment.contact.lastName,
        name: appointment.contact.firstName,
        middleName: appointment.contact.secondName ?? '',
        phone: appointment.contact.phone,
        email: appointment.contact.email ?? '',
        birthday: appointment.contact.birthday || '',
        address: appointment.contact.address ?? '',
        comment: appointment.contact.comment ?? '',
    };
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

    async getSlots(clinicUid: string, doctorUid: string, serviceUIDs: string[]): Promise<Slot[]> {
        const action = `${CONTROLLER}.getSchedule`;
        const data = new FormData();
        data.set('clinicUid', clinicUid);
        data.set('employeeUid', doctorUid);
        serviceUIDs.length && serviceUIDs.forEach(uid => {
            data.append('serviceUIDs[]', uid);
        });
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async bookSlot(reserve: Reserve): Promise<Reserve | null> {
        const action = `${CONTROLLER}.bookSlot`;
        const data = new FormData();
        data.set('clinicUid', reserve.clinicUid);
        data.set('employeeUid', reserve.doctorUid);
        data.set('dateTimeBegin', reserve.timeBegin);
        data.set('serviceDuration', `${reserve.duration}`);
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    },

    async sendAppointment(appointment: Appointment): Promise<any> {
        const action = `${CONTROLLER}.sendAppointment`;
        const payload = buildAppointmentPayload(appointment);
        const data = new FormData();
        data.set('jsonData', JSON.stringify(payload));
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
        const action = `${CONTROLLER}.verifyConfirmCode`;
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
        const action = `${CONTROLLER}.cancelOwnAppointment`;
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

    async sendEmailNoteAction(appointment: Appointment): Promise<any> {
        const action = `${CONTROLLER}.sendEmailNote`;
        const data = new FormData();
        data.set('jsonData', JSON.stringify(buildAppointmentPayload(appointment)));
        const res = await fetch(`${API_URL}?sessid=${sessid}&action=${action}`, {
            method: 'POST',
            body: data,
        });
        // @ts-ignore
        return await prepareResponse(res);
    }
}
