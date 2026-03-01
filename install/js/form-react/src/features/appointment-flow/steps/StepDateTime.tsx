import React, {useMemo, useState} from "react";
import dayjs from "dayjs";
import {Box, Button, Stack, Typography} from "@mui/material";
import {DateCalendar} from "@mui/x-date-pickers";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useSlots} from "../hooks/useSlots";

export const StepDateTime = () => {
    const {
        clinicUid,
        doctorUid,
        serviceUIDs,
        setSlot,
        nextStep,
    } = useAppointmentStore();

    const [selectedDate, setSelectedDate] = useState(dayjs());
    const {slots, slotsError} = useSlots(clinicUid ?? '', doctorUid ?? '');

    const slotsForDate = useMemo(() => {
        return slots.filter(
            (s) =>
                s.date === selectedDate.format("YYYY-MM-DD") &&
                s.isAvailable
        );
    }, [slots, selectedDate]);

    if (slotsError)
    {
        return <Typography component="h1" variant="subtitle1" align="center">{slotsError}</Typography>;
    }

    return (
        <Stack spacing={3} p={3}>
            {/* Календарь */}
            {doctorUid && (serviceUIDs.length > 0) && (
                <>
                    <Box flex={1}>
                        <DateCalendar
                            sx={{ width: '100%' }}
                            value={selectedDate}
                            onChange={(d) => setSelectedDate(d!)}
                        />
                    </Box>

                    <Box flex={1}>
                        <Typography variant="h6">
                            Доступное время
                        </Typography>

                        <Box
                            display="flex"
                            flexWrap="wrap"
                            gap={1}
                            mt={2}
                        >
                            {slotsForDate.map((slot) => (
                                <Button
                                    key={slot.timeBegin}
                                    variant="outlined"
                                    onClick={() => {
                                        setSlot(slot);
                                        nextStep();
                                    }}
                                >
                                    {slot.formattedTimeBegin}
                                </Button>
                            ))}
                        </Box>
                    </Box>
                </>
            )}
        </Stack>
    );
}