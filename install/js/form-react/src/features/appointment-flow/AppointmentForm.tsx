import {useAppointmentStore} from "../../shared/store/appointmentStore";
import { StepClinicSpecialty } from "./steps/StepClinicSpecialty";
import {
    Backdrop,
    Box,
    CircularProgress,
    Dialog,
    DialogContent, Fade,
    IconButton, MobileStepper,
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
import {StepContactForm} from "./steps/StepContactForm";

export const AppointmentForm: FC = () => {
    const { step, isOpen, setIsOpen, isLoading } = useAppointmentStore();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('sm'));
    const steps = [
        {
            id: 'clinicSpecialty',
            label: "Филиал и направление",
            component: StepClinicSpecialty,
        },
        {
            id: 'DoctorService',
            label: "Доктор, услуга",
            component: StepDoctorService,
        },
        {
            id: 'DateTime',
            label: "Дата, время",
            component: StepDateTime,
        },
        {
            id: 'contact',
            label: "Контакты",
            component: StepContactForm,
        },
        {
            id: 'final',
            label: "Результат",
            component: () => <>final-screen</>
        },
    ];

    const StepComponent = steps[step]?.component;

    return (
        <Dialog open={isOpen}
                onClose={()=> setIsOpen(false)}
                aria-labelledby={`appointment-form`}
                keepMounted={step !== 4}
                fullScreen={isMobile}
                slotProps={{
                    paper: {
                        sx: {
                            width: "100%",
                            maxWidth: 900
                        }
                    }
                }}
        >
            <DialogContent>
                <Box sx={{p: { xs: 2, md: 2 } }}>
                    <Typography component="h1" variant="h4" align="center">Онлайн-запись</Typography>
                    <IconButton onClick={()=> setIsOpen(false)}
                                sx={{position: 'absolute', top: '10px', right: '10px'}}
                                aria-label={window.BX.message('FORM_BTN_CLOSE')}
                    >
                        <CloseIcon />
                    </IconButton>

                    {isMobile
                        ? (
                            <Box sx={{ textAlign: "center", mb: 1 }}>
                                <Typography align="center">
                                    {steps[step]?.label}
                                </Typography>

                                <MobileStepper
                                    variant="progress"
                                    steps={steps.length}
                                    position="static"
                                    activeStep={step}
                                    sx={{ mt: 1, mb: 1, justifyContent: "center" }}
                                    nextButton={null}
                                    backButton={null}
                                />
                            </Box>
                        )
                        : (
                            <Stepper activeStep={step} sx={{ pt: 2, pb: 1 }}>
                                {steps.map((step) => (
                                    <Step key={step.id}>
                                        <StepLabel>{step.label}</StepLabel>
                                    </Step>
                                ))}
                            </Stepper>
                        )
                    }
                </Box>

                <Fade key={step} in timeout={300}>
                    <Box>
                        <StepComponent />
                    </Box>
                </Fade>

                <Backdrop open={isLoading} sx={{ zIndex: 2000 }}>
                    <CircularProgress color="inherit" />
                </Backdrop>
            </DialogContent>
        </Dialog>
    );
};