import React from "react";
import {
    Box,
    Button,
    Stack,
    TextField,
    Autocomplete, Typography,
} from "@mui/material";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { Clinic } from "../../../entities/clinic/model";
import { Specialty } from "../../../entities/specialty/model";
import { useClinics } from "../hooks/useClinics";
import { useDirections } from "../hooks/useDirections";

export const StepClinicSpecialty = () => {
    const {
        isOpen,
        clinicUid,
        specialtyUid,
        setClinic,
        setSpecialty,
        setMode,
    } = useAppointmentStore();

    const {clinics, clinicsError} = useClinics();
    const { specialties, directionsError } = useDirections(clinicUid);

    if(!isOpen)
    {
        return null;
    }

    const selectedClinic = clinics.find((c: Clinic) => c.uid === clinicUid) || null;
    const selectedSpec = specialties?.find((s: Specialty) => s.uid === specialtyUid) || null;

    if (clinicsError || directionsError)
    {
        return <Typography component="h1" variant="subtitle1" align="center">{clinicsError}<br/>{directionsError}</Typography>;
    }

    const isReady = clinicUid && specialtyUid;

    return (
        <Stack spacing={3}>
            <Autocomplete
                options={clinics}
                getOptionLabel={(option) => option.name}
                value={selectedClinic}
                onChange={(_, value) => setClinic(value?.uid ?? '')}
                renderInput={(params) => (
                    <TextField {...params} label="Клиника" />
                )}
            />

            <Autocomplete
                options={specialties}
                getOptionLabel={(option) => option.name}
                value={selectedSpec}
                disabled={!clinicUid}
                onChange={(_, value) => setSpecialty(value?.uid ?? '')}
                renderInput={(params) => (
                    <TextField {...params} label="Специализация" />
                )}
            />

            <Box display="flex" gap={2} justifyContent="center">
                <Button
                    variant="contained"
                    disabled={!isReady}
                    onClick={() => setMode("doctor-first")}
                >
                    Выбор врача
                </Button>

                <Button
                    variant="outlined"
                    disabled={!isReady}
                    onClick={() => setMode("service-first")}
                >
                    Выбор услуги
                </Button>
            </Box>
        </Stack>
    );
};