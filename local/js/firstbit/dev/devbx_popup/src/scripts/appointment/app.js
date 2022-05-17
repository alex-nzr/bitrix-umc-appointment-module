// @disabled-flow
'use strict';
import styles from "../../styles/app.scss";
import {convertHexToHsl} from "../utils/functions";
import "date";
import {EventManager} from "../utils/eventManager";
import {Renderer} from "../utils/renderer";
import type {ITextObject} from "../../types/params";
import {TextInputNames} from "../../types/params";

export class AppointmentSteps
{
    step: string                  = '';
    phoneMask: string             = '+7(000)000-00-00';
    loaded: boolean               = false;
    timeExpires: number           = 0;
    requiredInputs: HTMLElement[] = [];
    initParams: any               = {};
    eventHandlersAdded            = {};
    dataKeys = {
        clinicsKey: "clinic",
        specialtiesKey: "specialty",
        employeesKey: "doctor",
        servicesKey: "service",
        scheduleKey: "schedule",
    };
    data = {
        clinics: [],
        employees: {},
        services: {},
        schedule: []
    };
    selectionBlocks = {};
    selectionNodes = {};
    textNodes = {};
    defaultText = {};
    isUpdate = false;//not used now, but this app prepared to make ability to update current records from public interface

    /**
     * AppointmentSteps constructor
     * @param params
     */
    constructor(params)
    {
        this.firstInit      = true;
        this.initParams     = params;
        this.selectors      = this.getAppSelectors(styles);
        this.selectionSteps = Object.values(this.dataKeys);

        this.useServices 					= (params.useServices === "Y");
        this.selectDoctorBeforeService 		= (params.selectDoctorBeforeService === "Y");
        this.useTimeSteps 					= (params.useTimeSteps === "Y");
        this.timeStepDurationMinutes		= Number(params.timeStepDurationMinutes);
        this.strictCheckingOfRelations		= (params.strictCheckingOfRelations === "Y");
        this.showDoctorsWithoutDepartment	= (params.showDoctorsWithoutDepartment === "Y");
        this.confirmTypes                   = params.confirmTypes;
        this.useConfirmWith                 = (params.useConfirmWith);
        this.useEmailNote                   = (params.useEmailNote === "Y");

        this.useCustomMainBtn = (params.useCustomMainBtn === "Y");
        this.customColors     = params.customColors ?? {};

        this.filledInputs = {
            [this.dataKeys.clinicsKey]: {
                clinicUid: false,
                clinicName: false,
            },
            [this.dataKeys.specialtiesKey]: {
                specialty: false,
                specialtyUid: false,
            },
            [this.dataKeys.servicesKey]: {
                serviceUid: false,
                serviceName: false,
                serviceDuration: false,
            },
            [this.dataKeys.employeesKey]: {
                refUid: false,
                doctorName: false,
            },
            [this.dataKeys.scheduleKey]: {
                orderDate: false,
                timeBegin: false,
                timeEnd: false,
            },
            textValues: {
                name: 		this.filledInputs?.textValues?.name       ?? false,
                surname: 	this.filledInputs?.textValues?.surname    ?? false,
                middleName: this.filledInputs?.textValues?.middleName ?? false,
                phone: 		this.filledInputs?.textValues?.phone      ?? false,
                address: 	this.filledInputs?.textValues?.address    ?? false,
                email: 	    this.filledInputs?.textValues?.email      ?? false,
                birthday:   this.filledInputs?.textValues?.birthday   ?? false,
                comment: 	this.filledInputs?.textValues?.comment    ?? false,
            },
        }

        this.prepareSelectionBlocksForRender();
        this.renderer = new Renderer(styles, this);
    }

    /**
     * create js objects that contains html ids and default textContent for selection blocks
     * this objects will be used for creating selection blocks html
     */
    prepareSelectionBlocksForRender(){
        this.selectionSteps.forEach(step => {
            this.selectionBlocks[step] = {
                "blockId":      `app_${step}_block`,
                "listId":       `app_${step}_list`,
                "selectedId":   `app_${step}_selected`,
                "inputId":      `app_${step}_value`,
                "isRequired":   !(step === this.dataKeys.servicesKey && this.initParams.useServices !== "Y")
            }
            this.defaultText[step] = BX.message(`FIRSTBIT_JS_APPOINTMENT_SELECT_${step.toUpperCase()}_TEXT`);
        });
    }

    /**
     * start application
     */
    run() {
        this.checkRoot();
        this.insertAppHtml();
        this.init();
    }

    /**
     * check root selector and creates it if needed
     */
    checkRoot(){
        if (!this.root || !BX.type.isDomNode(this.root))
        {
            this.root = this.renderer.getDivElement(this.selectors.rootNodeId);
            BX.append(this.root, document.body);
        }
        else
        {
            BX.cleanNode(this.root);
        }
    }

    /**
     * build basic html skeleton and insert it to DOM
     */
    insertAppHtml(){
        BX.append(
            this.renderer.getAppHtmlSkeleton(),
            this.root
        );

        !this.useCustomMainBtn && BX.append(this.renderer.getDefaultStartBtn(), this.root);
    }

    /**
     * start all application actions
     */
    init(){
        try {
            this.initCustomEvents();
            this.initStartBtn();
            this.initBaseNodes();
            this.initOverlayAction();
            this.initForm();
            this.initMobileCloseBtn();
            this.initSelectionNodes();
            this.initTextNodes();
            this.addPhoneMasks();
            this.addCalendarSelection();
            this.addCustomColors();
        }
        catch (e) {
            this.logResultErrors(e);
        }
    }

    /**
     * subscribing on custom js events
     */
    initCustomEvents(){
        EventManager.subscribe(EventManager.fullDataLoaded, () => {
            this.startRender();
        })
    }

