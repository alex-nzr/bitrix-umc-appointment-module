import { useEffect } from "react";
import dayjs, { Dayjs } from "dayjs";

export const useFirstAvailableDate = (
    availableDates: Set<string>,
    selectedDate: Dayjs,
    setSelectedDate: (d: Dayjs) => void
) => {

    useEffect(() => {
        if (availableDates.size === 0) return;

        const selectedDateKey = selectedDate.format("YYYY-MM-DD");

        if (availableDates.has(selectedDateKey)) {
            return;
        }

        const first = Array.from(availableDates).sort()[0];
        setSelectedDate(dayjs(first));
    }, [availableDates, selectedDate, setSelectedDate]);
};
