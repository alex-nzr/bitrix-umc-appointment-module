import React from "react";
import { AppointmentForm } from "../features/appointment-flow/AppointmentForm";
import {StartButton} from "../features/start-button/StartButton";
import {CustomButtonBinding} from "../features/start-button/CustomButtonBinding";
import {useSettings} from "../features/appointment-flow/hooks/useSettings";
import './App.css';

export const App = () => {
    const { useCustomButton } = useSettings();

    return (
        <>
            <AppointmentForm/>
            {useCustomButton ? <CustomButtonBinding/> : <StartButton/>}
        </>
    );
};
