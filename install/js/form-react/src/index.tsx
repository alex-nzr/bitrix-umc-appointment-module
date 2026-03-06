import React from "react";
import ReactDOM from "react-dom/client";
import { Providers } from "./app/providers";
import { App } from "./app/App";
import {WidgetSettings} from "./shared/settings/widgetSettings";

function mountAppointmentWidget(
    container: HTMLElement,
    settings: WidgetSettings
) {
    const root = ReactDOM.createRoot(container);

    root.render(
        <React.StrictMode>
            <Providers settings={settings}>
                <App />
            </Providers>
        </React.StrictMode>
    );
}

window.BX.ready(() => {
    try
    {
        const settings = window.BX.Extension.getSettings('anz.appointment.form-react');
        if (settings?.error)
        {
            console.error(settings.error);
        }
        else
        {
            const widgetRoot = document.createElement('div');
            widgetRoot.setAttribute('id', 'appointment-widget-root-'+Number(new Date()));
            document.body.append(widgetRoot);

            mountAppointmentWidget(widgetRoot, settings);
        }
    }
    catch (e) {
        console.error(e);
    }
});




