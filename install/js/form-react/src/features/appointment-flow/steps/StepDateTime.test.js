import React, {act} from 'react';
import {createRoot} from 'react-dom/client';
import dayjs from 'dayjs';
import {StepDateTime} from './StepDateTime';
import {SettingsContext} from '../../../shared/settings/settingsContext';
import {useAppointmentStore} from '../../../shared/store/appointmentStore';
import {appointmentApi} from '../../../shared/api/appointmentApi';

jest.mock('./StepDateTime/Calendar', () => ({Calendar: () => null}));
jest.mock('./StepDateTime/SlotsList', () => ({SlotsList: () => null}));
jest.mock('../../../shared/api/appointmentApi', () => ({
    appointmentApi: {getCaptcha: jest.fn(), bookSlot: jest.fn()},
}));

let container;
let root;
let previousState;

beforeEach(() => {
    global.IS_REACT_ACT_ENVIRONMENT = true;
    previousState = useAppointmentStore.getState();
    const date = dayjs().format('YYYY-MM-DD');
    useAppointmentStore.setState({
        step: 2, clinicUid: 'clinic', doctorUid: 'doctor', serviceUIDs: [],
        isLoading: false, bookingUid: null, slotsCache: {clinic_doctor_: []},
        slot: {date, timeBegin: `${date}T12:00:00`, duration: 1800},
    });
    jest.clearAllMocks();
    appointmentApi.getCaptcha.mockResolvedValue({captchaSid: 'challenge', imageUrl: '/captcha.png'});
    appointmentApi.bookSlot.mockResolvedValue({uid: 'booking'});
    container = document.createElement('div');
    document.body.appendChild(container);
    root = createRoot(container);
});

afterEach(async () => {
    await act(async () => root.unmount());
    container.remove();
    useAppointmentStore.setState(previousState, true);
    delete global.IS_REACT_ACT_ENVIRONMENT;
});

const renderStep = async (captchaEnabled) => {
    await act(async () => root.render(
        <SettingsContext.Provider value={{servicesEnabled: false, captchaEnabled}}>
            <StepDateTime />
        </SettingsContext.Provider>
    ));
};

const nextButton = () => Array.from(container.querySelectorAll('button')).find(button => button.textContent === 'Далее');

test('disabled captcha allows booking without a challenge or hidden input', async () => {
    await renderStep(false);
    expect(container.querySelector('input')).toBeNull();
    expect(appointmentApi.getCaptcha).not.toHaveBeenCalled();
    expect(nextButton().disabled).toBe(false);
    await act(async () => nextButton().click());
    expect(appointmentApi.bookSlot).toHaveBeenCalledWith({
        clinicUid: 'clinic', doctorUid: 'doctor', date: dayjs().format('YYYY-MM-DD'),
        timeBegin: `${dayjs().format('YYYY-MM-DD')}T12:00:00`, duration: 1800,
    }, '', '');
    expect(useAppointmentStore.getState().bookingUid).toBe('booking');
    expect(useAppointmentStore.getState().step).toBe(3);
});

test.each([true, undefined])('enabled or missing setting requires a captcha answer: %s', async (setting) => {
    await renderStep(setting);
    expect(container.querySelector('input')).not.toBeNull();
    expect(appointmentApi.getCaptcha).toHaveBeenCalledTimes(1);
    expect(nextButton().disabled).toBe(true);
    expect(appointmentApi.bookSlot).not.toHaveBeenCalled();
});
