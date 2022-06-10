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
                        this.getLogoBlock(),
                        this.getCloseButton(),
                        this.getFormFirstBlock(),
                        this.getFormSecondBlock(),
                        this.getFormUserDataBlock(),
                        this.getFormMessageBlock(),
                        this.getFormPrivacyBlock(),
                        this.getFormResultBlock(),
                        this.getFormLoaderBlock(),
                    ]
                })
            ]
        });
    }

    getLogoBlock(){
        return BX.create('div', {
            attrs: {
                className: this.styles['appointment-form-head'],
            },
            children: [
                this.application.companyLogo ?
                    BX.create('img', {
                        attrs: {
                            className: this.styles['appointment-form-head-logo'],
                            src: this.application.companyLogo,
                            alt: 'company logo',
                        },
                    })
                : '',
            ]
        });
    }

    getCloseButton(){
        return BX.create('span', {
            attrs: {
                id: this.styles['appointment-form-close'],
            },
            html: '&#10006;'
        });
    }

    getFormFirstBlock(){
        const doctorBtn = this.getFormBtn(BX.message("FIRSTBIT_JS_FORM_BTN_DOCTOR_FIRST"), () => {
            this.application.setSelectionDoctorBeforeService(true);
        });
        const serviceBtn = this.getFormBtn(BX.message("FIRSTBIT_JS_FORM_BTN_SERVICE_FIRST"), () => {
            this.application.setSelectionDoctorBeforeService(false);
        });

        const buttons = this.application.useServices ? [doctorBtn, serviceBtn] : [doctorBtn];

        return BX.create('div', {
            attrs: {
                className: styles['appointment-form-step'],
                id: this.application.selectors.formStepIds.one
            },
            dataset: {
                services: !this.application.useServices ? 'disabled' : false
            },
            children: [
                ...(this.getSelectionNodes(
                    [this.application.dataKeys.clinicsKey, this.application.dataKeys.specialtiesKey]
                )),
                this.getFormButtonsBlock(buttons)
            ]
        });
    }

    getFormSecondBlock(){
        const btnPrev = this.getFormBtn(BX.message('FIRSTBIT_JS_FORM_BTN_PREV'), () => {
            this.application.changeFormStep(this.application.formStepNodes.one, true);
        }, false, true);
        const btnNext = this.getFormBtn(BX.message('FIRSTBIT_JS_FORM_BTN_NEXT'), () => {
            this.application.changeFormStep(this.application.formStepNodes.userData);
        });
        return BX.create('div', {
            attrs: {
                className: `${styles['appointment-form-step']} ${styles['hidden']}`,
                id: this.application.selectors.formStepIds.two
            },
            children: [
                ...(this.getSelectionNodes([
                    this.application.dataKeys.employeesKey,
                    this.application.useServices ? this.application.dataKeys.servicesKey : null,
                    this.application.dataKeys.scheduleKey
                ])),
                this.getFormButtonsBlock([btnPrev, btnNext])
            ]
        });
    }

    getFormUserDataBlock(){
        return BX.create('div', {
            attrs: {
                className: `${styles['appointment-form-step']} ${styles['hidden']}`,
                id: this.application.selectors.formStepIds.userData
            },
            children: [
                ...(this.getTextNodes()),
                this.getFormButtonsBlock([this.getFormSubmitBtn()])
            ]
        });
    }

    getFormMessageBlock(){
        return BX.create('p', {
            attrs: {
                id: this.application.selectors.messageNodeId,
            },
        });
    }

    getFormButtonsBlock(buttons: HTMLElement[]){
        return BX.create('div', {
            attrs: {
                className: this.styles['appointment-form-button-wrapper'],
            },
            children: buttons
        });
    }

    getFormSubmitBtn(){
        return BX.create('button', {
            attrs: {
                type: "submit",
                id: this.application.selectors.submitBtnId,
                className: this.styles['appointment-form-button'],
            },
            dataset: {
                "readonly": "Y"
            },
            text: BX.message('FIRSTBIT_JS_FORM_BTN_TEXT')
        });
    }

    getFormBtn(text: string, handler: Function, disabled: boolean = true, readonly = false){
        return BX.create('button', {
            attrs: {
                type: "button",
                className: this.styles['appointment-form-button'],
                disabled: disabled
            },
            dataset: {
                "readonly": readonly ? "Y" : "N"
            },
            text: text,
            events: {
                click: handler
            }
        });
    }

    getFormPrivacyBlock(){
        return BX.create('p', {
            attrs: {
                className: this.styles['appointment-info-message'],
            },
            children: [
                BX.create('span', {
                    text: `${BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_TEXT')} `
                }),
                BX.create('a', {
                    attrs: {
                        href: this.application.initParams['privacyPageLink'],
                        target: '_blank'
                    },
                    text: BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_LINK')
                }),
            ]
        });
    }

    getFormResultBlock(){
        return BX.create('div', {
            attrs: {
                id: this.application.selectors.appResultBlockId
            },
            children: [
                BX.create('p', {
                    text: ''
                }),
            ]
        });
    }

    getFormLoaderBlock(){
        return BX.create('div', {
            attrs: {
                className: this.styles['default-loader-wrapper']
            },
            html: `<svg class="${this.styles['default-loader-circular']}" viewBox="25 25 50 50">
                     <circle class="${this.styles['default-loader-path']}" cx="50" cy="50" r="20" fill="none" stroke-miterlimit="10"></circle>
                   </svg>`
        });
    }

    getSelectionNodes(blockKeys: string[])
    {
        const arNodes = [];
        blockKeys.length && blockKeys.forEach(key => {
            if (!this.application.selectionBlocks.hasOwnProperty(key)){
                return;
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

            const additionalClass = (key === this.application.dataKeys.clinicsKey) ? '' : this.styles['disabled'];
            arNodes.push(
                BX.create('div', {
                    attrs: {
                        id: this.application.selectionBlocks[key].blockId,
                        className: `${this.styles['selection-block']} ${additionalClass}`
                    },
                    children: [ selected, list, input ]
                })
            );
        });
        return arNodes;
    }

    getTextNodes() {
        const arNodes = [];

        for(const key in this.application.initParams['textBlocks'])
        {
            if (!this.application.initParams['textBlocks'].hasOwnProperty(key)){
                continue;
            }
            arNodes.push(
                BX.create('label', {
                    attrs: {
                        className: this.styles['appointment-form_input-wrapper'],
                    },
                    children: [
                        BX.create({
                            tag: this.application.initParams['textBlocks'][key]["type"] ? 'input' : 'textarea',
                            attrs: this.getTextInputAttrs(this.application.initParams['textBlocks'][key])
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

    getRootElement() {
        return BX.create('div', {
            attrs: {
                id: this.application.selectors.rootNodeId
            }
        });
    }

    renderSelectionItems(listNode, dataKey, items) {
        for(let key in items)
        {
            if(!items.hasOwnProperty(key)) continue;
            if(!this.application.allowToRender(listNode, dataKey, items[key])) continue;

            if(dataKey === this.application.dataKeys.scheduleKey)
            {
                this.renderScheduleItem(listNode, items[key]);
            }
            else
            {
                if(items[key].hasOwnProperty('price')){
                    const price = Number(items[key]['price']) > 0 ? `<b>${items[key]['price']}</b>&#8381;` : "";
                    items[key].fullName = `<p>${items[key].name}<br> <b>${price}</b></p>`;
                }
                const dataAttrs = {
                    uid:  items[key].uid ?? key,
                    name: items[key].fullName ?? items[key].name,
                }
                items[key].duration ? (dataAttrs.duration = items[key].duration): void(0);

                BX.append(BX.create('li', {
                    dataset: dataAttrs,
                    html: items[key].fullName ?? items[key].name
                }), listNode);
            }
        }

        if (listNode.children.length === 0){
            BX.append(
                this.createEmptySelectionNode(BX.message(`FIRSTBIT_JS_${dataKey.toUpperCase()}_NOT_FOUND_ERROR`)),
                listNode
            );
        }
        else
        {
            if(dataKey === this.application.dataKeys.scheduleKey){
                this.addHorizontalScrollButtons(listNode);
            }
            this.application.addItemActions(dataKey);
        }
    }

    renderScheduleItem(scheduleList, scheduleItem) {
        const serviceDuration = this.application.getServiceDuration(scheduleItem);
        const renderCustomIntervals = this.application.useServices && (serviceDuration > 0);
        const timeKey = renderCustomIntervals ? "freeNotFormatted" : "free";

        if (scheduleItem['timetable']?.[timeKey]?.length)
        {
            let intervals = scheduleItem['timetable'][timeKey];

            if (renderCustomIntervals)
            {
                const customIntervals = this.application.getIntervalsForServiceDuration(intervals, serviceDuration*1000);
                if (customIntervals.length === 0) {
                    return;
                }
                else {
                    intervals = customIntervals;
                }
            }

            let renderDate;
            let renderColumn = undefined;
            intervals.forEach((day, index) => {
                const isLast = (index === (intervals.length - 1));
                if ((day.date !== renderDate) || isLast)
                {
                    renderColumn ? scheduleList.append(renderColumn) : void(0);
                    !isLast || (intervals.length === 1) ? renderColumn = this.createDayColumn(day) : void(0);
                    renderDate = day.date;
                }

                if (renderColumn)
                {
                    BX.append(BX.create('span', {
                        dataset: {
                            displayDate: `${day['formattedDate']} `,
                            date:         day.date,
                            start:        day.timeBegin,
                            end:          day.timeEnd,
                        },
                        text: `${day['formattedTimeBegin']}`
                    }), renderColumn);
                }
            });
        }
    }

    createDayColumn(day){
        const date = this.application.readDateInfo(day.timeBegin);

        return BX.create('li', {
            children: [
                BX.create('p', {
                    text: `${date.weekDay}
                        ${day['formattedDate']}`
                })
            ]
        });
    }

    addHorizontalScrollButtons(scroller){
        const item = scroller.querySelector('li');

        if (scroller && item){
            const itemWidth = scroller.querySelector('li').clientWidth;

            BX.append(BX.create('div', {
                attrs: {
                    className: styles["horizontal-scroll-buttons"]
                },
                children: [
                    BX.create('button', {
                        attrs: {
                            type: "button"
                        },
                        text: "<",
                        events: {
                            click: () => {
                                if (scroller.scrollLeft !== 0) {
                                    scroller.scrollBy({ left: -itemWidth*3, top: 0, behavior: 'smooth' });
                                } else {
                                    scroller.scrollTo({ left: scroller.scrollWidth, top: 0, behavior: 'smooth' });
                                }
                            }
                        },
                    }),
                    BX.create('button', {
                        attrs: {
                            type: "button"
                        },
                        text: ">",
                        events: {
                            click: () => {
                                if (scroller.scrollLeft < (scroller.scrollWidth - itemWidth*3 - 10)) {
                                    scroller.scrollBy({ left: itemWidth*3, top: 0, behavior: 'smooth' });
                                } else {
                                    scroller.scrollTo({ left: 0, top: 0, behavior: 'smooth' });
                                }
                            }
                        },
                    }),
                ]
            }), scroller);
        }
    }

    getConfirmationBlock()
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
                        click: (e) => this.application.verifyConfirmCode(confirmInputNode.value, confirmWarningNode, e.target)
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



        this.application.startCodeTimerActions(confirmRepeatBtn);


        return confirmWrapper;
    }

    createEmptySelectionNode(message: string) {
        return BX.create('span', {
            attrs: {
                className: styles["empty-selection-message"]
            },
            text: message
        });
    }
}