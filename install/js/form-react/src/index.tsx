import React from 'react';
import ReactDOM from 'react-dom';
import App from './components/App';
import {CssBaseline} from "@mui/material";
import {configure, spy} from "mobx";

configure({
    reactionScheduler: (f) => {
        setTimeout(f)
    }
})

if (process.env.NODE_ENV !== 'production'){
    spy((event) => {
        event.type === "action" ? console.log(event) : void(0)
    })
}

const root = document.createElement('div');
root.setAttribute('id', 'appointment-widget-root-'+Number(new Date()));

document.addEventListener('DOMContentLoaded', () => document.body.append(root));

ReactDOM.render(
  <React.StrictMode>
      <CssBaseline />
      <App />
  </React.StrictMode>,
    root
);