    /**
     * find or create start button and add event handler for click
     */
    initStartBtn() {
        if(!this.firstInit && this.useCustomMainBtn){
            return;
        }
        const startBtnId = this.useCustomMainBtn ? this.initParams.customMainBtnId : this.selectors.startBtnId;
        this.startBtn = BX(startBtnId);
        if (BX.type.isDomNode(this.startBtn))
        {
            EventManager.bind(this.startBtn, 'click', this.togglePopup.bind(this));
        }
        else
        {
            throw new Error(`${BX.message('FIRSTBIT_JS_NODE_NOT_FOUND')} "${this.initParams.customMainBtnId}"`)
        }
    }

    /**
     * find all base nodes and save them to this object props
     */
    initBaseNodes() {
        this.overlay        = BX(this.selectors.overlayId);
        this.startBtnWrap   = BX(this.selectors.startBtnWrapId);
        this.mobileCloseBtn = BX(this.selectors.mobileCloseBtnId);
        this.messageNode    = BX(this.selectors.messageNodeId);
        this.submitBtn      = BX(this.selectors.submitBtnId);
        this.resultBlock    = BX(this.selectors.appResultBlockId);
    }

    /**
     * make popup hidden by click to overlay
     */
    initOverlayAction() {
        if (BX.type.isDomNode(this.overlay))
        {
            EventManager.bind(this.overlay, 'click', (e) => {
                if (e.target?.getAttribute('id') === this.selectors.overlayId){
                    this.togglePopup();
                }
            });
        }
    }

    /**
     * find form node and add event listeners
     */
    initForm() {
        this.form = BX(this.selectors.formId);
        if (this.form)
        {
            EventManager.bind(this.form, 'submit', this.submit.bind(this));
        }
        else
        {
            throw new Error(`${BX.message('FIRSTBIT_JS_NODE_NOT_FOUND')} ${this.selectors.formId}`)
        }
    }

    /**
     * find node and add event listener to close form
     */
    initMobileCloseBtn() {
        if (this.mobileCloseBtn)
        {
            EventManager.bind(this.mobileCloseBtn, 'click', this.togglePopup.bind(this))
        }
        else
        {
            throw new Error(`${BX.message('FIRSTBIT_JS_NODE_NOT_FOUND')} ${this.selectors.mobileCloseBtnId}`)
        }
    }

    /**
     * find nodes and save their data to this object
     */
    initSelectionNodes() {
        for (const key in this.selectionBlocks)
        {
            if (this.selectionBlocks.hasOwnProperty(key))
            {
                this.selectionNodes[key] = {
                    blockNode: 		BX(this.selectionBlocks[key].blockId),
                    listNode: 		BX(this.selectionBlocks[key].listId),
                    selectedNode: 	BX(this.selectionBlocks[key].selectedId),
                    inputNode: 		BX(this.selectionBlocks[key].inputId),
                }

                if (this.selectionBlocks[key].isRequired)
                {
                    this.requiredInputs.push(this.selectionNodes[key].inputNode);
                }
            }
        }
    }

    /**
     * find nodes, add actions and save their data to this object
     */
    initTextNodes() {
        this.initParams.textBlocks.forEach((block: ITextObject) => {
            const input = BX(block.id);
            if (!input){
                throw new Error(`${BX.message("FIRSTBIT_JS_NODE_NOT_FOUND")} ${block.id}`);
            }

            const currentValue = this.filledInputs.textValues[block.name];
            input.value = currentValue ? currentValue : '';
            if (input && currentValue && (block.name === TextInputNames.birthday)){
                const date = new Date(currentValue);
                input.value = this.convertDateToDisplay(date.getTime(), false, true);
            }

            EventManager.bind(input, 'input', (e)=> {
                let val: string = e.target.value ?? '';
                if (e.target.name === TextInputNames.phone && val.length > this.phoneMask.length){
                    val = val.substring(0, this.phoneMask.length)
                }
                this.filledInputs.textValues[block.name] = val;
            })

            if (block["data-required"] === "true")
            {
                this.requiredInputs.push(input);
            }
            else
            {
                if ((this.useConfirmWith === this.confirmTypes.email) && (block.name === TextInputNames.email))
                {
                    this.requiredInputs.push(input);
                }
            }

            this.textNodes[block.name] = {
                inputNode: input,
            }
        });
    }

    /**
     * loading data from 1c and build selectors html
     */
    start() {
        this.toggleLoader(true);
        this.loadData()
            .then(() => {
                this.loaded = true;
            })
            .catch(res => {
                !this.useCustomMainBtn && this.startBtnWrap.classList.add(styles['hidden']);
                this.logResultErrors(res);
            });
    }

    /**
     * sequentially loads data from 1c
     * @returns {Promise<any>}
     */
    async loadData(){
        const clinicsResponse = await this.getListClinic();

        if (clinicsResponse.data?.error)
        {
            return Promise.reject(clinicsResponse.data?.error)
        }
        else
        {
            if (clinicsResponse.data?.length > 0)
            {
                this.data.clinics = clinicsResponse.data;

                const employeesResponse = await this.getListEmployees();

                if (employeesResponse.data?.error)
                {
                    return Promise.reject(employeesResponse.data?.error)
                }
                else
                {
                    if (Object.keys(employeesResponse.data).length > 0)
                    {
                        this.data.employees = employeesResponse.data;
                        const scheduleResponse = await this.getSchedule();

                        if (scheduleResponse.data?.error)
                        {
                            return Promise.reject(scheduleResponse.data?.error)
                        }
                        else
                        {
                            if (scheduleResponse.data?.hasOwnProperty("schedule"))
                            {
                                this.data.schedule = scheduleResponse.data.schedule;
                                EventManager.emit(EventManager.fullDataLoaded);
                                return Promise.resolve();
                            }
                        }
                    }
                    else
                    {
                        return Promise.reject(BX.message("FIRSTBIT_JS_DOCTORS_NOT_FOUND_ERROR"))
                    }
                }
            }
            else
            {
                return Promise.reject(BX.message("FIRSTBIT_JS_CLINICS_NOT_FOUND_ERROR"))
            }
        }
    }

