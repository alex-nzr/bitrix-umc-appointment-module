import React, {useCallback, useEffect, useState} from "react";
import dayjs from "dayjs";
import {Box, Button, Stack, TextField, Typography} from "@mui/material";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useSlots} from "../hooks/useSlots";
import { useAvailableDates } from "../hooks/useAvailableDates";
import { useSlotsByDates } from "../hooks/useSlotsByDates";
import {SlotsList} from "./StepDateTime/SlotsList";
import {Calendar} from "./StepDateTime/Calendar";
import {NavigationButtons} from "./NavigationButtons";
import {useBooking} from "../hooks/useBooking";
import {useSettings} from "../hooks/useSettings";
import {appointmentApi, CaptchaChallenge} from "../../../shared/api/appointmentApi";

export const StepDateTime = () => {
    const { clinicUid, doctorUid, serviceUIDs, slot, setSlot, nextStep, isLoading } = useAppointmentStore();
    const [selectedDate, setSelectedDate] = useState(slot ? dayjs(slot.timeBegin) : dayjs());
    const {slots, slotsError} = useSlots(clinicUid ?? '', doctorUid ?? '', serviceUIDs);
    const {bookSlot, bookingError} = useBooking();
    const availableDates = useAvailableDates(slots);
    const slotsMap = useSlotsByDates(slots);
    const {servicesEnabled, captchaEnabled = true} = useSettings();
    const [captcha, setCaptcha] = useState<CaptchaChallenge | null>(null);
    const [captchaWord, setCaptchaWord] = useState('');
    const [captchaError, setCaptchaError] = useState<string | null>(null);

    const refreshCaptcha = useCallback(async () => {
        if (!captchaEnabled)
        {
            return;
        }
        try
        {
            setCaptchaError(null);
            setCaptchaWord('');
            setCaptcha(await appointmentApi.getCaptcha());
        }
        catch (error)
        {
            setCaptchaError(String(error));
        }
    }, [captchaEnabled]);

    useEffect(() => {
        refreshCaptcha();
    }, [refreshCaptcha]);

    useEffect(() => {
        if (slot && !dayjs(slot.timeBegin).isSame(selectedDate, 'day')) {
            setSlot(null)
        }
    }, [selectedDate, setSlot, slot])

    const handleNext = async () => {
        if (captchaEnabled && (captcha === null || captchaWord.trim() === ''))
        {
            setCaptchaError('Введите код с картинки');
            return;
        }

        const success = await bookSlot(captchaEnabled ? captcha?.captchaSid ?? '' : '', captchaEnabled ? captchaWord : '');
        if (success) {
            nextStep();
            return;
        }

        refreshCaptcha();
    };

    const showCalendar = Boolean(servicesEnabled ? doctorUid && (serviceUIDs.length > 0) : doctorUid);

    return (
        <Stack spacing={1}>
            {(slotsError || bookingError) && (
                <Typography component="h1" color={'red'} variant="subtitle1" align="center">{bookingError ?? slotsError}</Typography>
            )}

            {showCalendar && (
                <Box display="flex"
                     flexDirection={{ xs: "column", md: "row" }}
                     justifyContent={"space-between"}
                >
                    <Box width={{ xs: "100%", md: "50%" }}>
                        <Calendar selectedDate={selectedDate}
                                  setSelectedDate={setSelectedDate}
                                  availableDates={availableDates}
                        />
                    </Box>

                    <Box width={{ xs: "100%", md: "50%" }}>
                        <SlotsList
                            date={selectedDate}
                            slots={slotsMap.get(selectedDate.format("YYYY-MM-DD")) ?? []}
                        />
                    </Box>
                </Box>
            )}

            {captchaEnabled && <Stack
                spacing={0.25}
                alignItems="center"
            >
                <Typography>Введите код с картинки</Typography>
                {captcha && <Box component="img" src={captcha.imageUrl} alt="CAPTCHA" sx={{height: 40}} />}
                <TextField
                    value={captchaWord}
                    onChange={(event) => setCaptchaWord(event.target.value)}
                    inputProps={{maxLength: 10}}
                    size="small"
                />
                <Button variant="text" onClick={refreshCaptcha} disabled={isLoading}>Обновить картинку</Button>
                {captchaError && <Typography color="error">{captchaError}</Typography>}
            </Stack>}

            <NavigationButtons nextDisabled={!slot || (captchaEnabled && (!captcha || !captchaWord.trim())) || isLoading} nextHandler={handleNext}/>
        </Stack>
    );
}
