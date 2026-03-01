import React from "react";
import { AppointmentForm } from "../features/appointment-flow/AppointmentForm";
import {StartButton} from "../features/start-button/StartButton";
import './App.css';

export const App = () => {
    return (
        <>
            <AppointmentForm/>
            <StartButton/>
        </>
    );
};