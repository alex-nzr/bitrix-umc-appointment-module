

export const Service = {
    name: 'Service',
    template:
        `
            <div class="appointment-form-field" style="max-width: unset">
                <label>{{$Bitrix.Loc.getMessage('ANZ_JS_VUE_SERVICE_STEP_TITLE')}}</label>
                <select name="service" class="service-select" style="max-width: unset">
                    <option disabled selected>Выберите услугу</option>
                    <option value="00-000000000023">Прием (осмотр, консультация) врача-оториноларинголога первичный</option>
                    <option value="00-000000000024">Прием (осмотр, консультация) врача-оториноларинголога повторный</option>
                    <option value="00-000000000060">Тональная аудиометрия</option>
                    <option value="00-000000000065">Вазотомия</option>
                    <option value="00-000000000090">Вестибулометрия</option>
                </select>
            </div>
        `
}