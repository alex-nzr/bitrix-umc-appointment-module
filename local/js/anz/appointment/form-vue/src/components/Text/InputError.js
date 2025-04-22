

export const InputError = {
    props:[
        'empty',
        'invalid'
    ],
    // language=Vue
    template:
        `
          <span v-if="empty">{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_INPUT_REQUIRED_ERROR')}}</span>
          <span v-if="invalid">{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_INPUT_INVALID_ERROR')}}</span>
        `
}