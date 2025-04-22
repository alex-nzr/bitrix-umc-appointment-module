import {appStore} from "../../../stores/appStore";
import {DateTime as DateTimeHelper} from "../../../Application/Tools/DateTime";
import {String as StringHelper} from "../../../Application/Tools/String";
import {VueDatePicker} from "../../../lib/vue-datepicker/vue-datepicker";
import '../../../lib/vue-datepicker/vue-datepicker.css'

export const Calendar = {
    name: 'Calendar',
    components: { VueDatePicker },
    data(){
        return {
            dateTimeHelper: new DateTimeHelper(),
            inputId: (new StringHelper()).uniqId('calendar_'),
            startDate: new Date(),
            minDate: new Date(),
            maxDate: new Date((new Date()).setMonth((new Date()).getMonth() + 1))
        }
    },
    computed: {
        date(){
            return new Date(appStore().selectedData.schedule.date)
        },
    },
    methods: {
        setDate(value): void
        {
            let val = '';
            if (value instanceof Date)
            {
                value.setHours(0);
                value.setMinutes(0);
                value.setSeconds(0);
                val = this.dateTimeHelper.convertDateToISO(value.getTime())
            }
            appStore().setDate(val)
        },

        formatDate(date: Date|null): string
        {
            if (date instanceof Date)
            {
                return this.dateTimeHelper.convertDateToDisplay(date.getTime());
            }
            return '';
        }
    },
    // language=Vue
    template:
        `
            <div class="appointment-form-field">
              <label>{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_DATE_STEP_TITLE')}}</label>
              <VueDatePicker
                  :uid="inputId"
                  :model-value="date"
                  @update:model-value="setDate"
                  :hide-navigation="['time']"
                  :inline="{ input: false }"
                  :start-date="startDate"
                  focus-start-date
                  :min-date="minDate"
                  :max-date="maxDate"
                  :format="formatDate"
                  locale="ru"
                  auto-apply
              />
            </div>
        `
}