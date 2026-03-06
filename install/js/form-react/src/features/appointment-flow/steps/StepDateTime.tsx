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

export const StepDateTime = () => {
    const { clinicUid, doctorUid, serviceUIDs, slot, setSlot, nextStep, isLoading } = useAppointmentStore();
    const [selectedDate, setSelectedDate] = useState(dayjs());
    const {slots, slotsError} = useSlots(clinicUid ?? '', doctorUid ?? '', serviceUIDs);
    const {bookSlot, bookingError} = useBooking();
    const availableDates = useAvailableDates(slots);
    const slotsMap = useSlotsByDates(slots);

    useEffect(() => {
        setSlot(null)
    }, [selectedDate])

    const handleNext = async () => {
        const success = await bookSlot();
        if (success) {
            nextStep();
        }
    };

    return (
        <Stack spacing={3}>
            {(slotsError || bookingError) && (
                <Typography component="h1" variant="subtitle1" align="center">{bookingError ?? slotsError}</Typography>
            )}

            {doctorUid && (serviceUIDs.length > 0) && (
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