    /**
     * Load clinics list from 1c
     * @returns {Promise<any>}
     */
    getListClinic(){
        return BX.ajax.runAction('firstbit:appointment.oneCController.getClinics', {
            data: {
                sessid: BX.bitrix_sessid()
            }
        });
    }

    /**
     * Load employees list from 1c
     * @returns {Promise<any>}
     */
    getListEmployees(){
        return BX.ajax.runAction('firstbit:appointment.oneCController.getEmployees', {
            data: {
                sessid: BX.bitrix_sessid()
            }
        });
    }

    /**
     * Load doctor's schedule from 1c
     * @returns {Promise<any>}
     */
    getSchedule(){
        return BX.ajax.runAction('firstbit:appointment.oneCController.getSchedule', {
            data: {
                sessid: BX.bitrix_sessid()
            }
        });
    }

    /**
     * Load nomenclature list from 1c
     * @param clinicGuid
     * @returns {Promise<any>}
     */
    getListNomenclature(clinicGuid){
        return BX.ajax.runAction('firstbit:appointment.oneCController.getNomenclature', {
            data: {
                sessid: BX.bitrix_sessid(),
                clinicGuid: clinicGuid,
            }
        });
    }

    startRender(){
        const clinicsRendered = this.renderClinicList();
        if (clinicsRendered)
        {
            if (this.isUpdate === "Y")
            {
                for (const dataKey in this.filledInputs) {
                    if (this.filledInputs.hasOwnProperty(dataKey)
                        && this.selectionNodes.hasOwnProperty(dataKey))
                    {
                        this.filledInputs[dataKey] = JSON.parse(this.selectionNodes[dataKey].inputNode.value);
                    }
                }
                this.renderSpecialtiesList();
                this.renderEmployeesList();
                this.renderScheduleList();
            }
            setTimeout(() => {
                this.toggleLoader(false);
            }, 300)
        }
        else
        {
            throw new Error(BX.message("FIRSTBIT_JS_CLINICS_RENDER_ERROR"));
        }
    }

    /**
     * render clinics list
     * @returns {boolean}
     */
    renderClinicList(){
        let rendered = false;
        if(this.data.clinics.length)
        {
            if (this.selectionNodes.hasOwnProperty(this.dataKeys.clinicsKey))
            {
                const clinicsList = this.selectionNodes[this.dataKeys.clinicsKey].listNode;
                BX.cleanNode(clinicsList);
                this.data.clinics.forEach((clinic) => {
                    if (clinic.uid)
                    {
                        BX.append(BX.create('li', {
                            dataset: {
                                uid: clinic.uid,
                                name: clinic.name,
                            },
                            text: clinic.name
                        }), clinicsList);
                    }
                    else
                    {
                        throw new Error(`${BX.message("FIRSTBIT_JS_OBJECT_UID_ERROR")} ${clinic.name}`);
                    }
                });
                this.addItemActions(this.dataKeys.clinicsKey);
                rendered = true;
            }
            else
            {
                throw new Error(BX.message('FIRSTBIT_JS_CLINICS_NODE_NOT_FOUND_ERROR'));
            }
        }else{
            throw new Error(BX.message('FIRSTBIT_JS_CLINICS_NOT_FOUND_ERROR'));
        }
        return rendered;
    }

    renderSpecialtiesList(){
        if (this.selectionNodes.hasOwnProperty(this.dataKeys.specialtiesKey))
        {
            const specialtiesList = this.selectionNodes[this.dataKeys.specialtiesKey].listNode;
            BX.cleanNode(specialtiesList);

            if(Object.keys(this.data.employees).length > 0)
            {
                for (let uid in this.data.employees)
                {
                    if (this.data.employees.hasOwnProperty(uid))
                    {
                        const clinicCondition = (this.filledInputs[this.dataKeys.clinicsKey].clinicUid === this.data.employees[uid].clinicUid);
                        let canRender = true;
                        if(this.strictCheckingOfRelations){
                            canRender = clinicCondition;
                            if (this.showDoctorsWithoutDepartment){
                                canRender = clinicCondition || !this.data.employees[uid].clinicUid;
                            }
                        }

                        if (canRender && this.data.employees[uid]['specialty'])
                        {
                            const specialty = this.data.employees[uid]['specialty'];
                            const specialtyUid = this.createIdFromName(specialty);

                            const alreadyRendered = specialtiesList.querySelector(`[data-uid="${specialtyUid}"]`);
                            if (!alreadyRendered)
                            {
                                BX.append(BX.create('li', {
                                    dataset: {
                                        uid: specialtyUid,
                                        name: specialty,
                                    },
                                    text: specialty
                                }), specialtiesList);
                            }
                        }
                    }
                }
                if (specialtiesList.children.length === 0){
                    BX.append(BX.create('span', {
                        attrs: {
                            className: styles["empty-selection-message"]
                        },
                        text: BX.message('FIRSTBIT_JS_SPECIALTIES_NOT_FOUND_ERROR')
                    }), specialtiesList);
                }
                this.addItemActions(this.dataKeys.specialtiesKey);
            }
        }
        else
        {
            throw new Error(BX.message('FIRSTBIT_JS_SPECIALTIES_NODE_NOT_FOUND_ERROR'));
        }
    }

