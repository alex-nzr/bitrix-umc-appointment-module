import {Form} from "../Form/Form"
import {StartBtn} from "../Button/StartBtn/StartBtn";
import {appStore} from "../../stores/appStore";
import './Wrapper.css'

export const Wrapper = {
    name: 'Wrapper',
    components: {Form, ShowBtn: StartBtn},
    data(){
        return {
            useCustomBtn: appStore().useCustomBtn
        }
    },
    computed: {
        isActive(){
            return appStore().opened
        },
    },
    methods: {
        toggle(){
            appStore().toggle()
        }
    },
    // language=Vue
    template: ` 
                <div class="appointment-popup-overlay"
                     :class="{active: isActive}"
                >
                  <Form/>
                </div>
                <template v-if="!useCustomBtn"><ShowBtn @click="toggle"/></template>
              `
}