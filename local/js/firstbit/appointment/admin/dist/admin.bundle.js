'use strict';

BX.ready(function () {
    if (!BX.FirstBit?.Appointment){
        if (!BX.FirstBit){
            BX.FirstBit = {};
        }
        BX.FirstBit.Appointment = { Admin: {} };
    }
    else{
        BX.FirstBit.Appointment.Admin = {};
    }

    const fBitAdmin = BX.FirstBit.Appointment.Admin;

    fBitAdmin.deleteRecord = function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'deleteOrder')
    }

    fBitAdmin.updateRecord = function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'getOrderStatus')
    }

    fBitAdmin.runAction = function (id, gridId, orderUid, actionToCall) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        grid && grid.tableFade();

        const ajaxUrl = '/bitrix/services/main/ajax.php';
        const action = `firstbit:appointment.oneCController.${actionToCall}`;

        const formData = new FormData();
        formData.set('id', id);
        formData.set('orderUid', orderUid);
        formData.set('sessid', BX.bitrix_sessid());

        this.requestParams = {
            method: 'POST',
            body: formData,
        }

        fetch(`${ajaxUrl}?action=${action}`, this.requestParams)
            .then(response => {
                if (response.ok) {
                    return response.json();
                }else{
                    console.log(`Error. Status code ${response.status}`);
                }
            })
            .then(json => {
                if (json.status === 'error'){
                    console.log(json);
                }
            })
            .catch(e => console.log(e))
            .finally(() => (grid && grid.reloadTable()))
    }

    fBitAdmin.bindColorPickerToNode = function (nodeId, inputId, defaultColor = '') {
        const element = BX(inputId);
        const input = BX(inputId);
        BX.bind(element, 'click', function () {
            new BX.ColorPicker({
                bindElement: element,
                defaultColor: defaultColor ?? '#FFFFFF',
                allowCustomColor: true,
                onColorSelected: function (color) {
                    input.value = color;
                },
                popupOptions: {
                    angle: true,
                    autoHide: true,
                    closeByEsc: true,
                    events: {
                        onPopupClose: function () {}
                    }
                }
            }).open();
        })
    }

    fBitAdmin.runInputActions = function(){
        const checkBox = BX('appointment_view_use_custom_main_btn');
        this.checkInputs(checkBox);
        checkBox.addEventListener('change', () => this.checkInputs(checkBox))
    }

    fBitAdmin.checkInputs = function(checkbox){
        const textInput = BX('appointment_view_custom_main_btn_id');
        const bgColorInput = BX('--appointment-start-btn-bg-color');
        const textColorInput = BX('--appointment-start-btn-text-color');

        if (checkbox.checked){
            textInput.removeAttribute('disabled');
            bgColorInput.setAttribute('disabled', true);
            bgColorInput.style.opacity = '.5';
            textColorInput.setAttribute('disabled', true);
            textColorInput.style.opacity = '.5';
        }
        else {
            textInput.setAttribute('disabled', true);
            bgColorInput.removeAttribute('disabled');
            bgColorInput.style.opacity = '1';
            textColorInput.removeAttribute('disabled');
            textColorInput.style.opacity = '1';
        }
    }
});