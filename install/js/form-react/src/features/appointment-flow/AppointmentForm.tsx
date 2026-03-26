import {useAppointmentStore} from "../../shared/store/appointmentStore";
import {StepClinicSpecialty} from "./steps/StepClinicSpecialty";
import {
    Avatar,
    Backdrop,
    Box,
    CircularProgress,
    Dialog,
    DialogContent,
    Fade,
    IconButton,
    MobileStepper,
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
import {StepDateTime} from "./steps/StepDateTime";
import {StepContactForm} from "./steps/StepContactForm";
import {useSettings} from "./hooks/useSettings";
import {StepConfirmation} from "./steps/StepConfirmation";
import {ConfirmationType} from "../../shared/settings/widgetSettings";
import {FinalScreen} from "./steps/FinalScreen";
import {useBooking} from "./hooks/useBooking";

export const AppointmentForm: FC = () => {
    const { step, isOpen, setIsOpen, isLoading, bookingUid, reset } = useAppointmentStore();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('sm'));
    const {confirmationType, logoImageSrc} = useSettings();
    const {cancelBooking} = useBooking();

    const steps = [
        {
            id: 'ClinicSpecialty',
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
            id: 'ContactForm',
            label: "Контакты",
            component: StepContactForm,
        },

        ...(confirmationType !== ConfirmationType.none
            ? [{
                id: 'Confirmation',
                label: "Подтверждение",
                component: StepConfirmation
            }]
            : []),

        {
            id: 'Final',
            label: "Результат",
            component: FinalScreen
        },
    ];

    const StepComponent = steps[step]?.component;
    const finalStepIndex = steps.length - 1;

    const handleClose = async () => {
        if (bookingUid && step < finalStepIndex) {
            await cancelBooking();
        }

        reset();
        setIsOpen(false);
    };

    return (
        <Dialog open={isOpen}
                onClose={handleClose}
                aria-labelledby={`appointment-form`}
                keepMounted={steps[step]?.id !== 'Final'}
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
                    {logoImageSrc && (
                        <Box sx={{display: 'flex', justifyContent: 'center', mb: 2}}>
                            <Avatar
                                src={logoImageSrc}
                                alt="Logo"
                                variant="rounded"
                                sx={{
                                    width: 'auto',
                                    height: { xs: 56, md: 72 },
                                    maxWidth: '100%',
                                    '& img': {
                                        objectFit: 'contain'
                                    }
                                }}
                            />
                        </Box>
                    )}
                    <Typography component="h1" variant="h4" align="center">Онлайн-запись</Typography>
                    <IconButton onClick={handleClose}
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
