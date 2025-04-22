import {ITimeTableItem} from "./models";


export enum ETextFields{
    name = "name",
    middleName = "middleName",
    surname = "surname",
    phone = "phone",
    email = "email",
    comment = "comment",
}
export type ITextFields = {
    [key in ETextFields]: string;
};

export interface ISelectedParams{
    dateTime:   ITimeTableItem,
    employee:   IEmployee,
    clinic:     IClinic,
    services:    IService[],
    specialty:  ISpecialty,
    textFields: ITextFields
}

export interface ISelectionSetterParams{
    dateTime?:   ITimeTableItem,
    employee?:   IEmployee,
    clinic?:     IClinic,
    services?:    IService[],
    specialty?:  ISpecialty,
    textFields?: ITextFields
}