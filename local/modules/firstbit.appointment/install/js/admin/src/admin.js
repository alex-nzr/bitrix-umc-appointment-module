/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - admin.js
 * 10.07.2022 22:37
 * ==================================================
 */
'use strict';

import "./admin.css";
import "color_picker";

export const Admin = {
    ajaxUrl: '/bitrix/services/main/ajax.php',
    controller: 'anz:appointment.oneCController',
    requestParams: {
        method: 'POST',
        body: '',
    },

    deleteRecord: function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'deleteOrder')
    },

    updateRecord: function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'getOrderStatus')
    },

    runAction: function (id, gridId, orderUid, actionToCall) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        grid && grid.tableFade();

        const action = `${this.controller}.${actionToCall}`;

        this.requestParams.body = this.createFormData({id, orderUid});

        fetch(`${this.ajaxUrl}?action=${action}`, this.requestParams)
            .then(response => {
                if (response.ok) {
                    return response.json();
                }else{
                    console.log(`Error. Status code ${response.status}`);
                }
            })
            .then(json => {
                if (json.status === 'error'){
                    //console.log(json);
                }
            })
            .catch(e => console.log(e))
            .finally(() => {
                if (grid)
                {
                    const reloadParams = { apply_filter: 'Y', clear_nav: 'N' };
                    const pageParams = {[gridId]: `page-${this.getGridCurrentPage(grid)}`};
                    grid.baseUrl = BX.Grid.Utils.addUrlParams(grid.baseUrl, pageParams);
                    grid.reloadTable('POST', reloadParams);
                }
            })
    },

    createFormData: function(argsObject) {
        const formData = new FormData();

        for (let key in argsObject)
        {
            if (argsObject.hasOwnProperty(key))
            {
                formData.set(key, argsObject[key]);
            }
        }
        formData.set('sessid', BX.bitrix_sessid());

        return formData;
    },

    getGridCurrentPage(gridInstance) {
        let curPage = 0;
        if (BX.type.isDomNode(gridInstance?.data?.pagination))
        {
            const curPageNode = gridInstance.data.pagination.querySelector('.main-ui-pagination-active');
            if (curPageNode){
                curPage = !isNaN(parseInt(curPageNode.textContent)) ? parseInt(curPageNode.textContent) : 0;
            }
        }
        return curPage;
    },

    bindColorPickerToNode: function (nodeId, inputId, defaultColor = '') {
        const element = BX(nodeId);
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
    },

    activateInputs: function(){
        const inputs = {
            customMainBtnCheckbox: BX('appointment_view_use_custom_main_btn')
        };

        for (let key in inputs){
            if (inputs.hasOwnProperty(key))
            {
                switch (key) {
                    case "customMainBtnCheckbox":
                        if(inputs[key]){
                            this.changeInputsState(inputs[key]);
                            inputs[key].addEventListener('change', () => this.changeInputsState(inputs[key]))
                        }
                        break;
                    default:
                        break;
                }
            }

        }
    },

    changeInputsState: function(checkbox){
        const textInput = BX('appointment_view_custom_main_btn_id');
        const bgColorInput = BX('--appointment-start-btn-bg-color');
        const textColorInput = BX('--appointment-start-btn-text-color');

        if (checkbox.checked)
        {
            textInput.removeAttribute('disabled');
            bgColorInput.setAttribute('disabled', true);
            textColorInput.setAttribute('disabled', true);
        }
        else
        {
            textInput.setAttribute('disabled', true);
            bgColorInput.removeAttribute('disabled');
            textColorInput.removeAttribute('disabled');
        }
    },
};