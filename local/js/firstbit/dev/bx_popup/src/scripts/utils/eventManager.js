/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - eventManager.js
 * 10.07.2022 22:37
 * ==================================================
 */
import {Event} from 'main.core';

export class EventManager extends Event.EventEmitter{
    static fullDataLoaded  = 'BX.FirstBit.Appointment:dataLoaded';
    static clinicsRendered = 'BX.FirstBit.Appointment:clinicsRendered';
    static formStepChanged  = 'BX.FirstBit.Appointment:formStepChanged';

    static bind(target: Element, eventName: string, handler: (event: Event) => void, options?: {
        capture?: boolean,
        once?: boolean,
        passive?: boolean,
    }): void{
        Event.bind(target, eventName, handler, options);
    }
}