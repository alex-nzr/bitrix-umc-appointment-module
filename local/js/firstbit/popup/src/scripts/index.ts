import {AppointmentPopup} from "./appointment/app"

declare global {
    interface Window {
        BX: any;
    }
}
window.BX.AppointmentPopup = AppointmentPopup;