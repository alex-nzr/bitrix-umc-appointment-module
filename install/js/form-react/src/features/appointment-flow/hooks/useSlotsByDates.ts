import {Slot} from "../../../entities/slot/model";
import {useMemo} from "react";

export const useSlotsByDates = (slots: Slot[]) => {
    return useMemo(() => {
        const map = new Map<string, Slot[]>();
        slots.forEach(slot => {
            if (slot.isAvailable)
            {
                const date = slot.date.slice(0, 10);
                if (!map.has(date)) {
                    map.set(date, []);
                }
                map.get(date)!.push(slot);
            }
        });
        return map;
    }, [slots]);
};