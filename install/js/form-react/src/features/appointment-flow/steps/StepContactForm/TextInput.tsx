import React, {FC, useState} from 'react';
import {TextField} from "@mui/material";
import {EAdditionalFields, EContactFields} from "../../../../entities/appointment/model";
import {FormField} from "../../../../entities/appointment/formFields";
import {normalizeConfirmCode, validateConfirmCode} from "../../../../entities/appointment/validator";

interface TextInputProps extends FormField<EContactFields | EAdditionalFields>
{
    value: string
    setValue: (value: any) => void
}

export const TextInput: FC<TextInputProps> = ({name, label, required, multiline, placeholder, maskComponent, value, setValue}) => {
    const [helperText, setHelperText] = useState('');
    const [isChanged, setIsChanged]   = useState(false);
    const [isValid, setIsValid]   = useState<boolean|null>(null);

    const validate = (value: any) => {
        let text = '';
        let valid = true;

        switch (name)
        {
            case EContactFields.firstName:
            case EContactFields.lastName:
                if (!value || value.length < 2) {
                    valid = false;
                    text = 'От 2 до 50 символов';
                }
                break;

            case EContactFields.secondName:
                if (value && value.length < 2) {
                    valid = false;
                    text = 'От 2 до 50 символов';
                }
                break;

            case EContactFields.phone:

                if (!phoneIsValid(value)) {
                    valid = false;
                    text = 'Некорректный номер';
                }

                break;

            case EContactFields.email:

                if (value && !emailIsValid(value)) {
                    valid = false;
                    text = 'Некорректный email';
                }

                break;
            case EContactFields.birthday:

                const validDate = /^\d{2}\.\d{2}\.\d{4}$/.test(value);

                if (!validDate) {
                    valid = false;
                    text = 'Введите дату в формате ДД.ММ.ГГГГ';
                    break;
                }

                const [d, m, y] = value.split('.').map(Number);
                const date = new Date(y, m - 1, d);

                if (
                    date.getFullYear() !== y ||
                    date.getMonth() !== m - 1 ||
                    date.getDate() !== d
                ) {
                    valid = false;
                    text = 'Некорректная дата';
                    break;
                }

                const age = new Date().getFullYear() - y;

                if (age < 0 || age > 120) {
                    valid = false;
                    text = "Некорректный год рождения";
                }

                break;
            case EContactFields.comment:
                text = `Использовано ${value?.length ?? 0} из 300`;
                break;

            case EAdditionalFields.code:
                if (!validateConfirmCode(value))
                {
                    text = `${normalizeConfirmCode(String(value)).length ?? 0} из 4 цифр`;
                }
                break;
        }

        setHelperText(text);
        setIsValid(valid);
    };

    const phoneIsValid = (phone: string) => {
        return /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/.test(phone);
    };

    const emailIsValid = (email:string) => {
        return email.match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };

    const handleChange = (value: any) => {

        if (!isChanged) {
            setIsChanged(true);
        }

        setValue(value);
        validate(value);
    };

    return <TextField
        size="small"
        required={required}
        multiline={multiline}
        maxRows={3}
        sx={{ mt: 1 }}
        name={name}
        label={label}
        value={value}
        onChange={(e) => handleChange(e.target.value)}
        error={isChanged && !isValid}
        helperText={helperText}
        fullWidth
        placeholder={placeholder}
        slotProps={{
            input: maskComponent ? { inputComponent: maskComponent } : undefined,
            htmlInput: {
                maxLength: multiline ? 300 : 50,
                autoComplete: name === EContactFields.phone ? "new-password" : "on"
            }
        }}
    />;
};
