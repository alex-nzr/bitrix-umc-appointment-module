import './Form.css'
import {InputError} from '../Text/InputError'
import {Birthday} from "./Steps/Birthday";
import {Specialty} from "./Steps/Specialty";
import {appStore} from "../../stores/appStore";
import {Entity} from "../../Application/Model/Resource/Entity";
import {EmployeeCard} from "./Steps/EmployeeCard";
import {Calendar} from "./Steps/Calendar";
import {Service} from "./Steps/Service";
import {Time} from "./Steps/Time";

export const Form = {
    name: 'Form',
    components: {Birthday,Specialty,DoctorCard: EmployeeCard, Calendar, Service, Time, InputError},
    props: [],
    data(){
        return {
            message: false
        };
    },
    computed: {
        showSpecialty(){
            return (typeof appStore().selectedData.personal.birthday === 'string')
            && appStore().selectedData.personal.birthday.length > 0;
        },
        showDoctor(){
            return this.showSpecialty && (appStore().selectedData.specialty instanceof Entity);
        }
    },
    methods: {
        submitForm() {
            this.message = 'Запись создана!';
        },
    },
    // language=Vue
    template:
        `<div class="appointment-form">
            <form @submit.prevent="void(0)">
                <p class="appointment-form-title">{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_APP_TITLE')}}</p>
                <p class="appointment-form-subtitle">{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_APP_SUBTITLE')}}</p>
                <div class="appointment-form-content">
                  <div class="appointment-form-block">
                    <Birthday/>
                    <template v-if="showSpecialty">
                      <Specialty/>
                    </template>
                    <template v-else>
                      <InputError/>
                    </template>
                  </div>
                </div>

                <template v-if="showDoctor">
                  <div class="appointment-form-content">
                    <div class="appointment-form-block">
                      <DoctorCard/>
                      <Calendar/>
                    </div>
                    <div class="appointment-form-block">
                      <Service/>
                    </div>
                    <div class="appointment-form-block">
                      <Time/>
                    </div>
                  </div>
                </template>
            </form>
            <div v-if="message">{{ message }}</div>
          </div>
        `
}