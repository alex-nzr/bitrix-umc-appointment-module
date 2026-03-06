import {
    Stack,
    Typography,
    Autocomplete,
    TextField
} from "@mui/material";
import React, {useMemo} from "react";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { useDoctors } from "../hooks/useDoctors";
import { useServices } from "../hooks/useServices";
import {NavigationButtons} from "./NavigationButtons";

export const StepDoctorService = () => {
    const {
        clinicUid,
        specialtyUid,
        doctorUid,
        serviceUIDs,
        mode,
        setDoctor,
        setServices
    } = useAppointmentStore();

    const {doctors} = useDoctors(clinicUid);
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
    }, [clinicUid, specialtyUid, services]);

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
            return services.filter((s) => doctor.services.hasOwnProperty(s.uid));
        }
        return [];
    }, [doctorUid, doctors, services]);

    return (
        <Stack spacing={3}>
            {
                serviceError && <Typography component="h1" variant="subtitle1" align="center">{serviceError}</Typography>
            }
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
                        value={doctorUid ? doctors.find(d =>  d.uid === doctorUid) :  null}
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
                        value={serviceUIDs.length ? services.filter(s =>  serviceUIDs.includes(s.uid)) : undefined}
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
                        value={serviceUIDs.length ? services.filter(s =>  serviceUIDs.includes(s.uid)) : undefined}
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
                        value={doctorUid ? doctors.find(d =>  d.uid === doctorUid) :  null}
                    />
                </>
            )}

            <NavigationButtons nextDisabled={!doctorUid || serviceUIDs.length <= 0}/>
        </Stack>
    );
};