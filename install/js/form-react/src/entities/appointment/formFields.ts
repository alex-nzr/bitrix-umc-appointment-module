import { ConfirmationType } from "../../shared/settings/widgetSettings";
import {EContactFields} from "./model";
import {PhoneMask} from "../../features/appointment-flow/steps/StepContactForm/PhoneMask";
import {DateMask} from "../../features/appointment-flow/steps/StepContactForm/DateMask";

export interface FormField<TName>{
    name: TName,
    label: string,
    required: boolean,
    multiline?: boolean,
    placeholder?: string,
    maskComponent?: any,
}

export const getFormFields = (
    confirmationType: ConfirmationType,
    phoneInputMask: string
): FormField<EContactFields>[] => {
    const formFields: FormField<EContactFields>[] = [
        {
            name: EContactFields.firstName,
            label: 'Ваше имя',
            required: true,
        },
        {
            name: EContactFields.secondName,
            label: 'Ваше Отчество',
            required: false,
        },
        {
            name: EContactFields.lastName,
            label: 'Ваша фамилия',
            required: true,
        },
        {
            name: EContactFields.phone,
            label: 'Телефон',
            required: true,
            placeholder: phoneInputMask,
            maskComponent: PhoneMask,
        },
        {
            name: EContactFields.birthday,
            label: 'Дата рождения',
            required: true,
            placeholder: "ДД.ММ.ГГГГ",
            maskComponent: DateMask,
        },
        {
            name: EContactFields.email,
            label: 'Email',
            required: false,
        },
        {
            name: EContactFields.comment,
            label: 'Комментарий',
            required: false,
            multiline: true
        },
    ];

    return formFields.map(field => {
        if (field.name === EContactFields.email && confirmationType === ConfirmationType.email) {
            return {...field, required: true};
        }
        return field;
    });
};
