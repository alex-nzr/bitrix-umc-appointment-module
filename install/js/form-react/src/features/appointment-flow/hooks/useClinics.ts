import {useCallback, useEffect, useState} from 'react'
import {Clinic} from "../../../entities/clinic/model";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";
import {useSettings} from "./useSettings";

export const useClinics = () => {
    const { isOpen, setIsLoading, clinicUid, setClinic, clinicsCache, setClinicsCache} = useAppointmentStore();
    const [error, setError] = useState<string | null>(null);
    const {defaultClinicUid} = useSettings();

    const setDefaultClinic = useCallback((data: Clinic[]) => {
        const defaultClinic = defaultClinicUid
            ? data.find((c: Clinic) => c.uid === defaultClinicUid)
            : data.find((c: Clinic) => c.isDefault)

        if (isOpen && defaultClinic  && !clinicUid)
        {
            setClinic(defaultClinic.uid)
        }
    }, [clinicUid, defaultClinicUid, isOpen, setClinic]);

    useEffect(() => {
        if (!isOpen)
        {
            return;
        }

        if (clinicsCache.length) {
            setDefaultClinic(clinicsCache)
            return;
        }

        setError(null);
        setIsLoading(true)
        appointmentApi
            .getClinics()
            .then(data => {
                setIsLoading(false);
                setClinicsCache(data);
                setDefaultClinic(data)
            })
            .catch((e) => {
                setError(Array.isArray(e) ? String(e[0]?.message) : String(e));
                setIsLoading(false);
            })
    }, [clinicsCache, isOpen, setClinicsCache, setDefaultClinic, setIsLoading])

    return { clinics: clinicsCache, clinicsError: error ? window.BX.message('CLINICS_ERROR') : null}
}
