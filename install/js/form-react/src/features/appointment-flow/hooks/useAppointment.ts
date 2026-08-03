import {useCallback, useRef, useState} from 'react'
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";
import {Appointment} from "../../../entities/appointment/model";
import {useClinics} from "./useClinics";
import {useDoctors} from "./useDoctors";
import {useServices} from "./useServices";
import {Clinic} from "../../../entities/clinic/model";
import {Specialty} from "../../../entities/specialty/model";
import {Doctor} from "../../../entities/doctor/model";
import {useSettings} from "./useSettings";

export const useAppointment = () => {
    const {
        clinicUid,
        specialtyUid,
        doctorUid,
        serviceUIDs,
        slot,
        contact,
        bookingUid,
        setIsLoading,
    } = useAppointmentStore();
    const [error, setError] = useState<string | null>(null);
    const {servicesEnabled, emailNotificationEnabled} = useSettings();
    const { clinics } = useClinics();
    const { specialties, doctors } = useDoctors(clinicUid);
    const { services } = useServices(clinicUid);

    const appRef = useRef(false);

    const sendAppointment = useCallback(async (): Promise<boolean> => {
        const selectedClinic = clinics.find((c: Clinic) => c.uid === clinicUid) || null;
        const selectedSpec = specialties.find((s: Specialty) => s.uid === specialtyUid) || null;
        const selectedDoctor = doctors.find((d: Doctor) => d.uid === doctorUid) || null;
        const selectedServices = serviceUIDs.length ? services.filter(s =>  serviceUIDs.includes(s.uid)) : [];

        if (appRef.current
            || !bookingUid
            || !selectedClinic
            || !selectedSpec
            || !selectedDoctor
            || (servicesEnabled && !(selectedServices.length > 0))
            || !slot
        )
        {
            return false;
        }

        appRef.current = true;
        setError(null);
        setIsLoading(true);
        try
        {
            const appointment: Appointment = {
                uid: bookingUid,
                clinicUid: selectedClinic.uid,
                clinicName: selectedClinic.name,
                specialtyUid: selectedSpec.uid,
                specialtyName: selectedSpec.name,
                doctorUid: selectedDoctor.uid,
                doctorName: selectedDoctor.fullName || [
                    selectedDoctor.surname,
                    selectedDoctor.name,
                    selectedDoctor.middleName,
                ].filter(Boolean).join(' '),
                services: selectedServices,
                date: slot.date,
                timeBegin: slot.timeBegin,
                timeEnd: slot.timeEnd,
                duration: slot.duration,
                contact,
            };
            await appointmentApi.sendAppointment(appointment);
            if (emailNotificationEnabled && appointment.contact.email?.trim()) {
                try
                {
                    await appointmentApi.sendEmailNoteAction(appointment);
                }
                catch (e)
                {
                    console.error('Email notification failed', e);
                }
            }
            return true;
        }
        catch (e: any)
        {
            setError(Array.isArray(e) ? String(e[0]?.message) : String(e));
            return false;
        }
        finally
        {
            appRef.current = false;
            setIsLoading(false);
        }
    }, [
        bookingUid,
        clinicUid,
        clinics,
        contact,
        doctorUid,
        doctors,
        emailNotificationEnabled,
        serviceUIDs,
        services,
        servicesEnabled,
        setIsLoading,
        slot,
        specialtyUid,
        specialties,
    ]);

    return {
        sendAppointment,
        appointmentError: error ? window.BX.message('APPOINTMENT_SEND_ERROR') : null,
    };
}
