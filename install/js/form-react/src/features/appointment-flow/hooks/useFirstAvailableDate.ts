import { useEffect } from "react";
import dayjs, { Dayjs } from "dayjs";

export const useFirstAvailableDate = (
    availableDates: Set<string>,
    selectedDate: Dayjs,
    setSelectedDate: (d: Dayjs) => void
) => {

    useEffect(() => {
        if (availableDates.size === 0) return;
        const first = Array.from(availableDates)
            .sort()[0];

        if (!selectedDate.isSame(first, "day")) {
            setSelectedDate(dayjs(first));
        }

    }, [availableDates]);
};