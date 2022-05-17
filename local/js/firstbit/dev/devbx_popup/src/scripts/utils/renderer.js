import styles from "../../styles/app.scss";

export class Renderer
{
    constructor(styles, application){
        this.styles = styles;
        this.application = application;
    }

    getAppHtmlSkeleton()
    {
        return BX.create('div', {
            attrs: {
                id: this.application.selectors.overlayId,
                className: this.styles['appointment-popup-overlay']
            },
            children: [
                BX.create('form', {
                    attrs: {
                        id: this.application.selectors.formId,
                        className: this.styles['appointment-form']
                    },
                    children: [
                        BX.create('span', {
                            attrs: {
                                id: this.styles['appointment-form-close'],
                            },
                            html: '&#10006;'
                        }),

                        ...(this.getSelectionNodes()),

                        ...(this.getTextNodes()),

                        BX.create('p', {
                            attrs: {
                                id: this.application.selectors.messageNodeId,
                            },
                        }),

                        BX.create('div', {
                            attrs: {
                                className: this.styles['appointment-form-button-wrapper'],
                            },
                            children: [
                                BX.create('button', {
                                    attrs: {
                                        type: "submit",
                                        id: this.application.selectors.submitBtnId,
                                        className: this.styles['appointment-form-button'],
                                    },
                                    text: BX.message('FIRSTBIT_JS_FORM_BTN_TEXT')
                                }),
                            ]
                        }),

                        BX.create('p', {
                            attrs: {
                                className: this.styles['appointment-info-message'],
                            },
                            children: [
                                BX.create('span', {
                                    text: `${BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_TEXT')} `
                                }),
                                BX.create('a', {
                                    attrs: {
                                        href: this.application.initParams.privacyPageLink,
                                        target: '_blank'
                                    },
                                    text: BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_LINK')
                                }),
                            ]
                        }),

                        BX.create('div', {
                            attrs: {
                                id: this.application.selectors.appResultBlockId
                            },
                            children: [
                                BX.create('p', {
                                    text: ''
                                }),
                            ]
                        }),

                        BX.create('div', {
                            attrs: {
                                className: this.styles['default-loader-wrapper']
                            },
                            html:   `<svg class="${this.styles['default-loader-circular']}" viewBox="25 25 50 50">
                                        <circle class="${this.styles['default-loader-path']}" cx="50" cy="50" r="20" fill="none" stroke-miterlimit="10"></circle>
                                    </svg>`
                        }),
                    ]
                })
            ]
        });
    }

    getSelectionNodes()
    {
        const arNodes = [];
        for(const key in this.application.selectionBlocks)
        {
            if (!this.application.selectionBlocks.hasOwnProperty(key)){
                continue;
            }

            const selected = BX.create('p', {
                attrs: {
                    id: this.application.selectionBlocks[key].selectedId,
                    className: this.styles['selection-item-selected']
                },
                text: this.application.defaultText[key],
                events: {
                    click: () => this.application.toggleSelectionList(key, selected, list)
                }
            });
            const list = BX.create('ul', {
                attrs: {
                    id: this.application.selectionBlocks[key].listId,
                    className: `${this.styles['selection-item-list']}`
                },
                text: this.application.defaultText[key]
            });
            const input = BX.create('input', {
                attrs: {
                    id: this.application.selectionBlocks[key].inputId,
                    name: this.application.selectionBlocks[key].inputId,
                    type: 'hidden',
                }
            });

            arNodes.push(
                BX.create('div', {
                    attrs: {
                        id: this.application.selectionBlocks[key].blockId,
                        className: `${this.styles['selection-block']} ${key === this.application.dataKeys.clinicsKey ? '' : this.styles['hidden']}`
                    },
                    children: [ selected, list, input ]
                })
            );
        }
        return arNodes;
    }

    getTextNodes() {
        const arNodes = [];

        for(const key in this.application.initParams.textBlocks)
        {
            if (!this.application.initParams.textBlocks.hasOwnProperty(key)){
                continue;
            }
            arNodes.push(
                BX.create('label', {
                    attrs: {
                        className: this.styles['appointment-form_input-wrapper'],
                    },
                    children: [
                        BX.create({
                            tag: this.application.initParams.textBlocks[key]["type"] ? 'input' : 'textarea',
                            attrs: this.getTextInputAttrs(this.application.initParams.textBlocks[key])
                        })
                    ]
                }),
            );
        }

        return arNodes;
    }

