import {Event} from 'main.core';

export class EventManager extends Event.EventEmitter{
    static fullDataLoaded = 'BX.FirstBit.Appointment:dataLoaded';

    static bind(target: Element, eventName: string, handler: (event: Event) => void, options?: {
        capture?: boolean,
        once?: boolean,
        passive?: boolean,
    }): void{
        Event.bind(target, eventName, handler, options);
    }
}