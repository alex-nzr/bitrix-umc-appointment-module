import {FlowMode, useAppointmentStore} from "../../shared/store/appointmentStore";
import { StepClinicSpecialty } from "./steps/StepClinicSpecialty";
import {
    Box,
    CircularProgress,
    Dialog,
    DialogContent,
    IconButton,
    Step,
    StepLabel,
    Stepper,
    Typography,
    useMediaQuery,
    useTheme
} from "@mui/material";
import React, {FC} from "react";
import CloseIcon from "@mui/icons-material/Close";
import {StepDoctorService} from "./steps/StepDoctorService";
import { StepDateTime } from "./steps/StepDateTime";

const getStepContent = (isOpen: boolean, step: number)=> {
    switch (step) {
        case 0:
            return isOpen ? <StepClinicSpecialty /> : '';
        case 1:
            return <StepDoctorService />;
        case 2:
            return <StepDateTime />;
        case 3:
            return <>contact form</>;
        case 4:
            return <>final-screen</>;
        default:
            return <>Unknown step</>;
    }
}

export const AppointmentForm: FC = () => {
    const { step, mode, isOpen, setIsOpen, isLoading } = useAppointmentStore();
    const theme = useTheme();
    const fullScreen = useMediaQuery(theme.breakpoints.down('sm'));
    const steps = [
        {id: 'mode',        label: "Филиал и направление"},
        {id: 'selection',   label: "Доктор, услуга, время"},
        {id: 'contact',     label: "Контакты"},
        {id: 'final',       label: "Результат"},
    ];

    return (
        <Dialog open={isOpen}
                onClose={()=> setIsOpen(false)}
                aria-labelledby={`appointment-form`}
                maxWidth={'md'}
                keepMounted={step !== 3}
                fullScreen={fullScreen}
        >
            <DialogContent>
                <Box sx={{p: { xs: 2, md: 2 } }}>
                    <Typography component="h1" variant="h4" align="center">Онлайн-запись</Typography>
                    <IconButton onClick={()=> setIsOpen(false)}
                                sx={{position: 'absolute', top: '10px', right: '10px'}}
                    >
                        <CloseIcon />
                    </IconButton>
                    <Stepper activeStep={step} sx={{ pt: 2, pb: 1 }}>
                        {steps.map((step) => (
                            step.id === 'final'
                                ? void(0)
                                :   <Step key={step.id}>
                                    <StepLabel>{step.label}</StepLabel>
                                </Step>
                        ))}
                    </Stepper>
                </Box>

                {getStepContent(isOpen, step)}

                {isLoading && <Box className={'appointment-loading-screen'}>
                    <CircularProgress/>
                </Box>}
            </DialogContent>
        </Dialog>
    );
};