import {Entity} from "../Model/Resource/Entity";

export class AppState
{
    getters = {};
    actions = {};

    constructor() {
        this.getters = this.collectGetters();
        this.actions = this.collectActions();
    }

    getStoreName(){
        return 'applicationState';
    }

    state() {
        return {
            opened: false,
            useCustomBtn: false,
            selectedData: {
                clinic: {
                    uid: false,//"66abf7b4-2ff9-11df-8625-002618dcef2c",
                    name: false,//"Третий центр"
                },
                specialty: false,
                service: false,
                employee: false,
                schedule: {
                    date: false,//"2023-12-11T00:00:00",
                    start: false,//"2023-12-11T06:20:00",
                    end: false,//"2023-12-11T06:30:00"
                },
                personal: {
                    name: '',
                    surname: '',
                    middleName: '',
                    phone: '',
                    address: '',
                    email: '',
                    birthday: '',
                    comment: ''
                }
            }
        };
    }

    toggle() {
        this.opened = !this.opened;
        if (this.opened)
        {
            document.body.setAttribute('data-appointment-form-active', 'Y')
        }
        else
        {
            document.body.removeAttribute('data-appointment-form-active')
        }
    }

    setUseCustomBtn(value: boolean){
        this.useCustomBtn = value;
    }

    setBirthday(value) {
        this.selectedData.personal.birthday = value;
    }

    setSpecialty(uid, name) {
        this.selectedData.specialty = new Entity(uid, name);
    }

    setDate(date: string) {
        this.selectedData.schedule.date = date;
    }

    collectGetters()
    {
        return {

        };
    }

    collectActions()
    {
        return {
            toggle: this.toggle,
            setUseCustomBtn: this.setUseCustomBtn,
            setBirthday: this.setBirthday,
            setSpecialty: this.setSpecialty,
            setDate: this.setDate,
        };
    }
}