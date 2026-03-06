import React, { useState, useMemo } from "react";
import {
    Stack,
    TextField,
} from "@mui/material";
import { useAppointmentStore } from "../../../shared/store/appointmentStore";
import { NavigationButtons } from "./NavigationButtons";
import {DatePicker} from "@mui/lab";
import {Dayjs} from "dayjs";

export const StepContactForm = () => {

    const {
        prevStep,
        goToStep,
    } = useAppointmentStore();

    const [form, setForm] = useState<{
        name: string;
        middleName: string;
        surname: string;
        phone: string;
        email: string;
        comment: string;
        birthday: Dayjs | null;
    }>({
        name: '',
        middleName: '',
        surname: '',
        phone: '',
        email: '',
        comment: '',
        birthday: null
    });

    const handleChange = (field: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm(prev => ({
            ...prev,
            [field]: e.target.value
        }));
    };

    const isValid = useMemo(() => {
        return (
            form.name.trim() &&
            form.surname.trim() &&
            form.phone.trim() &&
            form.birthday
        );
    }, [form]);

    const submit = () => {
        if (!isValid) return;

        //createBooking(form);
        goToStep(4);
    };

    return (
        <Stack spacing={3}>

            <TextField
                label="Фамилия"
                value={form.surname}
                onChange={handleChange("surname")}
                required
                fullWidth
            />

            <TextField
                label="Имя"
                value={form.name}
                onChange={handleChange("name")}
                required
                fullWidth
            />

            <TextField
                label="Отчество"
                value={form.middleName}
                onChange={handleChange("middleName")}
                fullWidth
            />

            <TextField
                label="Телефон"
                value={form.phone}
                onChange={handleChange("phone")}
                required
                fullWidth
            />

            <TextField
                label="Email"
                value={form.email}
                onChange={handleChange("email")}
                type="email"
                fullWidth
            />

            <DatePicker
                label="Дата рождения"
                value={form.birthday}
                onChange={(date: Dayjs | null) =>
                    setForm(prev => ({
                        ...prev,
                        birthday: date
                    }))
                }
                slotProps={{
                    textField: {
                        required: true,
                        fullWidth: true
                    }
                }}
            />

            <TextField
                label="Комментарий"
                value={form.comment}
                onChange={handleChange("comment")}
                multiline
                rows={3}
                fullWidth
            />

            <NavigationButtons
                backHandler={prevStep}
                nextHandler={submit}
                nextText="Записаться"
                nextDisabled={!isValid}
            />

        </Stack>
    );
};