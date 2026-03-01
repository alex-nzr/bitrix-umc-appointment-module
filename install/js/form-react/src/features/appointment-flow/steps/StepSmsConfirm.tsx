import { useEffect, useState } from "react";
import { Box, Button, TextField, Typography } from "@mui/material";
import { appointmentApi } from "../../../shared/api/appointmentApi";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";

export const StepSmsConfirm = () => {
    const { nextStep } = useAppointmentStore();
    const [code, setCode] = useState("");
    const [seconds, setSeconds] = useState(60);

    useEffect(() => {
        const interval = setInterval(() => {
            setSeconds(s => {
                if (s <= 1) {
                    clearInterval(interval);
                    return 0;
                }
                return s - 1;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    const verify = async () => {
        await appointmentApi.verifyConfirmCode(code);
        nextStep();
    };

    return (
        <Box p={3}>
            <Typography>
                Введите код из SMS ({seconds})
            </Typography>

            <TextField
                value={code}
                onChange={(e) => setCode(e.target.value)}
                fullWidth
                sx={{ mt: 2 }}
            />

            <Button variant="contained" sx={{ mt: 2 }} onClick={verify}>
                Подтвердить
            </Button>
        </Box>
    );
};