    renderServicesList(){
        if (this.selectionNodes.hasOwnProperty(this.dataKeys.servicesKey))
        {
            const servicesList = this.selectionNodes[this.dataKeys.servicesKey].listNode;
            BX.cleanNode(servicesList);

            if(Object.keys(this.data.services).length > 0)
            {
                for (let uid in this.data.services)
                {
                    if (!this.data.services.hasOwnProperty(uid)){

                    }

                    let renderCondition = (this.filledInputs[this.dataKeys.specialtiesKey].specialtyUid
                        === this.data.services[uid].specialtyUid);
                    if (this.selectDoctorBeforeService)
                    {
                        const selectedEmployeeUid = this.filledInputs[this.dataKeys.employeesKey].refUid;
                        renderCondition = renderCondition && this.data.employees[selectedEmployeeUid].services.hasOwnProperty(uid);
                    }

                    if (renderCondition)
                    {
                        let price = Number((this.data.services[uid]['price']).replace(/\s+/g, ''));

                        if (this.data.services.hasOwnProperty(uid)){
                            BX.append(BX.create('li', {
                                dataset: {
                                    uid: uid,
                                    duration: this.data.services[uid].duration,
                                },
                                children: [
                                    BX.create('p', {
                                        html:  `${this.data.services[uid].name}<br>
                                                ${price>0 ? "<b>"+price+"</b>&#8381;" : ""}`
                                    })
                                ]
                            }), servicesList);
                        }
                    }
                }
                if (servicesList.children.length === 0){
                    BX.append(BX.create('span', {
                        attrs: {
                            className: styles["empty-selection-message"]
                        },
                        text: BX.message('FIRSTBIT_JS_SERVICES_NOT_FOUND_ERROR')
                    }), servicesList);
                }
                this.addItemActions(this.dataKeys.servicesKey);
            }
        }
        else
        {
            throw new Error(BX.message('FIRSTBIT_JS_SERVICES_NODE_NOT_FOUND_ERROR'));
        }
    }

    renderEmployeesList() {
        if (this.selectionNodes.hasOwnProperty(this.dataKeys.employeesKey))
        {
            const empList = this.selectionNodes[this.dataKeys.employeesKey].listNode;
            empList.innerHTML = '';

            if(Object.keys(this.data.employees).length > 0) {
                for (let uid in this.data.employees)
                {
                    if (this.data.employees.hasOwnProperty(uid))
                    {
                        const selectedSpecialty = this.filledInputs[this.dataKeys.specialtiesKey].specialty;
                        const selectedClinic = this.filledInputs[this.dataKeys.clinicsKey].clinicUid;
                        const specialtyCondition = this.data.employees[uid]['specialty'] === selectedSpecialty;
                        const clinicCondition = selectedClinic === this.data.employees[uid].clinicUid;

                        let canRender = specialtyCondition;

                        if(this.strictCheckingOfRelations){
                            if (this.showDoctorsWithoutDepartment){
                                canRender = (specialtyCondition && !this.data.employees[uid].clinicUid)
                                    ||
                                    (specialtyCondition && clinicCondition);
                            }
                            else
                            {
                                canRender = specialtyCondition && clinicCondition;
                            }
                        }

                        if (canRender)
                        {
                            if (this.useServices && !this.selectDoctorBeforeService)
                            {
                                const selectedServiceUid = this.filledInputs[this.dataKeys.servicesKey].serviceUid;
                                if (!this.data.employees[uid].services.hasOwnProperty(selectedServiceUid)){
                                    continue;
                                }
                            }
                            BX.append(BX.create('li', {
                                dataset: {
                                    uid: uid,
                                },
                                text:  `${this.data.employees[uid].surname} ${this.data.employees[uid].name} ${this.data.employees[uid].middleName}`
                            }), empList);
                        }
                    }
                }
                if (empList.children.length === 0){
                    BX.append(BX.create('span', {
                        attrs: {
                            className: styles["empty-selection-message"]
                        },
                        text: BX.message('FIRSTBIT_JS_DOCTORS_PARAMS_NOT_FOUND_ERROR')
                    }), empList);
                }
                this.addItemActions(this.dataKeys.employeesKey);
            }
        }
        else
        {
            throw new Error(BX.message('FIRSTBIT_JS_DOCTORS_NODE_NOT_FOUND_ERROR'));
        }
    }

