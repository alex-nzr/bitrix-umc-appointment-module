import React from 'react';
import AppointmentButton from "./AppointmentButton/AppointmentButton";
import AppointmentForm from "./AppointmentForm/AppointmentForm";
import {observer} from "mobx-react-lite";
import './App.css';

const App = () => {
    return (
          <>
              <AppointmentForm/>
              <AppointmentButton/>
          </>
    );
}

export default observer(App);