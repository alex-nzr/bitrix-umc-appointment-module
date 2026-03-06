import {Box} from "@mui/material";
import React from "react";
import dayjs, {Dayjs} from "dayjs";
import {DateCalendar} from "@mui/x-date-pickers";
import {useFirstAvailableDate} from "../../hooks/useFirstAvailableDate";
import { useSettings } from "../../hooks/useSettings";
import { CustomDay } from "./CustomDay";

interface CalendarProps {
    selectedDate: Dayjs
    setSelectedDate: (date: Dayjs) => void
    availableDates: Set<string>
}

export const Calendar = ({selectedDate, setSelectedDate, availableDates}: CalendarProps) => {
    const settings = useSettings();
    useFirstAvailableDate(availableDates, selectedDate, setSelectedDate);

    return (
        <Box flex={1}>
            <DateCalendar
                value={selectedDate}
                onChange={(d) => d && setSelectedDate(d)}
                shouldDisableDate={(date) =>
                    !availableDates.has(date.format("YYYY-MM-DD"))
                }
                minDate={dayjs()}
                maxDate={dayjs().add((settings.schedulePeriodDays ?? 30) + 1, "day")}
                slots={{
                    day: CustomDay
                }}
                slotProps={{
                    day: {
                        availableDates
                    } as any
                }}
            />
        </Box>
    )
}