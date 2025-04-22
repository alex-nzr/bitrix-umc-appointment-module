import {String as StringHelper} from "./String";

export class DateTime
{
    constructor() {
        this.stringHelper = new StringHelper();
    }

    convertDateToISO (timestamp: number): string
    {
        const date = this.readDateInfo(timestamp);

        return `${date.year}-${date.month}-${date.day}T${date.hours}:${date.minutes}:00`;
    }

    convertDateToDisplay (timestamp: number, onlyTime: boolean = false, onlyDate: boolean = false): string
    {
        const date = this.readDateInfo(timestamp);

        if (onlyTime){
            return `${date.hours}:${date.minutes}`;
        }
        if (onlyDate){
            return `${date.day}.${date.month}.${date.year}`;
        }
        return `${date.day}-${date.month}-${date.year}`;
    }

    readDateInfo(timestampOrISO: string|number)
    {
        const date = new Date(timestampOrISO);

        let day = `${date.getDate()}`;
        if (Number(day)<10) {
            day = `0${day}`;
        }

        let month = `${date.getMonth()+1}`;
        if (Number(month)<10) {
            month = `0${month}`;
        }

        let hours = `${date.getHours()}`;
        if (Number(hours)<10) {
            hours = `0${hours}`;
        }

        let minutes = `${date.getMinutes()}`;
        if (Number(minutes)<10) {
            minutes = `0${minutes}`;
        }

        let seconds = `${date.getSeconds()}`;
        if (Number(seconds) < 10)
        {
            seconds = `0${seconds}`;
        }

        return {
            day,
            month,
            year: date.getFullYear(),
            hours,
            minutes,
            seconds,
            weekDay: this.stringHelper.ucFirst(date.toLocaleString('ru', {weekday: 'short'}))
        }
    }
}