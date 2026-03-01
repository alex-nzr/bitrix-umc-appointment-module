import {Specialty} from "../specialty/model";

export interface Service{
    id?:            number
    uid:            string
    name:           string
    duration:       number
    specialties: {
        [key: string]: Specialty
    },
    typeOfItem:     string
    artNumber:      string
    measureUnit:    string
    price:          number
}