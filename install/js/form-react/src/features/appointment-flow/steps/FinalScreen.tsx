import React, {FC} from 'react';
import {Box, DialogContent} from "@mui/material";
import VerifiedIcon from '@mui/icons-material/Verified';
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {Doctor} from "../../../entities/doctor/model";
import {useDoctors} from "../hooks/useDoctors";

export const FinalScreen:FC = () => {
    const {clinicUid, doctorUid, slot} = useAppointmentStore();
    const { doctors } = useDoctors(clinicUid);
    const selectedDoctor = doctors.find((d: Doctor) => d.uid === doctorUid) || null;
    return (
        <DialogContent>
            <Box sx={{display: 'flex',alignItems:'center',justifyContent: 'center'}}>
                <VerifiedIcon sx={{mr:2, width: '50px', height: '50px'}} color={"success"}/>
                <span>
                    Вы успешно записаны на приём.<br/>
                    Врач - <b>{selectedDoctor?.fullName}</b><br/>
                    Дата <b>{slot?.formattedDate}</b><br/>
                    Время <b>{slot?.formattedTimeBegin}</b>
                </span>
            </Box>
        </DialogContent>
    );
};