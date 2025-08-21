
import {Event} from 'main.core';

export class EventManager extends Event.EventEmitter{
    static clinicsLoaded = 'BX.Anz.Appointment:clinicsLoaded';
    static employeesLoaded = 'BX.Anz.Appointment:employeesLoaded';
    static nomenclatureLoaded = 'BX.Anz.Appointment:nomenclatureLoaded';
    static scheduleLoaded = 'BX.Anz.Appointment:scheduleLoaded';
    static fullDataLoaded  = 'BX.Anz.Appointment:dataLoaded';
    static clinicsRendered = 'BX.Anz.Appointment:clinicsRendered';
    static formStepChanged  = 'BX.Anz.Appointment:formStepChanged';

    static bind(target: Element, eventName: string, handler: (event: Event) => void, options?: {
        capture?: boolean,
        once?: boolean,
        passive?: boolean,
    }): void{
        Event.bind(target, eventName, handler, options);
    }
}