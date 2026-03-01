import { Specialty } from "../specialty/model"

export interface Doctor{
    id?:        number
    uid:        string
    name:       string
    surname:    string
    middleName: string
    fullName:   string
    description: string
    photo:      string //base64
    clinicName: string
    clinicUid:  string
    specialtyName:  string //mainSpecialty
    specialtyUid:  string //mainSpecialty
    specialties: {
        [key: string]: Specialty
    },
    services: {
        [key: string]: {
            uid: string
            personalDuration?: string | number
        }
    }
    isActive:   boolean
    inSchedule: boolean
}