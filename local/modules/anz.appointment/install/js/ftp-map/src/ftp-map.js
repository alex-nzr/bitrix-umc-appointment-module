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

import "./style.css";
import "ui.buttons";
import {Extension} from 'main.core';
import { PopupManager } from 'main.popup';

export const FtpMap = {
    controller: 'anz:appointment.oneCController',
    options: Extension.getSettings('anz.appointment.ftp-map'),
    ftpMapSaveBtnId: 'ftp-map-save-btn',
    ftpMapFormId: `ftp-map-form-${Math.round(Math.random() * 10000)}`,
    getClinicsBtnId: 'ftp-map-get-clinics-btn',
    createdHandleInputRows: 0,

    init: function(inputId){
        this.optionInput = BX(inputId);
        this.valueNode   = BX(`${inputId}_block`)
        this.changeBtn   = BX('ftp-map-change-btn');
        this.changeBtn && this.changeBtn.addEventListener('click', () => {
            this.showFtpMapPopup();
        });
    },

    getClinics: function () {
        this.getClinicsBtn = BX(this.getClinicsBtnId);
        this.getClinicsBtn && this.getClinicsBtn.classList.add('ui-btn-wait');
        BX.ajax.runAction(`${this.controller}.getClinics`, {
            data: this.createFormData()
        }).then(response => {
            if (response.status === 'success')
            {
                this.renderInputsFromOneCData(response.data);
            }
            else
            {
                throw new Error('Something went wrong. Unknown response status - '.response.status);
            }
        }).catch(e => {
            console.log(e);
            this.getClinicsBtn && this.getClinicsBtn.classList.remove('ui-btn-wait');
        })
    },

    createFormData: function(argsObject = {}) {
        const formData = new FormData();

        for (let key in argsObject)
        {
            formData.set(key, argsObject[key]);
        }
        formData.set('sessid', BX.bitrix_sessid());
        formData.set(this.options.ignoreFtpOptionKey, 'Y');

        return formData;
    },

    showFtpMapPopup() {
        this.ftpMapForm = this.createFtpMapForm();
        this.ftpMapPopup = PopupManager.create(
            "ftp-map-popup",
            null,
            {
                content: this.ftpMapForm,
                width: 700,
                closeIcon: true,
                titleBar: '',
                closeByEsc: true,
                overlay: {
                    backgroundColor: 'black',
                    opacity: 500
                },
                buttons: [
                    new BX.PopupWindowButton({
                        text: BX.message('SAVE_TEXT'),
                        className: 'ui-btn ui-btn-primary',
                        id: this.ftpMapSaveBtnId,
                        events: {
                            click: () => {
                                this.saveFtpMapValue();
                                this.ftpMapPopup.close();
                            }
                        }
                    }),
                ],
            }
        );
        this.ftpMapPopup.show();
    },

    createFtpMapForm() {
        return BX.create('form', {
            props: {
                id: this.ftpMapFormId,
                classList: 'ftp-map-changing-form'
            },
            children: [
                BX.create('table',{
                    children: [
                        BX.create('tbody',{
                            children: [
                                BX.create('tr',{
                                    props: {
                                        classList: 'btn-row'
                                    },
                                    children: [
                                        BX.create('td',{
                                            children: [
                                                BX.create('button',{
                                                    props: {
                                                        classList: 'ui-btn ui-btn-success',
                                                        type: 'button'
                                                    },
                                                    text: BX.message('HANDLE_INPUT'),
                                                    events: {
                                                        click: (e) => {
                                                            const tbody  = e.target.closest('tbody');
                                                            const btnRow = e.target.closest('tr');

                                                            if (btnRow && tbody)
                                                            {
                                                                tbody.append(this.createHandleInputRow());
                                                                btnRow.innerHTML = '';
                                                                btnRow.append(this.createHandleInputMoreBtn(tbody));
                                                            }
                                                        }
                                                    }
                                                }),
                                            ]
                                        }),
                                        BX.create('td',{
                                            children: [
                                                BX.create('button',{
                                                    props: {
                                                        classList: 'ui-btn ui-btn-success',
                                                        id: this.getClinicsBtnId,
                                                        type: 'button'
                                                    },
                                                    text: BX.message('LOAD_FROM_1C'),
                                                    events: {
                                                        click: () => this.getClinics()
                                                    }
                                                }),
                                            ]
                                        })
                                    ]
                                })
                            ]
                        })
                    ]
                })
            ]
        })
    },

    saveFtpMapValue() {
        if (this.ftpMapForm)
        {
            const formData = new FormData(this.ftpMapForm);
            const valueObj = {};

            let lastGuid    = null;
            let guidCounter = 0;
            formData.forEach(function(value, key){
                if (key.indexOf('guid_') === 0)
                {
                    guidCounter        = key.split('_')[1];
                    lastGuid           = value;
                    valueObj[lastGuid] = null;
                }
                else if(key.indexOf('path_') === 0)
                {
                    let pathCounter = key.split('_')[1];
                    if (pathCounter === guidCounter)
                    {
                        valueObj[lastGuid] = value;
                    }
                }
            });

            this.renderOptionValue(valueObj);

            this.optionInput && (this.optionInput.value = JSON.stringify(valueObj));
        }
    },

    renderOptionValue(valueObj){
        if (BX.type.isDomNode(this.valueNode))
        {
            const currentRows = this.valueNode.querySelectorAll('.table-value-row');
            currentRows.length && currentRows.forEach(row => {
                row.remove();
            });

            const tbody = this.valueNode.querySelector('tbody');
            if (tbody)
            {
                for (const key in valueObj)
                {
                    tbody.prepend(
                        BX.create('tr', {
                            props: {
                                classList: 'table-value-row',
                            },
                            children: [
                                BX.create('td', {
                                    text: key
                                }),
                                BX.create('td', {
                                    text: valueObj[key]
                                })
                            ]
                        })
                    );
                }
            }
        }
    },

    createHandleInputRow() {
        if (this.createdHandleInputRows >= 30)
        {
            return '';
        }

        this.createdHandleInputRows++;
        return BX.create('tr', {
            children: [
                BX.create('td', {
                    children: [
                        BX.create('input', {
                            attrs: {
                                type: 'text',
                                name: `guid_${this.createdHandleInputRows}`,
                                placeholder: BX.message('GUID_HINT')
                            }
                        })
                    ]
                }),
                BX.create('td', {
                    children: [
                        BX.create('input', {
                            attrs: {
                                type: 'text',
                                name: `path_${this.createdHandleInputRows}`,
                                placeholder: BX.message('PATH_HINT')
                            }
                        })
                    ]
                })
            ]
        });
    },

    createHandleInputMoreBtn(parentNode) {
        return BX.create('td', {
            attrs: {
                colspan: '2'
            },
            children: [
                BX.create('button',{
                    props: {
                        classList: 'ui-btn ui-btn-success-dark',
                        type: 'button'
                    },
                    text: BX.message('ADD_TEXT'),
                    events: {
                        click: () => parentNode && parentNode.append(this.createHandleInputRow())
                    }
                })
            ]
        });
    },

    renderInputsFromOneCData(clinics) {
        if ((typeof clinics === 'object') && this.getClinicsBtn)
        {
            const tbody  = this.getClinicsBtn.closest('tbody');
            const btnRow = this.getClinicsBtn.closest('tr');

            if (btnRow && tbody)
            {
                btnRow.innerHTML = '';
                let counter = 0;
                for (const key in clinics) {
                    counter++;
                    tbody.append(this.createImportedInputRow(clinics[key], counter));
                }
            }
        }
    },

    createImportedInputRow(clinic, counter) {
        return BX.create('tr', {
            children: [
                BX.create('td', {
                    children: [
                        BX.create('div', {
                            props:{
                                className: 'ftp-map-changing-form-table-clinic-name'
                            },
                            text: clinic.name
                        }),
                        BX.create('input', {
                            attrs: {
                                type: 'text',
                                name: `guid_${counter}`,
                                readonly: true,
                                value: clinic.uid
                            }
                        })
                    ]
                }),
                BX.create('td', {
                    children: [
                        BX.create('input', {
                            attrs: {
                                type: 'text',
                                name: `path_${counter}`,
                                placeholder: BX.message('PATH_HINT')
                            }
                        })
                    ]
                })
            ]
        });
    }
};