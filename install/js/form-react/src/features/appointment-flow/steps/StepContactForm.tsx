import React, {useEffect, useState} from "react";
import {DialogContentText, Link, Stack, Typography} from "@mui/material";
import {useAppointmentStore} from "../../../shared/store/appointmentStore";
import {NavigationButtons} from "./NavigationButtons";
import {TextInput} from "./StepContactForm/TextInput";
import {useSettings} from "../hooks/useSettings";
import {validateContact} from "../../../entities/appointment/validator";
import {getFormFields} from "../../../entities/appointment/formFields";
import {useAppointment} from "../hooks/useAppointment";
import {ConfirmationType} from "../../../shared/settings/widgetSettings";

export const StepContactForm = () => {
    const {
        prevStep,
        nextStep,
        contact,
        setContact
    } = useAppointmentStore();

    const [isValid, setIsValid] = useState(false)
    const {privacyPolicyUrl, confirmationType, phoneInputMask} = useSettings();
    const {sendAppointment, appointmentError} = useAppointment();

    useEffect(() => {
        if (!contact)
        {
            return;
        }
        setIsValid(validateContact(contact, confirmationType));
    }, [contact, confirmationType]);

    const submit = async() => {
        if (isValid)
        {
            if (confirmationType === ConfirmationType.none)
            {
                const isSuccess = await sendAppointment();
                if (isSuccess)
                {
                    nextStep();
                }
            }
            else
            {
                nextStep();
            }
        }
    }

    return (
        <Stack spacing={3}>
            {appointmentError && (
                <Typography component="h1" color={'red'} variant="subtitle1" align="center">{appointmentError}</Typography>
            )}

            {
                getFormFields(confirmationType, phoneInputMask).map(field => {
                    return (
                        <TextInput key={field.name}
                                   {...field}
                                   value={contact[field.name] ?? ''}
                                   setValue={(value: any) => {
                                       setContact({
                                           ...contact,
                                           [field.name]: value
                                       });
                                   }}
                        />
                    );
                })
            }

            {privacyPolicyUrl && (
                <DialogContentText sx={{textAlign:'center'}}>
                    <span>Отправляя заявку вы соглашаетесь с </span>
                    <Link
                        href={privacyPolicyUrl}
                        target="_blank"
                        variant="body2"
                    >
                        политикой конфиденциальности
                    </Link>
                    <span> сайта</span>
                </DialogContentText>
            )}

            <NavigationButtons
                backHandler={prevStep}
                nextHandler={submit}
                nextText="Записаться"
                nextDisabled={!isValid}
            />
        </Stack>
    );
};