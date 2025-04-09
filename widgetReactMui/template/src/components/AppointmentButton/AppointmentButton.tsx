//import LoadingButton from '@mui/lab/LoadingButton';
import './AppointmentButton.css';
import {FC, useEffect} from "react";
import {observer} from "mobx-react-lite";
import appState from "../../store/AppState";
import dataState from "../../store/OneCDataState";
import {IResponse} from "../../types/models";

const AppointmentButton:FC = () => {
    useEffect(()=>{
        if (appState.isNeedToLoad){
            appState.isLoading = true;
            dataState.loadData()
                .then((res: IResponse) => {
                    if (res && res.error){
                        console.error("Loading data error - " + res.error);
                        appState.isCanRender = false;
                    }
                    else if (res && res.success){
                        appState.isLoading = false;
                    }
                    else{
                        console.error("Loading data error.", res);
                        appState.isCanRender = false;
                    }
                })
                .finally(() => appState.isNeedToLoad = false)
        }
    });

    const buttons = document.querySelectorAll(
        `.${appState.commonBtnClass}, .${appState.employeeBtnClass}, .${appState.serviceBtnClass}`
    );

    if (buttons.length)
    {
        if (!appState.isCustomButtonsAdded)
        {
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.hasAttribute('uid'))
                    {
                        const uid = btn.getAttribute('uid');
                        if (uid)
                        {
                            if (btn.classList.contains(appState.employeeBtnClass))
                            {
                                return appState.toggleAppointmentForm(true, uid);
                            }
                            else if (btn.classList.contains(appState.serviceBtnClass))
                            {
                                return appState.toggleAppointmentForm(true, '', uid);
                            }
                        }
                    }
                    return appState.toggleAppointmentForm(true);
                });
            });
            appState.isCustomButtonsAdded = true;
        }

        if (appState.isLoading)
        {
            buttons.forEach(btn => {
                btn.classList.add('btn-disabled');
            });
        }
        else
        {
            buttons.forEach(btn => {
                btn.classList.remove('btn-disabled');
            });
        }
    }

    if (!appState.isCanRender){
        return <></>
    }

    return <span id={`appointment-state-indicator`}
                 className={`${appState.isLoading ? 'loading' : 'loaded'} ${appState.isAppOpen ? 'opened' : 'closed'}`}
           >
           </span>;
    /*return (
        <div id={`appointment-button-wrapper`}
             className={`${ !appState.isLoading && !appState.isAppOpen ? 'pulse' : ''}`}
        >
            <LoadingButton
                onClick={() => appState.toggleAppointmentForm(true)}
                loading={appState.isLoading}
                loadingPosition="center"
                variant={`${appState.isAppOpen ? 'outlined' : 'contained'}`}
            >
                Запись на приём
            </LoadingButton>
        </div>
    );*/
}

export default observer(AppointmentButton);