    getTextInputAttrs(attrs) {
        const preparedAttrs = {}
        for(const attr in attrs)
        {
            if (attrs.hasOwnProperty(attr))
            {
                if (attr === "class")
                {
                    preparedAttrs.className = this.styles[ attrs[attr] ];
                }
                else
                {
                    preparedAttrs[attr] = attrs[attr];
                }
            }
        }
        return preparedAttrs;
    }

    /**
     * Create start button elements
     * @returns {div}
     */
    getDefaultStartBtn() {
        return BX.create('div', {
            attrs: {
                id: this.application.selectors.startBtnWrapId,
                className: this.styles['appointment-button-wrapper']
            },
            children: [
                BX.create('button', {
                    attrs: {
                        id: this.application.selectors.startBtnId,
                    },
                    children: [
                        BX.create('span', {
                            text: BX.message('FIRSTBIT_JS_START_BTN_TEXT')
                        })
                    ]
                })
            ]
        });
    }

    getDivElement(id) {
        return BX.create('div', {
            attrs: {
                id: id
            }
        });
    }

    getConfirmationBlock(orderData)
    {
        const confirmWarningNode = BX.create('p', {
            attrs: {
                className: styles['appointment-warning-text']
            }
        });

        const confirmInputNode = BX.create('input', {
            attrs: {
                type: 'number',
                className: this.styles['appointment-form_input'],
                placeholder: BX.message("FIRSTBIT_JS_CONFIRM_CODE_MESSAGE"),
                required: 'true',
                autocomplete: 'new-password',
            },
            events: {
                input: (e) => {
                    if (e.target?.value?.length > 4){
                        e.target.value = e.target.value.substring(0, 4);
                    }
                }
            },
        });

        const confirmSubmitBtn = BX.create('div', {
            attrs: {
                className: styles['appointment-form-button-wrapper']
            },
            children: [
                BX.create('button', {
                    attrs: {
                        className: styles['appointment-form-button'],
                        type: 'button'
                    },
                    text: BX.message("FIRSTBIT_JS_SEND_BTN_TEXT"),
                    events: {
                        click: (e) => {
                            if (confirmInputNode && confirmWarningNode){
                                confirmWarningNode.textContent = '';
                                if (confirmInputNode.value && confirmInputNode.value.length === 4){
                                    this.application.form.classList.add(styles['sending']);
                                    this.application.verifyConfirmCode(confirmInputNode.value, orderData, confirmWarningNode, e.target);
                                }
                                else
                                {
                                    if (!confirmInputNode.value || (confirmInputNode.value.length !== 4)){
                                        confirmWarningNode.textContent = BX.message("FIRSTBIT_JS_CONFIRM_CODE_LENGTH");
                                    }
                                }
                            }
                        }
                    },
                }),
            ]
        });

        const confirmRepeatBtn = BX.create('a', {
            attrs: {
                className: styles['appointment-form-button-link'],
                href: "#"
            }
        });

        const confirmWrapper = BX.create('div', {
            attrs: {
                id: this.application.selectors.confirmWrapperId,
                style: "width: 100%",
            },
            children: [
                BX.create('label', {
                    attrs: {
                        className: styles['appointment-form_input-wrapper'],
                    },
                    children: [
                        confirmInputNode
                    ]
                }),
                confirmWarningNode,
                confirmSubmitBtn,
                confirmRepeatBtn,
            ]
        });

        const curTimeSeconds: number = Number(((new Date()).getTime() / 1000).toFixed(0));
        let remainingTime = this.application.timeExpires - curTimeSeconds;

        const interval = setInterval(() => {
            if (remainingTime <= 0)
            {
                confirmRepeatBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.application.sendConfirmCode(orderData);
                });
                clearInterval(interval);
            }
            else
            {
                remainingTime--;
                confirmRepeatBtn.textContent = `${BX.message("FIRSTBIT_JS_CONFIRM_CODE_SEND_AGAIN")} 
                                         ${remainingTime > 0 ? remainingTime : ''}`;
            }
        }, 1000);

        return confirmWrapper;
    }
}