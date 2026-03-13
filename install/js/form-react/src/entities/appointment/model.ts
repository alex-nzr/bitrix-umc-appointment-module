import {Service} from "../service/model";

export enum EContactFields{
    firstName = "firstName",
    secondName = "secondName",
    lastName = "lastName",
    phone = "phone",
    birthday = "birthday",
    email = "email",
    address = "address",
    comment = "comment",
}

export enum EAdditionalFields{
    code = 'code',
}

export type ContactInfo = {
    id?: number
    uid?: string
} & {
    [K in
        | EContactFields.firstName
        | EContactFields.lastName
        | EContactFields.secondName
        | EContactFields.phone
        | EContactFields.birthday
    ]: string
} & {
    [K in
        | EContactFields.email
        | EContactFields.address
        | EContactFields.comment
    ]?: string
};

export interface Reserve {
    id?: number
    uid?: string
    clinicUid:  string,
    doctorUid: string
    date: string
    timeBegin: string
    duration: number
}

export interface Appointment {
    id?: number
    uid: string
    clinicUid:  string
    clinicName: string
    specialtyUid: string
    specialtyName: string
    doctorUid: string
    doctorName: string
    services: Service[]
    date: string
    timeBegin: string
    timeEnd: string
    duration: number
    contact: ContactInfo
}