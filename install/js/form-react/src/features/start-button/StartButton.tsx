import Button from '@mui/material/Button'
import {FC} from "react";
import { useAppointmentStore } from '../../shared/store/appointmentStore';

export const StartButton:FC = () => {
    const { isOpen, setIsOpen } = useAppointmentStore();

    return (
        <div id={`appointment-button-wrapper`}
             className={`${!isOpen ? 'pulse' : ''}`}
        >
            <Button
                onClick={() => setIsOpen(!isOpen)}
                loadingPosition="center"
                variant={`${isOpen ? 'outlined' : 'contained'}`}
            >
                Запись на приём
            </Button>
        </div>
    );
}