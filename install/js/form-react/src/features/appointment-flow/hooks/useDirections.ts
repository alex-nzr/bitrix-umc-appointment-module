import { useEffect, useState } from 'react'
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {appointmentApi} from "../../../shared/api/appointmentApi";
import {Specialty} from "../../../entities/specialty/model";
import { Doctor } from '../../../entities/doctor/model';

export const useDirections = (clinicUid: string|null = null) => {
    const { setIsLoading, doctorsCache, setDoctorsCache } = useAppointmentStore();
    const [specialties, setSpecialties] = useState<Specialty[]>([])
    const [error, setError] = useState<string|null>(null)

    useEffect(() => {
        if (!clinicUid) {
            return;
        }

        setSpecialties([]);

        const setSpecialtiesByClinic = (data: Doctor[]) => {
            const specMap: Record<string, Specialty> = {};
            data.length && data.forEach((doctor) => {
                if(!doctor.clinicUid || doctor.clinicUid === clinicUid)
                {
                    for (let specUid in doctor.specialties)
                    {
                        specMap[specUid] = doctor.specialties[specUid];
                    }
                }
            });

            setSpecialties(Object.values(specMap))
        }

        if (doctorsCache.length) {
            setSpecialtiesByClinic(doctorsCache);
            return;
        }

        setIsLoading(true)
        appointmentApi
            .getDoctors()
            .then(data => {
                setDoctorsCache(data);
                setSpecialtiesByClinic(data);
            })
            .catch((e) => setError(String(e)))
            .finally(() => setIsLoading(false))
    }, [clinicUid])

    return { specialties, doctors: doctorsCache, directionsError: error }
}