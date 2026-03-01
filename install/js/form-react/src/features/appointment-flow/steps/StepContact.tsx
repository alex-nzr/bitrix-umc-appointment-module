import { useForm } from "react-hook-form";
import { Box, Button, TextField } from "@mui/material";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { appointmentApi } from "../../../shared/api/appointmentApi";

export const StepContact = () => {
    const { bookingUid, nextStep, setIsLoading } = useAppointmentStore();

    const { register, handleSubmit } = useForm();

    const onSubmit = async (data: any) => {
        setIsLoading(true);

        await appointmentApi.sendConfirmCode(data.phone);

        setIsLoading(false);
        nextStep();
    };

    return (
        <Box p={3} component="form" onSubmit={handleSubmit(onSubmit)}>
            <TextField fullWidth label="ФИО" {...register("fullName")} />
            <TextField fullWidth label="Телефон" {...register("phone")} sx={{ mt: 2 }} />
            <TextField fullWidth label="Email" {...register("email")} sx={{ mt: 2 }} />

            <Button type="submit" variant="contained" sx={{ mt: 3 }}>
                Получить код
            </Button>
        </Box>
    );
};