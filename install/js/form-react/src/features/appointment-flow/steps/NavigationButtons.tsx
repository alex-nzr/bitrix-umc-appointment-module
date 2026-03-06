import {Box, Button} from "@mui/material";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";

interface NavigationButtonsProps{
    nextHandler?: () => void
    backHandler?: () => void
    nextText?: string
    backText?: string
    backDisabled?: boolean
    nextDisabled: boolean
}

export const NavigationButtons = ({
    backHandler, nextHandler, nextText, backText, nextDisabled, backDisabled
}: NavigationButtonsProps) => {
    const {
        nextStep,
        prevStep,
    } = useAppointmentStore();
    return (
        <Box display="flex" gap={2} justifyContent="center">
            <Button
                variant="outlined"
                disabled={backDisabled ?? false}
                onClick={() => backHandler ? backHandler() : prevStep()}
            >
                {backText ?? 'Назад'}
            </Button>

            <Button
                variant="contained"
                disabled={nextDisabled}
                onClick={() => nextHandler ? nextHandler() : nextStep()}
            >
                {nextText ?? 'Далее'}
            </Button>
        </Box>
    )
}