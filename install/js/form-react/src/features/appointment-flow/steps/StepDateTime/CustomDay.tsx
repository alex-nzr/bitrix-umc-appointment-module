import {PickersDay, PickersDayProps} from "@mui/x-date-pickers";

type CustomDayExtraProps = {
    availableDates?: Set<string>;
};

export const CustomDay = (props: PickersDayProps & CustomDayExtraProps) => {
    const { day, availableDates, ...other } = props;

    const hasSlots = availableDates?.has(day.format("YYYY-MM-DD"));

    return (
        <PickersDay
            {...other}
            day={day}
            sx={
                hasSlots
                    ? {
                        bgcolor: "aquamarine",
                        "&:hover": {
                            bgcolor: "lightgreen",
                        },
                    }
                    : undefined
            }
        />
    );
};