// @disabled-flow
'use strict';
import styles from "../../styles/app.scss";
import {convertHexToHsl, maskInput} from "../utils/functions";
import "date";
import {Event} from 'main.core';
import {EventManager} from "../utils/eventManager";
import {Renderer} from "../utils/renderer";
import type {ITextObject} from "../../types/params";
import {TextInputNames} from "../../types/params";

export class AppointmentSteps
{
    selectionStep: string         = '';
    currentFormStep: HTMLElement  = null;
    formStepNodes: any            = {one: null, two: null, userData: null}
    phoneMask: string             = '+7(000)000-00-00';
    loaded: boolean               = false;
    timeExpires: number           = 0;
    requiredInputs: HTMLElement[] = [];
    initParams: any               = {};
    eventHandlersAdded            = {};
    servicesStorage               = {};
    dataKeys = {
        clinicsKey:     "clinics",
        specialtiesKey: "specialties",
        employeesKey:   "employees",
        servicesKey:    "services",
        scheduleKey:    "schedule",
    };
    data = {
        [this.dataKeys.clinicsKey]:     [],
        [this.dataKeys.specialtiesKey]: {},
        [this.dataKeys.servicesKey]:    {},
        [this.dataKeys.employeesKey]:   {},
        [this.dataKeys.scheduleKey]:    []
    };
    orderData       = {};
    selectionBlocks = {};
    selectionNodes  = {};
    textNodes       = {};
    defaultText     = {};

    selectDoctorBeforeService = true;

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
        this.useTimeSteps 					= (params.useTimeSteps === "Y");
        this.timeStepDurationMinutes		= Number(params.timeStepDurationMinutes);
        this.strictCheckingOfRelations		= (params.strictCheckingOfRelations === "Y");
        this.showDoctorsWithoutDepartment	= (params.showDoctorsWithoutDepartment === "Y");
        this.confirmTypes                   = params.confirmTypes;
        this.useConfirmWith                 = (params.useConfirmWith);
        this.useEmailNote                   = (params.useEmailNote === "Y");

        this.companyLogo      = params.companyLogo ?? false;
        this.useCustomMainBtn = (params.useCustomMainBtn === "Y") && params['customMainBtnId'];
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
                "blockId":      `appointment_${step}_block`,
                "listId":       `appointment_${step}_list`,
                "selectedId":   `appointment_${step}_selected`,
                "inputId":      `appointment_${step}_value`,
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
            this.root = this.renderer.getRootElement();
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
            this.initFormStepNodes();
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
    initCustomEvents()
    {
        EventManager.subscribe(EventManager.fullDataLoaded, () => {
            this.loaded = true;
            try{
                this.renderBlock(this.dataKeys.clinicsKey);
            }
            catch(e){
                this.logResultErrors(e);
            }
        });

        EventManager.subscribe(EventManager.clinicsRendered, () => {
            this.createSpecialtiesList();
            this.toggleLoader(false);
        });

        EventManager.subscribe(EventManager.formStepChanged, (e) => {
            e.data.isBack ? (this.selectionStep = this.dataKeys.specialtiesKey) : void(0);
            this.changeFormStepActions(e.data);
            this.activateSelectionNodes();
        });
    }

    initFormStepNodes(){
        this.currentFormStep = this.formStepNodes.one   = BX(this.selectors.formStepIds.one);
        this.formStepNodes.two   = BX(this.selectors.formStepIds.two);
        this.formStepNodes.userData = BX(this.selectors.formStepIds.userData);
    }

