import {FC, useEffect} from "react";
import {useAppointmentStore} from "../../shared/store/appointmentStore";
import {useSettings} from "../appointment-flow/hooks/useSettings";

export const CustomButtonBinding: FC = () => {
    const { setIsOpen } = useAppointmentStore();
    const { useCustomButton, customButtonSelector } = useSettings();

    useEffect(() => {
        if (!useCustomButton || !customButtonSelector) {
            return;
        }

        const elements = Array.from(document.querySelectorAll<HTMLElement>(customButtonSelector));
        if (!elements.length) {
            return;
        }

        const openForm = () => setIsOpen(true);

        elements.forEach((element) => element.addEventListener('click', openForm));

        return () => {
            elements.forEach((element) => element.removeEventListener('click', openForm));
        };
    }, [customButtonSelector, setIsOpen, useCustomButton]);

    return null;
}
