import {appStore} from "../../../stores/appStore";
import { mapActions } from 'ui.vue3.pinia';
import {Entity} from "../../../Application/Model/Resource/Entity";

export const Specialty = {
    name: 'Specialty',
    setup() {
        return {
            specialtyUid: appStore().selectedData.specialty?.uid
        };
    },
    computed: {},
    methods: {
        setSpecialty: (uid, name) => void appStore().setSpecialty(uid, name),
    },
    // language=Vue
    template: `
    <div class="appointment-form-field">
        <label>{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_SPECIALTY_STEP_TITLE')}}</label>
        <select v-model="specialtyUid"
                placeholder="{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_SPECIALTY_SELECT_PH')}}"
                @change="setSpecialty($event.target.value, $event.target.dataset?.name)"
        >
          <option value="sp1">Spec-1</option>
          <option value="sp2">Spec-2</option>
          <option value="sp3">Spec-3</option>
        </select>
    </div>
    `
}