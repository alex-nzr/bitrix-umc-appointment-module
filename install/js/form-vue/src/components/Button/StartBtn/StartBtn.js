
import './StartBtn.css'
import {appStore} from "../../../stores/appStore";

export const StartBtn = {
    components: {},
    props:[
        ''
    ],
    computed: {
        classList(){
            return `${appStore().opened ? ' active' : ''}`
        },
    },
    // language=Vue
    template: `
      <div class="appointment-start-button-wrapper">
        <button :class="classList"><span>{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_APP_START_BTN_TEXT')}}</span></button>
      </div>`
}