import React, {useEffect, useState} from "react";
import dayjs from "dayjs";
import {Box, Stack, Typography} from "@mui/material";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {useSlots} from "../hooks/useSlots";
import { useAvailableDates } from "../hooks/useAvailableDates";
import { useSlotsByDates } from "../hooks/useSlotsByDates";
import {SlotsList} from "./StepDateTime/SlotsList";
import {Calendar} from "./StepDateTime/Calendar";
import {NavigationButtons} from "./NavigationButtons";
import {useBooking} from "../hooks/useBooking";
import {useSettings} from "../hooks/useSettings";

export const StepDateTime = () => {
    const { clinicUid, doctorUid, serviceUIDs, slot, setSlot, nextStep, isLoading } = useAppointmentStore();
    const [selectedDate, setSelectedDate] = useState(slot ? dayjs(slot.timeBegin) : dayjs());
    const {slots, slotsError} = useSlots(clinicUid ?? '', doctorUid ?? '', serviceUIDs);
    const {bookSlot, bookingError} = useBooking();
    const availableDates = useAvailableDates(slots);
    const slotsMap = useSlotsByDates(slots);
    const {servicesEnabled} = useSettings();

    useEffect(() => {
        if (slot && !dayjs(slot.timeBegin).isSame(selectedDate, 'day')) {
            setSlot(null)
        }
    }, [selectedDate])

    const handleNext = async () => {
        const success = await bookSlot();
        if (success) {
            nextStep();
        }
    };

    const showCalendar = Boolean(servicesEnabled ? doctorUid && (serviceUIDs.length > 0) : doctorUid);

    return (
        <Stack spacing={3}>
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

            <NavigationButtons nextDisabled={!slot || isLoading} nextHandler={handleNext}/>
        </Stack>
    );
}