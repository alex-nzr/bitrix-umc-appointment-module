import {appStore} from "../../../stores/appStore";
import {DateTime as DateTimeHelper} from "../../../Application/Tools/DateTime";
import {String as StringHelper} from "../../../Application/Tools/String";
import {VueDatePicker} from "../../../lib/vue-datepicker/vue-datepicker";
import '../../../lib/vue-datepicker/vue-datepicker.css'

export const Birthday = {
    name: 'Birthday',
    components: { VueDatePicker },
    data(){
        return {
            dateTimeHelper: new DateTimeHelper(),
            inputId: (new StringHelper()).uniqId('birthday_'),
            startDate: new Date(2000, 0, 1)
        }
    },
    computed: {
        birthday(){
            return new Date(appStore().selectedData.personal.birthday)
        }
    },
    methods: {
        setBirthday(value): void
        {
            let birthdayVal = '';
            if (value instanceof Date)
            {
                birthdayVal = this.dateTimeHelper.convertDateToISO(value.getTime())
            }
            appStore().setBirthday(birthdayVal)
        },

        formatDate(date: Date|null): string
        {
            if (date instanceof Date)
            {
                return this.dateTimeHelper.convertDateToDisplay(date.getTime(), false, true);
            }
            return '';
        }
    },
    // language=Vue
    template: `
    <div class="appointment-form-field">
        <label>{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_BIRTHDAY_STEP_TITLE')}}</label>
        <VueDatePicker 
            :uid="inputId"
            :model-value="birthday"
            @update:model-value="setBirthday"
            :clearable="true" 
            :placeholder="$Bitrix.Loc.getMessage('ANZ_JS_VUE_BIRTHDAY_INPUT_PH')"
            :hide-navigation="['time']"
            :start-date="startDate"
            :format="formatDate"
            locale="ru"
            auto-apply
        />
    </div>
    `
}