import { useMemo } from "react";
import { Slot } from "../../../entities/slot/model";
import dayjs from "dayjs";

export const useAvailableDates = (slots: Slot[]) => {

    return useMemo(() => {
        return new Set(
            slots
                .filter(s => s.isAvailable)
                .map(s => dayjs(s.date).format("YYYY-MM-DD"))
        );
    }, [slots]);

};