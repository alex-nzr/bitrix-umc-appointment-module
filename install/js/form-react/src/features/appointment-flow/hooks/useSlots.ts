import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useEffect, useState} from "react";
import { Slot } from "../../../entities/slot/model";
import { appointmentApi } from "../../../shared/api/appointmentApi";

export const useSlots = (clinicUid: string | null, doctorUid: string | null, serviceUIDs: string[]) => {
    const { setIsLoading, slotsCache, setSlotsCache } = useAppointmentStore();
    const [error, setError] = useState<string | null>(null);
    const cacheKey = `${clinicUid}_${doctorUid}_${[...serviceUIDs].sort().join(',')}`;
    const slots = slotsCache[cacheKey] ?? []
    useEffect(() => {
        if (!clinicUid || !doctorUid) return;

        if (slotsCache[cacheKey]) return

        setError(null);
        setIsLoading(true);
        appointmentApi.getSlots(clinicUid, doctorUid, serviceUIDs)
            .then((data: Slot[]) => setSlotsCache(cacheKey, data))
            .catch((e) => setError(Array.isArray(e) ? String(e[0]?.message) : String(e)))
            .finally(() => setIsLoading(false));
    }, [cacheKey]);

    return {slots, slotsError: error ? window.BX.message('SCHEDULE_ERROR') : null};
};