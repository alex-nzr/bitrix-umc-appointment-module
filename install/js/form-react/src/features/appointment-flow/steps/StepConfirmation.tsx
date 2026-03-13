import React, {useEffect, useState} from "react";
import {Box, Button, Typography} from "@mui/material";
import {appointmentApi} from "../../../shared/api/appointmentApi";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useSettings} from "../hooks/useSettings";
import {ConfirmationType} from "../../../shared/settings/widgetSettings";
import {TextInput} from "./StepContactForm/TextInput";
import {ConfirmCodeMask} from "./StepContactForm/ConfirmCodeMask";
import {EAdditionalFields} from "../../../entities/appointment/model";
import {normalizeConfirmCode} from "../../../entities/appointment/validator";
import {useAppointment} from "../hooks/useAppointment";
import ErrorIcon from "@mui/icons-material/Error";

export const StepConfirmation = () => {
    const { code, setCode, nextStep, goToStep, contact, isLoading, setIsLoading } = useAppointmentStore();
    const {sendAppointment, appointmentError} = useAppointment();
    const [seconds, setSeconds] = useState(60);
    const [confirmed, setConfirmed] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const {confirmationType} = useSettings();

    useEffect(() => {
        send();
    }, []);

    useEffect(() => {
        if (!confirmed)
        {
            return;
        }
        sendAppointment()
            .then(isSuccess => isSuccess ? nextStep() : void(0))
    }, [confirmed]);

    const initTimer = () => {
        setSeconds(60);
        const interval = setInterval(() => {
            setSeconds((s) => {
                if (s <= 1) {
                    clearInterval(interval);
                    setCode('');
                    return 0;
                }
                return s - 1;
            });
        }, 1000);
    }

    const send = () => {
        setIsLoading(true);
        appointmentApi.sendConfirmCode(contact.phone, contact.email)
            .then(() => initTimer())
            .catch((e) => setError(Array.isArray(e) ? String(e[0]?.message) : String(e)))
            .finally(() => setIsLoading(false))
    };

    const verify = () => {
        if (!code)
        {
            return;
        }
        setIsLoading(true);
        appointmentApi.verifyConfirmCode(code)
            .then(() => {
                setConfirmed(true);
            })
            .catch((e) => setError(Array.isArray(e) ? String(e[0]?.message) : String(e)))
            .finally(() => setIsLoading(false))
    };

    if (appointmentError)
    {
        return (
            <>
                <Typography>
                    <ErrorIcon sx={{mr:2, width: '50px', height: '50px'}} color={"error"}/>
                    <span>
                        Создание записи не удалось. Возможно время уже занято.<br/>
                        Попробуйте повторить запись, выбрав другое время.
                    </span>
                </Typography>
                <Box display="flex" gap={2} justifyContent="center">
                    <Button
                        variant="outlined"
                        disabled={false}
                        onClick={() => goToStep(2)}
                    >
                        {'Назад'}
                    </Button>
                </Box>
            </>
        );
    }

    return (
        <Box p={3}>
            <Typography>
                Код подтверждения отправлен на {confirmationType === ConfirmationType.phone ? contact.phone : contact.email}.
                Введите его ниже.
            </Typography>

            <TextInput
                name={EAdditionalFields.code}
                label={'Код подтверждения'}
                required={true}
                placeholder={'_ - _ - _ - _'}
                maskComponent={ConfirmCodeMask}
                value={code ?? ''}
                setValue={(value) => setCode(normalizeConfirmCode(String(value)))}
            />

            {error && (
                <Typography color="error" sx={{ mt: 1 }}>
                    {error}
                </Typography>
            )}

            {seconds <= 0 && (
                <Button
                    variant="contained"
                    sx={{ mt: 2 }}
                    onClick={send}
                    disabled={isLoading}
                >
                    Отправить повторно
                </Button>
            )}

            {seconds > 0 && (
                <>
                    <Button
                        variant="contained"
                        sx={{ mt: 2 }}
                        onClick={verify}
                        disabled={!code || isLoading}
                    >
                        Подтвердить
                    </Button>
                    <Typography variant="body2" sx={{ mt: 1 }}>
                        Отправить код повторно через {seconds} сек
                    </Typography>
                </>
            )}
        </Box>
    );
};