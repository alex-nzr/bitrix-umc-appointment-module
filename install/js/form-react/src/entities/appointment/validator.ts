import {ContactInfo} from "./model";
import {ConfirmationType} from "../../shared/settings/widgetSettings";

export const validateContact = (contact: ContactInfo, confirmationType: ConfirmationType) => {

    if (!contact.firstName || contact.firstName.length < 2) {
        return false;
    }
    if (!contact.lastName || contact.lastName.length < 2) {
        return false;
    }
    if (!contact.phone || contact.phone.length < 16) {
        return false;
    }

    if (!contact.birthday) {
        return false;
    }

    if (confirmationType === ConfirmationType.email && !contact.email) {
        return false;
    }

    return true;
};

export const normalizeConfirmCode = (value: string): string => {
    return value.replace(/\D/g, "");
};

export const validateConfirmCode = (value: string): boolean => {
    return normalizeConfirmCode(value).length >= 4;
};
