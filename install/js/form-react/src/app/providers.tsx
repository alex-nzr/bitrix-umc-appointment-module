import { ThemeProvider, createTheme } from '@mui/material/styles'
import CssBaseline from '@mui/material/CssBaseline'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers'
import 'dayjs/locale/ru';
import React, { ReactNode } from "react";
import { WidgetSettings } from '../shared/settings/widgetSettings';
import { SettingsContext } from '../shared/settings/settingsContext';

interface ProvidersProps {
    children: ReactNode;
    settings: WidgetSettings;
}

export const Providers: React.FC<ProvidersProps> = ({ children, settings }) => {
    const theme = createTheme({
        palette: {
            primary: {
                main: settings.mainColor ?? ''
            }
        }
    })

    return (
        <SettingsContext.Provider value={settings}>
            <ThemeProvider theme={theme}>
                <style>{`
                    #appointment-button-wrapper.pulse:before{
                        background-color: ${settings.mainColor};
                    }
                `}</style>

                <LocalizationProvider
                    dateAdapter={AdapterDayjs}
                    adapterLocale="ru"
                >
                    <CssBaseline />
                    {children}
                </LocalizationProvider>
            </ThemeProvider>
        </SettingsContext.Provider>
    )
}