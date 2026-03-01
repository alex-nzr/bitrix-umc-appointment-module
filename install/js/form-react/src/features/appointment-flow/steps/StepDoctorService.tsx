import {
    Stack,
    Typography,
    Autocomplete,
    TextField,
} from "@mui/material";
import React, {useMemo} from "react";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { useDirections } from "../hooks/useDirections";
import { useServices } from "../hooks/useServices";

export const StepDoctorService = () => {
    const {
        clinicUid,
        specialtyUid,
        doctorUid,
        serviceUIDs,
        mode,
        setDoctor,
        setServices,
    } = useAppointmentStore();

    const {doctors} = useDirections(clinicUid);
    const {services, serviceError} = useServices(clinicUid ?? '');

    const doctorsBySpecialty = useMemo(() => {
        return doctors.filter((d) =>
            Object.values(d.specialties)?.some((s) => s.uid === specialtyUid)
        );
    }, [doctors, specialtyUid]);

    const servicesBySpecialty = useMemo(() => {
        return clinicUid ? services.filter(
            (s) => Object.values(s.specialties)?.some((s) => s.uid === specialtyUid)
        ) : [];
    }, [clinicUid, specialtyUid]);

    const doctorsByService = useMemo(() => {
        if (serviceUIDs.length === 0) {
            return [];
        }
        return doctorsBySpecialty.filter((d) => {
            let res = true;
            serviceUIDs.forEach(sUid => {
                if(!d.services[sUid])
                {
                    res = false;
                }
            })
            return res;
        });
    }, [doctorsBySpecialty, serviceUIDs]);

    const servicesByDoctor = useMemo(() => {
        if (!doctorUid) return [];
        const doctor = doctors.find((d) => d.uid === doctorUid);
        if (doctor)
        {
            return servicesBySpecialty.filter((s) => doctor.services.hasOwnProperty(s.uid));
        }
        return [];
    }, [doctorUid, doctors]);

    if (serviceError)
    {
        return <Typography component="h1" variant="subtitle1" align="center">{serviceError}</Typography>;
    }

    return (
        <Stack spacing={3} p={3}>
            {/* DOCTOR FIRST */}
            {mode === "doctor-first" && (
                <>
                    <Autocomplete
                        options={doctorsBySpecialty}
                        getOptionLabel={(o) => o.fullName}
                        onChange={(_, v) => {
                            setDoctor(v?.uid ?? '');
                            setServices([]);
                        }}
                        renderInput={(params) => (
                            <TextField {...params} label="Врач" />
                        )}
                    />

                    <Autocomplete
                        disabled={!doctorUid}
                        options={servicesByDoctor}
                        multiple={true}
                        getOptionLabel={(o) => o.name}
                        onChange={(_, v) =>
                            setServices(v.map(s => s.uid) ?? [])
                        }
                        renderInput={(params) => (
                            <TextField {...params} label="Услуга" />
                        )}
                    />
                </>
            )}

            {/* SERVICE FIRST */}
            {mode === "service-first" && (
                <>
                    <Autocomplete
                        options={servicesBySpecialty}
                        multiple={true}
                        getOptionLabel={(o) => o.name}
                        onChange={(_, v) => {
                            setServices(v.map(s => s.uid) ?? [])
                            setDoctor('');
                        }}
                        renderInput={(params) => (
                            <TextField {...params} label="Услуга" />
                        )}
                    />

                    <Autocomplete
                        disabled={serviceUIDs.length <= 0}
                        options={doctorsByService}
                        getOptionLabel={(o) => o.fullName}
                        onChange={(_, v) =>
                            setDoctor(v?.uid ?? '')
                        }
                        renderInput={(params) => (
                            <TextField {...params} label="Врач" />
                        )}
                    />
                </>
            )}
        </Stack>
    );
};