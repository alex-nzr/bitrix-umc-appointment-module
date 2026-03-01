import { useEffect, useState } from 'react'
import {Clinic} from "../../../entities/clinic/model";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";

export const useClinics = () => {
    const { isOpen, setIsLoading, clinicsCache, setClinicsCache } = useAppointmentStore();

    const [error, setError] = useState<string|null>(null)

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

        setIsLoading(true)
        appointmentApi
            .getClinics()
            .then(data => {
                setClinicsCache(data);
                setDefaultClinic(data)
            })
            .catch((e) => setError(String(e)))
            .finally(() => setIsLoading(false))
    }, [isOpen])

    return { clinics: clinicsCache, clinicsError: error }
}