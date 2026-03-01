import {Service} from "../service/model";

export interface ContactInfo {
    id?: number
    uid?: string
    firstName:  string
    lastName:   string
    secondName: string
    phone:      string
    email?:     string
    address?:   string
}

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
    doctorName: number
    services: Service[]
    date: string
    timeBegin: string
    timeEnd: string
    duration: number
    contact: ContactInfo
    comment?:   string
}