    /**
     * find or create start button and add event handler for click
     */
    initStartBtn() {
        if(!this.firstInit && this.useCustomMainBtn){
            return;
        }
        const startBtnId = this.useCustomMainBtn ? this.initParams['customMainBtnId'] : this.selectors.startBtnId;
        this.startBtn = BX(startBtnId);
        if (BX.type.isDomNode(this.startBtn))
        {
            EventManager.bind(this.startBtn, 'click', this.togglePopup.bind(this));
        }
        else
        {
            throw new Error(`${BX.message('FIRSTBIT_JS_NODE_NOT_FOUND')} "${this.initParams['customMainBtnId']}"`)
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

    /**
     * find nodes, add actions and save their data to this object
     */
    initTextNodes() {
        this.initParams['textBlocks'].forEach((block: ITextObject) => {
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
        this.loadData();
    }

    /**
     * sequentially loads data from 1c
     */
    loadData(): void
    {
        this.getListClinic()
            .then(clinicsResponse => {
                if (clinicsResponse.data?.error) {
                    throw new Error(clinicsResponse.data?.error);
                }
                else if(clinicsResponse.data?.length === 0) {
                    throw new Error(BX.message("FIRSTBIT_JS_CLINICS_NOT_FOUND_ERROR"));
                }
                else {
                    this.data.clinics = clinicsResponse.data;
                    return this.getListEmployees();
                }
            })
            .then(employeesResponse => {
                if (employeesResponse.data?.error) {
                    throw new Error(employeesResponse.data?.error);
                }
                else if(Object.keys(employeesResponse.data).length === 0) {
                    throw new Error(BX.message("FIRSTBIT_JS_DOCTORS_NOT_FOUND_ERROR"));
                }
                else {
                    this.data.employees = employeesResponse.data;
                    return this.getSchedule();
                }
            })
            .then(scheduleResponse => {
                if (scheduleResponse.data?.error) {
                    throw new Error(scheduleResponse.data?.error);
                }
                if (scheduleResponse.data?.hasOwnProperty("schedule"))
                {
                    this.data.schedule = scheduleResponse.data.schedule;
                    EventManager.emit(EventManager.fullDataLoaded);
                }
            })
            .catch(e => {
                !this.useCustomMainBtn && this.startBtnWrap.classList.add(styles['hidden']);
                this.logResultErrors(e);
            })
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

    renderSpecialtiesList(){
        this.renderBlock(this.dataKeys.specialtiesKey);
    }

    renderServicesList(){
        this.renderBlock(this.dataKeys.servicesKey);
    }

    renderEmployeesList() {
        this.renderBlock(this.dataKeys.employeesKey);
    }

    renderScheduleList() {
        this.renderBlock(this.dataKeys.scheduleKey);
    }

    renderBlock(dataKey)
    {
        const listNode = this.selectionNodes[dataKey]?.listNode;
        if(!listNode){
            throw new Error(BX.message(`FIRSTBIT_JS_${dataKey.toUpperCase()}_NODE_NOT_FOUND_ERROR`));
        }
        (dataKey === this.dataKeys.scheduleKey) ? listNode.classList.add(styles["column-mode"]) : void(0);
        BX.cleanNode(listNode);

        if(Object.keys(this.data[dataKey]).length > 0)
        {
            const items = this.data[dataKey];
            this.renderer.renderSelectionItems(listNode, dataKey, items);
            (dataKey === this.dataKeys.clinicsKey) ? EventManager.emit(EventManager.clinicsRendered) : void(0);
        }
    }

    allowToRender(listNode: HTMLElement, dataKey: string, item: any): boolean
    {
        let canRender = true;
        const selectedClinic        = this.filledInputs[this.dataKeys.clinicsKey].clinicUid;
        const selectedSpecialtyUid  = this.filledInputs[this.dataKeys.specialtiesKey].specialtyUid;
        const selectedEmployeeUid   = this.filledInputs[this.dataKeys.employeesKey].refUid;
        const selectedServiceUid    = this.filledInputs[this.dataKeys.servicesKey].serviceUid;
        let clinicCondition, specialtyCondition;

        switch (dataKey)
        {
            case this.dataKeys.specialtiesKey:
                const alreadyRendered = listNode.querySelector(`[data-uid="${item.uid}"]`);
                clinicCondition = item.clinics.includes(selectedClinic);
                if(this.strictCheckingOfRelations){
                    canRender = clinicCondition;
                    if (this.showDoctorsWithoutDepartment){
                        canRender = (clinicCondition || item.clinics.includes(''));
                    }
                }
                canRender = canRender && BX.type.isNotEmptyString(item.name) && !alreadyRendered;
                break;

            case this.dataKeys.employeesKey:
                specialtyCondition = (item.specialtyUid === selectedSpecialtyUid);
                clinicCondition    = (selectedClinic === item.clinicUid);

                canRender = specialtyCondition;

                if(this.strictCheckingOfRelations){
                    if (this.showDoctorsWithoutDepartment){
                        canRender = (specialtyCondition && !item.clinicUid)
                                    ||
                                    (specialtyCondition && clinicCondition);
                    }
                    else
                    {
                        canRender = specialtyCondition && clinicCondition;
                    }
                }
                if (this.useServices && !this.selectDoctorBeforeService)
                {
                    canRender = canRender && item.services.hasOwnProperty(selectedServiceUid);
                }
                break;

            case this.dataKeys.servicesKey:
                canRender = (selectedSpecialtyUid === item.specialtyUid);
                if (this.selectDoctorBeforeService)
                {
                    canRender = canRender && this.data.employees[selectedEmployeeUid].services.hasOwnProperty(item.uid);
                }
                break;

            case this.dataKeys.scheduleKey:
                canRender = (item.clinicUid === selectedClinic) && (item.refUid === selectedEmployeeUid)
                break;
            default:
                break;

        }
        return canRender;
    }

    getServiceDuration(scheduleItem):number {
        const selectedEmployee = this.data.employees[scheduleItem.refUid];
        const selectedService = this.filledInputs[this.dataKeys.servicesKey];
        let serviceDuration = Number(selectedService.serviceDuration);
        if(selectedEmployee.services.hasOwnProperty(selectedService.serviceUid))
        {
            if (selectedEmployee.services[selectedService.serviceUid].hasOwnProperty("personalDuration")){
                const personalDuration = selectedEmployee.services[selectedService.serviceUid]["personalDuration"];
                serviceDuration = Number(personalDuration) > 0 ? Number(personalDuration) : serviceDuration;
            }
        }
        return serviceDuration;
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

    toggleSelectionList(dataKey: string, selected: HTMLElement, list: HTMLElement) {
        list.classList.toggle(styles['active']);
        for (const nodesKey in this.selectionNodes) {
            if (
                this.selectionNodes.hasOwnProperty(nodesKey)
                && nodesKey !== dataKey
            ){
                this.selectionNodes[nodesKey]?.listNode?.classList.remove(styles['active']);
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

                        this.changeSelectionStep(dataKey, e.currentTarget);
                        this.activateSelectionNodes();
                    })
                });
            }
            else{
                item.addEventListener('click', (e)=>{
                    e.stopPropagation();
                    this.selectionNodes[dataKey].listNode.classList.remove(styles['active']);
                    this.selectionNodes[dataKey].selectedNode.innerHTML = `<span>${e.currentTarget.textContent}</span>`;
                    this.changeSelectionStep(dataKey, e.currentTarget);
                    if(dataKey !== this.dataKeys.specialtiesKey)
                    {
                        this.activateSelectionNodes();
                    }
                    else
                    {
                        this.activateStepButtons();
                    }
                })
            }
        }
    }

    changeSelectionStep(dataKey, target){
        this.selectionNodes[dataKey].inputNode.value = target.dataset.uid;
        switch (dataKey) {
            case this.dataKeys.clinicsKey:
                const clinicUid = target.dataset.uid;
                this.filledInputs[dataKey].clinicUid = clinicUid;
                this.filledInputs[dataKey].clinicName = target.dataset.name;
                if (this.useServices)
                {
                    if(this.servicesStorage[clinicUid])
                    {
                        this.data.services = {...this.servicesStorage[clinicUid]};
                        this.renderSpecialtiesList();
                    }
                    else
                    {
                        this.toggleLoader(true);
                        this.getListNomenclature(`${clinicUid}`)
                            .then((nomenclature) => {
                                if (nomenclature.data?.error){
                                    throw new Error(nomenclature.data.error);
                                }else{
                                    if (Object.keys(nomenclature.data).length > 0){
                                        this.data.services = nomenclature.data;
                                        this.bindServicesToSpecialties();
                                        this.renderSpecialtiesList();
                                        this.servicesStorage[clinicUid] = {...this.data.services}
                                    }
                                }
                                this.toggleLoader(false);
                            })
                            .catch(res => {
                                this.logResultErrors(res);
                            });
                    }
                }
                else
                {
                    this.renderSpecialtiesList();
                }
                break;
            case this.dataKeys.specialtiesKey:
                this.filledInputs[dataKey].specialty = target.dataset.name;
                this.filledInputs[dataKey].specialtyUid = target.dataset.uid;
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
        this.selectionStep = dataKey;
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
                const specialty   = employees[employeeUid].specialty;

                if(specialty){
                    if(empServices && Object.keys(empServices).length > 0) {
                        for (const empServiceUid in empServices)
                        {
                            if (!empServices.hasOwnProperty(empServiceUid)) { return; }

                            if (services.hasOwnProperty(empServiceUid)){
                                services[empServiceUid].specialtyUid = employees[employeeUid].specialtyUid;
                            }
                        }
                    }
                }
            }
        }
    }

    createSpecialtiesList(){
        const employees = this.data.employees;
        if(Object.keys(employees).length > 0)
        {
            for (const uid in employees)
            {
                if (employees.hasOwnProperty(uid))
                {
                    const specialty = employees[uid].specialty;
                    specialty && this.addSpecialty(employees[uid]);
                }
            }
        }
    }
    
    addSpecialty(employee)
    {
        if(this.data[this.dataKeys.specialtiesKey][employee.specialtyUid])
        {
            this.addClinicToSpecialty(this.data[this.dataKeys.specialtiesKey][employee.specialtyUid], employee.clinicUid);
        }
        else
        {
            this.data[this.dataKeys.specialtiesKey][employee.specialtyUid] = {
                uid:        employee.specialtyUid,
                name:       employee.specialty,
                clinics:    [employee.clinicUid]
            }
        }
    }

    addClinicToSpecialty(specialty, clinicUid){
        if(Array.isArray(specialty.clinics) && !specialty.clinics.includes(clinicUid))
        {
            specialty.clinics.push(clinicUid);
        }
    }

    activateSelectionNodes(){
        let current = false;
        let next = false;
        this.selectionSteps.forEach(nodesKey => {
            if (!this.useServices && nodesKey === this.dataKeys.servicesKey){
                return;
            }
            console.log(nodesKey,current,next)
            if (this.selectionNodes.hasOwnProperty(nodesKey))
            {
                const block = this.selectionNodes[nodesKey].blockNode;
                if (!current && !next){
                    block.classList.remove(styles["disabled"]);
                }
                else if (current && !next){
                    block.classList.remove(styles["disabled"])
                    this.resetValue(nodesKey);
                }
                else{
                    block.classList.add(styles["disabled"]);
                    this.resetValue(nodesKey);
                }
                next = current;

                if(nodesKey === this.selectionStep)
                {
                    current = true;

                    const selectedSpecialty = this.filledInputs[this.dataKeys.specialtiesKey].specialtyUid;
                    const selectedDate      = this.filledInputs[this.dataKeys.scheduleKey].orderDate;

                    const specialtyCondition = (nodesKey === this.dataKeys.specialtiesKey) && selectedSpecialty;
                    const dateCondition      = (nodesKey === this.dataKeys.scheduleKey) && selectedDate;
                    if( ((this.currentFormStep === this.formStepNodes.one) && specialtyCondition)
                        || ((this.currentFormStep === this.formStepNodes.two) && dateCondition)
                    ){
                        this.activateStepButtons();
                    }
                    else{
                        this.deactivateStepButtons();
                    }
                }
            }
        })
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

    setSelectionDoctorBeforeService(value: boolean){
        if(this.filledInputs[this.dataKeys.specialtiesKey].specialty !== false)
        {
            this.resetValue(this.dataKeys.employeesKey);

            this.selectDoctorBeforeService = value;
            if(this.useServices)
            {
                this.resetValue(this.dataKeys.servicesKey);

                if (value === true){
                    BX.insertBefore(
                        this.selectionNodes[this.dataKeys.employeesKey].blockNode,
                        this.selectionNodes[this.dataKeys.servicesKey].blockNode
                    );
                    this.selectionSteps[3] = this.dataKeys.servicesKey;
                    this.selectionSteps[2] = this.dataKeys.employeesKey;
                    this.renderEmployeesList();
                }else{
                    BX.insertBefore(
                        this.selectionNodes[this.dataKeys.servicesKey].blockNode,
                        this.selectionNodes[this.dataKeys.employeesKey].blockNode
                    );
                    this.selectionSteps[2] = this.dataKeys.servicesKey;
                    this.selectionSteps[3] = this.dataKeys.employeesKey;
                    this.renderServicesList();
                }
            }
            else{
                this.renderEmployeesList();
            }
            EventManager.emit(EventManager.formStepChanged, new Event.BaseEvent({
                data: {
                    previousStep: this.formStepNodes.one,
                    newStep: this.formStepNodes.two,
                },
            }));
        }
        else
        {
            this.checkRequiredFields();
        }
    }

    changeFormStep(nextStep: HTMLElement, isBack: boolean = false) {
        EventManager.emit(EventManager.formStepChanged, new Event.BaseEvent({
            data: {
                previousStep: this.currentFormStep,
                newStep: nextStep,
                isBack: isBack
            },
        }));
    }

    changeFormStepActions(data)
    {
        if(BX.type.isDomNode(data?.newStep)){
            this.currentFormStep = data.newStep;
            data.newStep.classList.remove(styles['hidden']);
        }
        if(BX.type.isDomNode(data?.previousStep)){
            data.previousStep.classList.add(styles['hidden']);
        }
    }

    activateStepButtons(){
        if(BX.type.isDomNode(this.currentFormStep)){
            const buttons = this.currentFormStep.querySelectorAll(
                `.${styles['appointment-form-button']}:not([data-readonly="Y"])`
            );
            buttons.length && buttons.forEach(button => button.removeAttribute('disabled'))
        }
    }

    deactivateStepButtons(){
        if(BX.type.isDomNode(this.currentFormStep)){
            const buttons = this.currentFormStep.querySelectorAll(
                `.${styles['appointment-form-button']}:not([data-readonly="Y"])`
            );
            buttons.length && buttons.forEach(button => button.setAttribute('disabled', true))
        }
    }

    submit(event) {
        event.preventDefault();

        if (this.checkRequiredFields())
        {
            this.messageNode ? this.messageNode.textContent = "" : void(0);
            this.toggleLoader(true);
            this.orderData = {...this.filledInputs.textValues};

            for (let key in this.selectionNodes)
            {
                if(!this.useServices && (key === this.dataKeys.servicesKey)) {
                    continue;
                }

                if (this.selectionNodes.hasOwnProperty(key) && this.filledInputs.hasOwnProperty(key))
                {
                    this.selectionNodes[key].inputNode.value = JSON.stringify(this.filledInputs[key]);
                    this.orderData = {...this.orderData, ...this.filledInputs[key]};
                }
            }

            if (this.useConfirmWith !== this.confirmTypes.none){
                this.sendConfirmCode();
            }
            else
            {
                this.sendOrder();
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

    sendConfirmCode (event = false) {
        event && event.preventDefault();

        this.messageNode.textContent = "";

        BX.ajax.runAction('firstbit:appointment.messageController.sendConfirmCode', {
            data: {
                phone: this.orderData.phone,
                email: this.orderData.email,
                sessid: BX.bitrix_sessid()
            }
        })
        .then(result => {
            this.timeExpires = result.data?.timeExpires ?? ((new Date()).getTime() / 1000).toFixed(0) + 60;
            this.createConfirmationForm();
            this.toggleLoader(false);
        })
        .catch(result => {
            this.messageNode.textContent = result.errors?.[0]?.message + BX.message("FIRSTBIT_JS_SOME_DISPLAY_ERROR_POSTFIX");
            this.logResultErrors(result);
            this.toggleLoader(false);
        });
    }

    createConfirmationForm (){
        this.confirmWrapper && this.confirmWrapper.remove();
        this.confirmWrapper = this.renderer.getConfirmationBlock();
        this.form.classList.add(styles['appointment-form-confirmation-mode']);
        BX.append(this.confirmWrapper, this.form);
    }

    verifyConfirmCode (code, confirmWarningNode, btnNode)
    {
        if (confirmWarningNode && btnNode){
            confirmWarningNode.textContent = '';
            if (code?.length === 4)
            {
                btnNode.classList.add(styles['loading']);

                BX.ajax.runAction('firstbit:appointment.messageController.verifyConfirmCode', {
                    data: {
                        code: code,
                        email: this.orderData.email,
                        sessid: BX.bitrix_sessid()
                    }
                })
                .then(() => this.sendOrder())
                .catch(result => {
                    btnNode.classList.remove(styles['loading']);
                    if (result.errors?.length > 0){
                        result.errors.forEach((error) => {
                            confirmWarningNode.innerHTML = ((Number(error.code) === 400) || (Number(error.code) === 406) || (Number(error.code) === 425))
                                    ? `${confirmWarningNode.innerHTML}${error.message}<br>`
                                    : BX.message("FIRSTBIT_JS_APPLICATION_ERROR");
                        })
                    }
                });
            }
            else
            {
                confirmWarningNode.textContent = BX.message("FIRSTBIT_JS_CONFIRM_CODE_LENGTH");
            }
        }
    }

    sendOrder() {
        BX.ajax.runAction('firstbit:appointment.oneCController.addOrder', {
            data: {
                params: JSON.stringify(this.orderData),
                sessid: BX.bitrix_sessid()
            }
        })
        .then((result) => {
            this.destroyConfirmationForm();
            this.toggleLoader(false);

            if (result.data?.error)
            {
                throw new Error(result.data.error);
            }
            else
            {
                if (this.useEmailNote && this.orderData.email)
                {
                    this.sendEmailNote();
                }
                this.finalizingWidget(true);
            }
        })
        .catch(result => {
            this.destroyConfirmationForm();
            this.toggleLoader(false);
            this.logResultErrors(result);
            this.finalizingWidget(false);
        });
    }

    sendEmailNote() {
        BX.ajax.runAction('firstbit:appointment.messageController.sendEmailNote', {
            data: {
                params: JSON.stringify(this.orderData),
                sessid: BX.bitrix_sessid()
            }
        }).then().catch();
    }

    startCodeTimerActions(confirmRepeatBtn: HTMLElement){
        const curTimeSeconds: number = Number(((new Date()).getTime() / 1000).toFixed(0));
        let remainingTime = this.timeExpires - curTimeSeconds;
        const interval = setInterval(() => {
            if (remainingTime <= 0)
            {
                EventManager.bind(confirmRepeatBtn, 'click', this.sendConfirmCode.bind(this));
                clearInterval(interval);
            }
            else
            {
                remainingTime--;
                confirmRepeatBtn.textContent = `${BX.message("FIRSTBIT_JS_CONFIRM_CODE_SEND_AGAIN")} 
                                                ${remainingTime > 0 ? remainingTime : ''}`;
            }
        }, 1000);
    }

    destroyConfirmationForm(){
        this.confirmWrapper && this.confirmWrapper.remove();
        this.form.classList.remove(styles['appointment-form-confirmation-mode']);
    }

    finalizingWidget(success) {
        this.resultBlock.classList.add(styles['active']);
        this.form.classList.add(styles['off']);

        const resTextNode = this.resultBlock.querySelector('p');
        if (resTextNode)
        {
            if (success)
            {
                const date = this.convertDateToDisplay(this.orderData.timeBegin, false);
                const time = this.convertDateToDisplay(this.orderData.timeBegin, true);
                const doctor = this.orderData.doctorName;
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
        }, 5000);
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
        maskedInputs.length && maskedInputs.forEach((input: HTMLInputElement) => {
            input.addEventListener('input', (e) => {
                maskInput(e.currentTarget, this.phoneMask);
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
     * @param stylesObject
     * @returns object
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
            confirmWrapperId:   stylesObject['appointment-form-confirmation-wrapper'],
            formStepIds: {
                one:        'appointment-form-step-one',
                two:        'appointment-form-step-two',
                userData:   'appointment-form-step-userData',
            }
        }
    }
}