    renderScheduleList() {
        if (this.data.schedule.length)
        {
            const scheduleList = this.selectionNodes[this.dataKeys.scheduleKey].listNode;
            scheduleList.classList.add(styles["column-mode"]);
            BX.cleanNode(scheduleList);

            this.data.schedule.forEach((employeeSchedule) => {
                if (
                    employeeSchedule.clinicUid === this.filledInputs[this.dataKeys.clinicsKey].clinicUid
                    && employeeSchedule.refUid === this.filledInputs[this.dataKeys.employeesKey].refUid
                )
                {
                    const selectedEmployee = this.data.employees[employeeSchedule.refUid];
                    const selectedService = this.filledInputs[this.dataKeys.servicesKey];
                    let serviceDuration = Number(selectedService.serviceDuration);
                    if(selectedEmployee.services.hasOwnProperty(selectedService.serviceUid))
                    {
                        if (selectedEmployee.services[selectedService.serviceUid].hasOwnProperty("personalDuration")){
                            const personalDuration = selectedEmployee.services[selectedService.serviceUid]["personalDuration"];
                            serviceDuration = Number(personalDuration) > 0 ? Number(personalDuration) : serviceDuration;
                        }
                    }
                    const renderCustomIntervals = this.useServices && (serviceDuration > 0);
                    const timeKey = renderCustomIntervals ? "freeNotFormatted" : "free";

                    if (employeeSchedule.timetable[timeKey].length)
                    {
                        let intervals = employeeSchedule.timetable[timeKey];

                        if (renderCustomIntervals)
                        {
                            const customIntervals = this.getIntervalsForServiceDuration(intervals, serviceDuration*1000);

                            if (customIntervals.length === 0)
                            {
                                BX.append(BX.create('span', {
                                    attrs: {
                                        className: styles["empty-selection-message"]
                                    },
                                    text: BX.message('FIRSTBIT_JS_SCHEDULE_FREE_TIME_NOT_FOUND')
                                }), scheduleList);

                                return;
                            }
                            else
                            {
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
                    }else{
                        BX.append(BX.create('span', {
                            attrs: {
                                className: styles["empty-selection-message"]
                            },
                            text: BX.message('FIRSTBIT_JS_SCHEDULE_FREE_TIME_NOT_FOUND')
                        }), scheduleList);
                    }
                }
            });
            if (scheduleList.children.length === 0){
                BX.append(BX.create('span', {
                    attrs: {
                        className: styles["empty-selection-message"]
                    },
                    text: BX.message('FIRSTBIT_JS_SCHEDULE_FREE_TIME_NOT_FOUND')
                }), scheduleList);
            }
            this.addHorizontalScrollButtons();
            this.addItemActions(this.dataKeys.scheduleKey);
        }
        else
        {
            throw new Error(BX.message('FIRSTBIT_JS_SCHEDULE_NOT_FOUND_ERROR'));
        }
    }

    getIntervalsForServiceDuration(intervals, serviceDurationMs) {
        const newIntervals = [];
        intervals.length && intervals.forEach((day) => {
            const timestampTimeBegin = (new Date(day.timeBegin)).getTime();
            const timestampTimeEnd = (new Date(day.timeEnd)).getTime();
            const timeDifference = timestampTimeEnd - timestampTimeBegin;
            const appointmentsCount = Math.floor(timeDifference / serviceDurationMs);
            if (appointmentsCount > 0)
            {
                if (this.useTimeSteps && (serviceDurationMs >= 30*60*1000)) //use timeSteps only for services with duration>=30 minutes
                {
                    let start   = new Date(timestampTimeBegin);
                    let end     = new Date(timestampTimeBegin + serviceDurationMs);
                    while(end.getTime() <= timestampTimeEnd){
                        newIntervals.push({
                            "date": 				day.date,
                            "timeBegin": 			this.convertDateToISO(Number(start)),
                            "timeEnd": 				this.convertDateToISO(Number(end)),
                            "formattedDate": 		this.convertDateToDisplay(Number(start), false),
                            "formattedTimeBegin": 	this.convertDateToDisplay(Number(start), true),
                            "formattedTimeEnd": 	this.convertDateToDisplay(Number(end), true),
                        });
                        start.setMinutes(start.getMinutes() + this.timeStepDurationMinutes);
                        end.setMinutes(end.getMinutes() + this.timeStepDurationMinutes);
                    }
                }
                else
                {
                    for (let i = 0; i < appointmentsCount; i++)
                    {
                        let start = Number(new Date(timestampTimeBegin + (serviceDurationMs * i)));
                        let end = Number(new Date(timestampTimeBegin + (serviceDurationMs * (i+1))));
                        newIntervals.push({
                            "date": 				day.date,
                            "timeBegin": 			this.convertDateToISO(start),
                            "timeEnd": 				this.convertDateToISO(end),
                            "formattedDate": 		this.convertDateToDisplay(start, false),
                            "formattedTimeBegin": 	this.convertDateToDisplay(start, true),
                            "formattedTimeEnd": 	this.convertDateToDisplay(end, true),
                        });
                    }
                }
            }
        });
        return newIntervals;
    }

    createDayColumn(day){
        const date = this.readDateInfo(day.timeBegin);

        return BX.create('li', {
            children: [
                BX.create('p', {
                    text: `${date.weekDay}
                        ${day['formattedDate']}`
                })
            ]
        });
    }

    addHorizontalScrollButtons(){
        const scroller = this.selectionNodes[this.dataKeys.scheduleKey].listNode;
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

    toggleSelectionList(dataKey: string, selected: HTMLElement, list: HTMLElement)
    {
        list.classList.toggle(styles['active']);
        for (const nodesKey in this.selectionNodes) {
            if (
                this.selectionNodes.hasOwnProperty(nodesKey)
                && nodesKey !== dataKey
            ){
                this.selectionNodes[nodesKey].listNode.classList.remove(styles['active']);
            }
        }
    }

    addItemActions(dataKey){
        const items = this.selectionNodes[dataKey].listNode.children;
        if (!items.length){
            return;
        }
        for (let item of items) {
            if (dataKey === this.dataKeys.scheduleKey)
            {
                const times = item.querySelectorAll('span');
                times.length && times.forEach((time) => {
                    time.addEventListener('click', (e)=>{
                        e.stopPropagation();
                        this.selectionNodes[dataKey].listNode.classList.remove(styles['active']);
                        this.selectionNodes[dataKey].selectedNode.innerHTML = `
                            <span>
                                ${e.currentTarget.dataset.displayDate} - 
                                ${e.currentTarget.textContent}
                            </span>
                        `;

                        this.changeStep(dataKey, e.currentTarget);
                        this.activateBlocks();
                    })
                });
            }
            else{
                item.addEventListener('click', (e)=>{
                    e.stopPropagation();
                    this.selectionNodes[dataKey].listNode.classList.remove(styles['active']);
                    this.selectionNodes[dataKey].selectedNode.innerHTML = `<span>${e.currentTarget.textContent}</span>`;
                    this.changeStep(dataKey, e.currentTarget);
                    this.activateBlocks();
                })
            }
        }
    }

    changeStep(dataKey, target){
        this.selectionNodes[dataKey].inputNode.value = target.dataset.uid;
        switch (dataKey) {
            case this.dataKeys.clinicsKey:
                this.filledInputs[dataKey].clinicUid = target.dataset.uid;
                this.filledInputs[dataKey].clinicName = target.dataset.name;
                if (this.useServices)
                {
                    this.form.classList.add(styles['loading']);
                    this.getListNomenclature(`${target.dataset.uid}`)
                        .then((nomenclature) => {
                            if (nomenclature.data?.error){
                                throw new Error(nomenclature.data.error);
                            }else{
                                if (Object.keys(nomenclature.data).length > 0){
                                    this.data.services = nomenclature.data;
                                    this.bindServicesToSpecialties();
                                }
                            }
                            this.form.classList.remove(styles['loading'])
                        })
                        .catch(res => {
                            this.logResultErrors(res);
                        });
                }
                this.renderSpecialtiesList();
                break;
            case this.dataKeys.specialtiesKey:
                this.filledInputs[dataKey].specialty = target.textContent;
                this.filledInputs[dataKey].specialtyUid = target.dataset.uid;
                if(this.useServices){
                    if (this.selectDoctorBeforeService){
                        this.renderEmployeesList();
                    }else{
                        this.renderServicesList();
                    }
                }else{
                    this.renderEmployeesList();
                }
                break;
            case this.dataKeys.servicesKey:
                this.filledInputs[dataKey].serviceName = target.textContent;
                this.filledInputs[dataKey].serviceUid = target.dataset.uid;
                this.filledInputs[dataKey].serviceDuration = target.dataset.duration;
                this.selectDoctorBeforeService ? this.renderScheduleList(): this.renderEmployeesList();
                break;
            case this.dataKeys.employeesKey:
                this.filledInputs[dataKey].doctorName = target.textContent;
                this.filledInputs[dataKey].refUid = target.dataset.uid;
                if(this.useServices){
                    if (this.selectDoctorBeforeService){
                        this.renderServicesList();
                    }else{
                        this.renderScheduleList();
                    }
                }else{
                    this.renderScheduleList();
                }
                break;
            case this.dataKeys.scheduleKey:
                this.filledInputs[dataKey].orderDate = target.dataset.date;
                this.filledInputs[dataKey].timeBegin = target.dataset.start;
                this.filledInputs[dataKey].timeEnd = target.dataset.end;
                this.selectionNodes[dataKey].inputNode.value = target.dataset.date;
                break;
            default:
                break;
        }
        this.step = dataKey;
    }

    bindServicesToSpecialties() {
        const services  = this.data.services;
        const employees = this.data.employees;
        if(Object.keys(employees).length > 0)
        {
            for (const employeeUid in employees)
            {
                if (!employees.hasOwnProperty(employeeUid)) { return; }
                const empServices = employees[employeeUid].services;
                if(empServices && Object.keys(empServices).length > 0){
                    for (const empServiceUid in empServices)
                    {
                        if (!empServices.hasOwnProperty(empServiceUid)) { return; }

                        if (services.hasOwnProperty(empServiceUid)){
                            const specialty = employees[employeeUid]['specialty'];
                            if (specialty){
                                services[empServiceUid].specialtyUid = this.createIdFromName(specialty);
                            }
                        }
                    }
                }
            }
        }
    }

    activateBlocks(){
        let current = false;
        let next = false;
        for (const nodesKey in this.selectionNodes)
        {
            if (!this.useServices && nodesKey === this.dataKeys.servicesKey){
                continue;
            }

            if (this.selectionNodes.hasOwnProperty(nodesKey))
            {
                const block = this.selectionNodes[nodesKey].blockNode;
                if (!current && !next){
                    block.classList.remove(styles["hidden"])
                }
                else if (current && !next){
                    block.classList.remove(styles["hidden"])
                    this.resetValue(nodesKey);
                }
                else{
                    block.classList.add(styles["hidden"]);
                    this.resetValue(nodesKey);
                }
                next = current;
                if(nodesKey === this.step) {
                    current = true;
                }
            }
        }
    }

    resetValue(nodesKey: string) {
        this.selectionNodes[nodesKey].selectedNode.textContent = this.defaultText[nodesKey];
        this.selectionNodes[nodesKey].inputNode.value = "";
        if (this.filledInputs.hasOwnProperty(nodesKey)){
            for (const propKey in this.filledInputs[nodesKey]) {
                if (this.filledInputs[nodesKey].hasOwnProperty(propKey)){
                    this.filledInputs[nodesKey][propKey] = false;
                }
            }
        }
    }

    submit(event) {
        event.preventDefault();

        if (this.checkRequiredFields())
        {
            this.messageNode ? this.messageNode.textContent = "" : void(0);
            this.form.classList.add(styles['loading']);
            let orderData = {...this.filledInputs.textValues};

            for (let key in this.selectionNodes)
            {
                if (this.selectionNodes.hasOwnProperty(key) && this.filledInputs.hasOwnProperty(key))
                {
                    this.selectionNodes[key].inputNode.value = JSON.stringify(this.filledInputs[key]);
                    orderData = {...orderData, ...this.filledInputs[key]};
                }
            }

            if (this.useConfirmWith !== this.confirmTypes.none){
                this.sendConfirmCode(orderData);
            }
            else
            {
                this.sendOrder(orderData);
            }
        }
        else
        {
            if (this.messageNode){
                this.messageNode.textContent = BX.message("FIRSTBIT_JS_ORDER_CHECK_FIELDS_ERROR");
            }
            else {
                this.logResultErrors(BX.message("FIRSTBIT_JS_ORDER_CHECK_FIELDS_ERROR"));
            }
        }
    }

    sendConfirmCode (params) {
        this.messageNode.textContent = "";

        BX.ajax.runAction('firstbit:appointment.messageController.sendConfirmCode', {
            data: {
                phone: params.phone,
                email: params.email,
                sessid: BX.bitrix_sessid()
            }
        })
        .then(result => {
            this.timeExpires = result.data?.timeExpires ?? ((new Date()).getTime() / 1000).toFixed(0) + 60;
            this.createConfirmationForm(params);
        })
        .catch(result => {
            this.messageNode.textContent = result.errors?.[0]?.message + BX.message("FIRSTBIT_JS_SOME_DISPLAY_ERROR_POSTFIX");
            this.logResultErrors(result);
        });
    }

    createConfirmationForm (params){
        this.confirmWrapper && this.confirmWrapper.remove();
        this.confirmWrapper = this.renderer.getConfirmationBlock(params);
        this.form.classList.add(styles['appointment-form-confirmation-mode']);
        BX.insertBefore(this.confirmWrapper, this.submitBtn.closest('div'))
    }

    verifyConfirmCode (code, params, textNode, btnNode) {
        btnNode.classList.add(styles['loading']);

        BX.ajax.runAction('firstbit:appointment.messageController.verifyConfirmCode', {
            data: {
                code: code,
                email: params.email,
                sessid: BX.bitrix_sessid()
            }
        })
        .then(() => this.sendOrder(params))
        .catch(result => {
            btnNode.classList.remove(styles['loading']);
            if (result.errors?.length > 0){
                result.errors.forEach((error) => {
                    textNode.innerHTML = ((Number(error.code) === 400) || (Number(error.code) === 406) || (Number(error.code) === 425))
                        ? `${textNode.innerHTML}${error.message}<br>`
                        : BX.message("FIRSTBIT_JS_APPLICATION_ERROR");
                })
            }
        });
    }

    sendOrder(params) {
        BX.ajax.runAction('firstbit:appointment.oneCController.addOrder', {
            data: {
                params: JSON.stringify(params),
                sessid: BX.bitrix_sessid()
            }
        })
        .then((result) => {
            this.confirmWrapper && this.confirmWrapper.remove();
            this.form.classList.remove(styles['appointment-form-confirmation-mode'], styles['loading']);

            if (result.data?.error)
            {
                this.logResultErrors(result.data.error);
                this.finalizingWidget(false);
            }
            else
            {
                if (this.useEmailNote && params.email)
                {
                    this.sendEmailNote(params);
                }
                this.finalizingWidget(true);
            }
        })
        .catch(result => this.logResultErrors(result));
    }

    sendEmailNote(params) {
        BX.ajax.runAction('firstbit:appointment.messageController.sendEmailNote', {
            data: {
                params: JSON.stringify(params),
                sessid: BX.bitrix_sessid()
            }
        }).then().catch();
    }

    finalizingWidget(success) {
        this.resultBlock.classList.add(styles['active']);
        this.form.classList.add(styles['off']);

        const resTextNode = this.resultBlock.querySelector('p');
        if (resTextNode)
        {
            if (success)
            {
                const date = this.convertDateToDisplay(this.filledInputs[this.dataKeys.scheduleKey].timeBegin, false);
                const time = this.convertDateToDisplay(this.filledInputs[this.dataKeys.scheduleKey].timeBegin, true);
                const doctor = this.filledInputs[this.dataKeys.employeesKey].doctorName;
                resTextNode.innerHTML = `${BX.message("FIRSTBIT_JS_APPOINTMENT_SUCCESS")}
                                         <br>${date} ${time}
                                         <br>${BX.message("FIRSTBIT_JS_APPOINTMENT_DOCTOR")} - ${doctor}` ;
                resTextNode.classList.add(styles['success']);
                this.finalAnimations();
            }
            else
            {
                resTextNode.append(this.createFinalError());
                resTextNode.classList.add(styles['error']);
                setTimeout(()=>{
                    this.reload();
                }, 5000);
            }
        }
    }

    finalAnimations(){
        this.startBtn.classList.remove(styles['active']);
        this.startBtn.classList.add(styles['success']);
        setTimeout(()=>{
            this.reload();
        }, 4000);
    }

    reload(event = false){
        event && event.preventDefault();
        this.overlay.classList.remove(styles['active']);
        this.firstInit = false;
        this.loaded    = false;
        setTimeout(this.run.bind(this), 500);
    }

    createFinalError () {
        return BX.create('p', {
            children: [
                BX.create('span', {
                    html: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_START')
                }),
                BX.create('a', {
                    attrs: {
                        href: "#"
                    },
                    text: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_LINK'),
                    events: {
                        click: (e) => this.reload(e)
                    }
                }),
                BX.create('span', {
                    html: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_END')
                })
            ]
        });
    }

    checkRequiredFields(){
        let allNotEmpty = true;

        if (this.requiredInputs.length > 0){
            this.requiredInputs.some((input) => {
                if (!BX.type.isNotEmptyString(input.value))
                {
                    allNotEmpty = false;
                    input.parentElement?.classList.add(styles["error"])
                    return true;
                }
                else
                {
                    input.parentElement?.classList.remove(styles["error"]);
                }
            });
        }
        return allNotEmpty && this.phoneIsValid(this.textNodes.phone.inputNode);
    }

    phoneIsValid(phoneInput){
        const phone = phoneInput.value;
        let isValid = !( !phone || (phone.length !== this.phoneMask.length) );
        if (phoneInput.parentElement !== null){
            !isValid
                ? phoneInput.parentElement.classList.add(styles["error"])
                : phoneInput.parentElement.classList.remove(styles["error"]);
        }
        return isValid;
    }

    /**
     * add input mask to all inputs with type=tel
     */
    addPhoneMasks(){
        const maskedInputs = this.overlay.querySelectorAll('input[type="tel"]');
        const that = this;
        maskedInputs.length && maskedInputs.forEach((input: HTMLInputElement) => {
            input.addEventListener('input', (e) => {
                that.maskInput(e.currentTarget, this.phoneMask);
            });
        });
    }

    /**
     * add BX.calendar extension to select birthday date on related input
     */
    addCalendarSelection(){
        const that = this;
        const birthdayInput = this.overlay.querySelector('input[name="birthday"]');
        birthdayInput.addEventListener('keydown', (e) => {
            e.preventDefault();
            return false;
        });
        birthdayInput.addEventListener('click', () => {
            BX.calendar({
                node: birthdayInput,
                field: birthdayInput,
                bTime: false,
                callback_after: function(date){
                    const timestamp = (new Date(date)).getTime();
                    that.filledInputs.textValues.birthday = that.convertDateToISO(timestamp);
                }
            });
        });
    }

    /**
     * inject styles with custom color variables from module settings
     */
    addCustomColors(){
        if (Object.keys(this.customColors).length > 0)
        {
            const style = BX.create('style');
            style.textContent = `.${styles['appointment-popup-overlay']}, .${styles['appointment-button-wrapper']}{`
            for (let key in this.customColors){
                if (this.customColors.hasOwnProperty(key))
                {
                    switch (key) {
                        case "--appointment-main-color":
                            const hslM = convertHexToHsl(this.customColors[key]);
                            if (hslM){
                                style.textContent += `--main-h: ${hslM.h};--main-s: ${hslM.s};--main-l: ${hslM.l};`;
                            }
                            break;
                        case "--appointment-field-color":
                            const hslF = convertHexToHsl(this.customColors[key]);
                            if (hslF){
                                style.textContent += `-field-h: ${hslF.h};--field-s: ${hslF.s};--field-l: ${hslF.l};`;
                            }
                            break;
                        default:
                            style.textContent += `${key}: ${this.customColors[key]};`;
                            break;
                    }
                }
            }
            style.textContent = style.textContent + `}`;
            this.overlay.after(style);
        }
    }

    /**
     * show/hide popup with appointment form and starts loading data from 1c on first showing
     */
    togglePopup() {
        this.overlay.classList.toggle(styles['active']);
        this.useCustomMainBtn ? this.startBtn.classList.toggle('active')
            : this.startBtn.classList.toggle(styles['active']);
        if (!this.loaded){
            this.start();
        }
    }

    /**
     * toggle load animation on form
     * @param on
     */
    toggleLoader(on = true){
        on  ? this.form.classList.add(styles['loading'])
            : this.form.classList.remove(styles['loading'])
    }

    /**
     * add phone mask
     * @param input
     * @param mask
     */
    maskInput(input, mask){
        const value = input.value;
        const literalPattern = /[0]/;
        const numberPattern = /[0-9]/;

        let newValue = "";

        let valueIndex = 0;

        for (let i = 0; i < mask.length; i++) {
                if (i >= value.length) break;
                if (mask[i] === "0" && !numberPattern.test(value[valueIndex])) break;
                while (!literalPattern.test(mask[i])) {
                if (value[valueIndex] === mask[i]) break;
                newValue += mask[i++];
            }
            newValue += value[valueIndex++];
        }

        input.value = newValue;
    }

    /**
     * convert date to ISO format without seconds
     * @param timestamp
     * @returns {string}
     */
    convertDateToISO (timestamp) {
        const date = this.readDateInfo(timestamp);

        return `${date.year}-${date.month}-${date.day}T${date.hours}:${date.minutes}:00`;
    }

    /**
     * convert date to format "d-m-Y" / "d.m.Y" / "H:i"
     * @param timestamp
     * @param onlyTime
     * @param onlyDate
     * @returns {string}
     */
    convertDateToDisplay (timestamp, onlyTime = false, onlyDate = false) {
        const date = this.readDateInfo(timestamp);

        if (onlyTime){
            return `${date.hours}:${date.minutes}`;
        }
        if (onlyDate){
            return `${date.day}.${date.month}.${date.year}`;
        }
        return `${date.day}-${date.month}-${date.year}`;
    }

    /**
     * convert param to object with detail info about date
     * @param timestampOrISO
     * @returns {{hours: string, seconds: string, month: string, year: number, minutes: string, weekDay, day: string}}
     */
    readDateInfo(timestampOrISO){
        const date = new Date(timestampOrISO);

        let day = `${date.getDate()}`;
        if (Number(day)<10) {
            day = `0${day}`;
        }

        let month = `${date.getMonth()+1}`;
        if (Number(month)<10) {
            month = `0${month}`;
        }

        let hours = `${date.getHours()}`;
        if (Number(hours)<10) {
            hours = `0${hours}`;
        }

        let minutes = `${date.getMinutes()}`;
        if (Number(minutes)<10) {
            minutes = `0${minutes}`;
        }

        let seconds = `${date.getSeconds()}`;
        if (Number(seconds)<10) {
            seconds = `0${seconds}`;
        }

        return {
            "day": day,
            "month": month,
            "year": date.getFullYear(),
            "hours": hours,
            "minutes": minutes,
            "seconds": seconds,
            "weekDay": this.ucFirst(date.toLocaleString('ru', {weekday: 'short'}))
        }
    }

    /**
     * make unique code from string
     * @param str
     * @returns {*}
     */
    createIdFromName(str) {
        return window.btoa(window.unescape(encodeURIComponent(str)));
    }

    /**
     * make the first letter of a string uppercase
     * @param str
     * @returns {string|*}
     */
    ucFirst(str) {
        if (!str) return str;

        return str[0].toUpperCase() + str.slice(1);
    }

    /**
     * error logging
     * @param res
     */
    logResultErrors(res) {
        if (res.errors && Array.isArray(res.errors) && res.errors.length > 0)
        {
            res.errors.forEach(error => {
                console.log(`${BX.message("FIRSTBIT_JS_APPLICATION_ERROR")} - ${error.message}`)
            })
        }
        else
        {
            console.log(BX.message("FIRSTBIT_JS_APPLICATION_ERROR") + "\r\n", res.message ?? res);
        }
    }

    /**
     * init elements selectors
     */
    getAppSelectors(stylesObject)
    {
        return {
            rootNodeId:         'firstbit-appointment-application-root',
            overlayId:          'appointment-popup-steps-overlay',
            startBtnWrapId:     stylesObject['appointment-button-wrapper'],
            startBtnId:         stylesObject['appointment-button'],
            formId:             stylesObject['appointment-form'],
            mobileCloseBtnId:   stylesObject['appointment-form-close'],
            messageNodeId:      stylesObject['appointment-form-message'],
            submitBtnId:        stylesObject['appointment-form-button'],
            appResultBlockId:   stylesObject['appointment-result-block'],
            inputClass:         stylesObject['appointment-form_input'],
            textareaClass:      stylesObject['appointment-form_textarea'],
            confirmWrapperId:   stylesObject['appointment-form-confirmation-wrapper']
        }
    }
}