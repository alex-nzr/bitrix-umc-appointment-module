import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useEffect, useState} from "react";
import { Slot } from "../../../entities/slot/model";
import { appointmentApi } from "../../../shared/api/appointmentApi";

export const useSlots = (clinicUid: string | null, doctorUid: string | null) => {
    const { setIsLoading } = useAppointmentStore();
    const [slots, setSlots] = useState<Slot[]>([]);
    const [error, setError] = useState<string|null>(null)

    useEffect(() => {
        if (!clinicUid || !doctorUid) return;

        setIsLoading(true);

        appointmentApi.getSlots(clinicUid, doctorUid)
            .then((data: Slot[]) => setSlots(data))
            .catch(e => setError(String(e)))
            .finally(() => setIsLoading(false));
    }, [clinicUid, doctorUid]);

    return {slots, slotsError: error};
};