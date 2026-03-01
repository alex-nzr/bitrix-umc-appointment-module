import {appointmentApi} from "../../../shared/api/appointmentApi";
import {useEffect, useState} from "react";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";

export const useServices = (clinicUid: string | null) => {
    const { servicesCache, setServicesCache, setIsLoading } = useAppointmentStore();
    const [error, setError] = useState<string|null>(null)

    useEffect(() => {
        if (!clinicUid || servicesCache[clinicUid]) return;

        setIsLoading(true);

        appointmentApi.getServices(clinicUid)
            .then(data => setServicesCache(clinicUid, data))
            .catch((e) => setError(String(e)))
            .finally(() => setIsLoading(false));
    }, [clinicUid]);

    return {
        services: servicesCache[clinicUid ?? ''] ?? [],
        serviceError: error
    };
};