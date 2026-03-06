import {useRef} from 'react'
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";
import {Reserve} from "../../../entities/appointment/model";

export const useBooking = () => {
    const {
        clinicUid,
        doctorUid,
        serviceUIDs,
        slot,
        bookingUid,
        setIsLoading,
        setBookingUid,
        error, setError
    } = useAppointmentStore();

    const bookingRef = useRef(false);

    const bookSlot = async (): Promise<boolean> => {
        if (bookingRef.current || !clinicUid || !doctorUid || !serviceUIDs.length || !slot)
        {
            return false;
        }

        bookingRef.current = true;
        setError(null);
        setIsLoading(true);
        try
        {
            const reserve: Reserve = {
                clinicUid,
                doctorUid,
                date: slot.date,
                timeBegin: slot.timeBegin,
                duration: slot.duration,
            };
            const data = await appointmentApi.bookSlot(reserve);
            setBookingUid(data?.uid ?? null);
            return true;
        }
        catch (e: any)
        {
            setError(Array.isArray(e) ? String(e[0]?.message) : String(e));
            return false;
        }
        finally
        {
            bookingRef.current = false;
            setIsLoading(false);
        }
    };

    const cancelBooking = async () => {
        if (!bookingUid) {
            return;
        }

        setIsLoading(true);
        try {
            await appointmentApi.deleteAppointment(bookingUid);
        } catch(e: any) {
            console.error(e)
        } finally {
            setIsLoading(false);
        }

        setBookingUid(null);
    };

    return {
        bookSlot,
        cancelBooking,
        bookingError: error ? window.BX.message('BOOKING_SLOT_ERROR') : null,
    };
}