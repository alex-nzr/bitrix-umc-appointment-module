import {appointmentApi} from "../../../shared/api/appointmentApi";
import {useEffect, useState} from "react";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";

export const useServices = (clinicUid: string | null) => {
    const { servicesCache, setServicesCache, setIsLoading } = useAppointmentStore();
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!clinicUid || servicesCache[clinicUid]) return;

        setError(null);
        setIsLoading(true);
        appointmentApi.getServices(clinicUid)
            .then(data => setServicesCache(clinicUid, data))
            .catch((e) => setError(Array.isArray(e) ? String(e[0]?.message) : String(e)))
            .finally(() => setIsLoading(false));
    }, [clinicUid, servicesCache, setIsLoading, setServicesCache]);

    return {
        services: servicesCache[clinicUid ?? ''] ?? [],
        serviceError: error ? window.BX.message('SERVICES_ERROR') : null
    };
};
