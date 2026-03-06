import {Box, Button, Grid, Typography} from "@mui/material";
import {useAppointmentStore} from "../../../../shared/store/appointmentStore";
import React, {useEffect, useRef} from "react";
import dayjs, {Dayjs} from "dayjs";
import "dayjs/locale/ru";
import {Slot} from "../../../../entities/slot/model";
import {useBooking} from "../../hooks/useBooking";

interface SlotsListProps {
    date: Dayjs
    slots: Slot[]
}

export const SlotsList = ({date, slots}: SlotsListProps) => {
    dayjs.locale("ru");
    const selectedRef = useRef<HTMLButtonElement | null>(null);
    const {slot, setSlot, isLoading, step, bookingUid } = useAppointmentStore()
    const {cancelBooking} = useBooking();
    const selectedSlot = slot;

    useEffect(() => {
        selectedRef.current?.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }, [selectedSlot, step]);

    const handleSelectSlot = async (slot: Slot) => {
        if (bookingUid) {
            await cancelBooking();
        }
        setSlot(slot);
    };

    let text = null;
    if (!slots.length && isLoading)
    {
        text = 'Загрузка расписания...';
    }
    else if (slots.length && isLoading)
    {
        text = 'Бронирование слота...';
    }
    else if (!slots.length && !isLoading)
    {
        text = 'Нет доступных слотов';
    }

    if (text)
    {
        return (
            <Typography component="h6"
                        variant="subtitle1"
                        align="center"
                        width={'100%'}
            >
                { text }
            </Typography>
        )
    }

    return (
        <Box flex={1}>
            <Typography component="h6" align="center">
                Доступное время на {date.format("dd, D MMMM")}
            </Typography>
            <Box sx={{
                flex: 1,
                overflowY: "auto",
                maxHeight: 340,
                pr: 1
            }}>
                <Grid container spacing={1}>
                    {slots.map((slot) => {
                        const isSelected = selectedSlot?.timeBegin === slot.timeBegin
                        return (
                            <Grid size={3} key={slot.timeBegin + slot.doctorUid + slot.clinicUid}>
                                <Button
                                    ref={isSelected ? selectedRef : null}
                                    fullWidth
                                    variant={isSelected ? 'contained' : 'outlined'}
                                    size="small"
                                    onClick={() => handleSelectSlot(slot)}
                                >
                                    {slot.formattedTimeBegin}
                                </Button>
                            </Grid>
                        )
                    })}
                </Grid>
            </Box>
        </Box>
    )
}