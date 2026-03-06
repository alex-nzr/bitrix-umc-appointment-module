import React, {useEffect} from "react";
import {
    Stack,
    TextField,
    Autocomplete, Typography,
} from "@mui/material";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { Clinic } from "../../../entities/clinic/model";
import { Specialty } from "../../../entities/specialty/model";
import { useClinics } from "../hooks/useClinics";
import { useDoctors } from "../hooks/useDoctors";
import {NavigationButtons} from "./NavigationButtons";
import {useSettings} from "../hooks/useSettings";

export const StepClinicSpecialty = () => {
    const {
        isOpen,
        clinicUid,
        specialtyUid,
        setClinic,
        setSpecialty,
        setMode,
        goToStep
    } = useAppointmentStore();

    const {clinics, clinicsError} = useClinics();
    const { specialties, useDoctorsError } = useDoctors(clinicUid);
    const {defaultClinicUid} = useSettings();

    useEffect(() => {
        if (defaultClinicUid && !clinicUid) {
            setClinic(defaultClinicUid);
        }
    }, [defaultClinicUid, clinicUid, setClinic]);

    if(!isOpen)
    {
        return null;
    }

    const selectedClinic = clinics.find((c: Clinic) => c.uid === clinicUid) || null;
    const selectedSpec = specialties?.find((s: Specialty) => s.uid === specialtyUid) || null;

    return (
        <Stack spacing={3}>
            {(clinicsError || useDoctorsError) && (
                <Typography variant="subtitle1" align="center">
                    {clinicsError}
                    {clinicsError && useDoctorsError && <br />}
                    {useDoctorsError}
                </Typography>
            )}
            <Autocomplete
                options={clinics ?? []}
                getOptionLabel={(option) => option.name}
                value={selectedClinic}
                isOptionEqualToValue={(a, b) => a.uid === b.uid}
                onChange={(_, value) => setClinic(value?.uid ?? '')}
                renderInput={(params) => (
                    <TextField {...params} label="Клиника" />
                )}
            />

            <Autocomplete
                options={specialties ?? []}
                getOptionLabel={(option) => option.name}
                isOptionEqualToValue={(a, b) => a.uid === b.uid}
                value={selectedSpec}
                disabled={!clinicUid}
                onChange={(_, value) => setSpecialty(value?.uid ?? '')}
                renderInput={(params) => (
                    <TextField {...params} label="Специализация" />
                )}
            />

            <NavigationButtons
                backHandler={() => {
                    setMode("doctor-first");
                    goToStep(1);
                }}
                nextHandler={() => {
                    setMode("service-first");
                    goToStep(1);
                }}
                backText={'Выбрать врача'}
                nextText={'Выбрать услугу'}
                nextDisabled={!(clinicUid && specialtyUid)}
                backDisabled={!(clinicUid && specialtyUid)}
            />
        </Stack>
    );
};