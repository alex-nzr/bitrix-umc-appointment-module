import { useEffect } from 'react'
import {Clinic} from "../../../entities/clinic/model";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";

export const useClinics = () => {
    const { isOpen, setIsLoading, clinicsCache, setClinicsCache, error, setError} = useAppointmentStore();
    const setClinic = useAppointmentStore((s) => s.setClinic)

    const setDefaultClinic = (data: Clinic[]) => {
        const defaultClinic = data.find((c: Clinic) => c.isDefault)
        if (defaultClinic) {
            setClinic(defaultClinic.uid)
        }
    }

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
                setClinicsCache(data);
                setDefaultClinic(data)
            })
            .catch((e) => setError(Array.isArray(e) ? String(e[0]?.message) : String(e)))
            .finally(() => setIsLoading(false))
    }, [isOpen])

    return { clinics: clinicsCache, clinicsError: error ? window.BX.message('CLINICS_ERROR') : null}
}