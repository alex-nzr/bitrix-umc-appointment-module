//@flow
type ETextInputNames = | "name" | "middleName" | "surname" | "phone" | "email" | "birthday" | "comment";
type EIllegalAttributes = "data-required" | "aria-autocomplete";

interface ITextInputNames{
    name: string,
    middleName: string,
    surname: string,
    phone: string,
    email: string,
    birthday: string,
    comment: string
}

export const TextInputNames: ITextInputNames = {
    name: "name",
    middleName: "middleName",
    surname: "surname",
    phone: "phone",
    email: "email",
    birthday: "birthday",
    comment: "comment"
};

export interface ITextObject {
    type?: string,
    placeholder: string,
    id: string,
    maxlength: string,
    class: string,
    name: ETextInputNames,
    [key: EIllegalAttributes]: string,
    autocomplete?: string,
}