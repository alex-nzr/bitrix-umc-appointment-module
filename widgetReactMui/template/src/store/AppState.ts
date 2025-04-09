import {makeAutoObservable} from "mobx";
import {EAppFormTypes, ETextFields, ISelectedParams, IService} from "../types/selectedData";
import dataState from "./OneCDataState";

class AppState {
    constructor() {
        makeAutoObservable(this,{
            checkTextFields: false,
            isServicesAvailable: false
        });
        if (process.env.NODE_ENV === "development"){
            this.DEMO_MODE = true;
        }
    }

    private readonly DEMO_MODE:                     boolean = false;
    private readonly privacyLink:                   string  = "/download/politika-obrabotki-personalnyh-dannyh.pdf";
    private readonly useTimeSteps:                  boolean = false;
    private readonly timeStepDurationMinutes:       number	= 15;
    private readonly strictCheckingOfRelations:     boolean	= true;
    private readonly showDoctorsWithoutDepartment:  boolean = true;
    private readonly useMultipleServices:           boolean = false;

    private customButtonsAdded: boolean = false;
    private lastOpenedForm: null|EAppFormTypes = null;

    private loading                     = true;
    private needToLoad                  = true;
    private appOpen                     = false;
    private canRender                   = true;
    private selectDoctorBeforeService 	= true;
    private activeStepNumber            = 0;

    private commonBtnClassName = 'appointment-first-btn';
    private employeeBtnClassName = 'appointment-second-btn';
    private serviceBtnClassName = 'appointment-third-btn';

    private selectedValues: ISelectedParams = {
        clinic: {
            uid: '',
            name: '',
        },
        specialty: {
            uid: '',
            name: '',
        },
        services: [],
        employee: {
            uid: '',
            name: '',
        },
        dateTime: {
            date: '',
            timeBegin: '',
            timeEnd: '',
            formattedDate: '',
            formattedTimeBegin: '',
            formattedTimeEnd: '',
        },
        textFields: {
            name: '',
            middleName: '',
            surname: '',
            phone: '',
            email: '',
            comment: ''
        }
    }

    private validTextFields:{[key in ETextFields]:boolean} = {
        name: false,
        middleName: false,
        surname: false,
        phone: false,
        email: true,
        comment: true
    }

    private result: boolean = false;

    get commonBtnClass() {
        return this.commonBtnClassName;
    }

    get employeeBtnClass() {
        return this.employeeBtnClassName;
    }

    get serviceBtnClass() {
        return this.serviceBtnClassName;
    }

    get isLoading() {
        return this.loading;
    }
    set isLoading(value: boolean){
        this.loading = value;
    }

    get isCustomButtonsAdded(){
        return this.customButtonsAdded;
    }
    set isCustomButtonsAdded(value: boolean){
        this.customButtonsAdded = value;
    }

    get isNeedToLoad() {
        return this.needToLoad;
    }
    set isNeedToLoad(value: boolean){
        this.needToLoad = value;
    }

    get isAppOpen() {
        return this.appOpen;
    }
    set isAppOpen(value: boolean){
        this.appOpen = value;
    }

    get isCanRender() {
        return this.canRender;
    }
    set isCanRender(value: boolean){
        this.canRender = value;
    }

    get activeStep(){
        return this.activeStepNumber;
    }
    set activeStep(value: number){
        this.activeStepNumber = value;
    }
    stepNext = () => {
        this.activeStepNumber++;
    }
    stepBack = () => {
        this.activeStepNumber--;
    }

    get lastOpenedFormType(){
        return this.lastOpenedForm;
    }
    set lastOpenedFormType(value : EAppFormTypes|null)
    {
        this.lastOpenedForm = value;
    }

    toggleAppointmentForm(open: boolean, employeeUid: string = '', serviceUid: string = '')
    {
        //При первом открытии проставляются дефолтные значения
        if (this.lastOpenedFormType === null)
        {
            this.resetAppState();
        }

        //Если открывается форма записи к конкретному доктору или на конкретную услугу
        if(employeeUid || serviceUid)
        {
            let needReset = true;
            if (serviceUid)
            {
                employeeUid = dataState.getFirstEmployeeUidByServiceUid(serviceUid);
                this.selectDoctorBeforeService = false;
            }
            else
            {
                this.selectDoctorBeforeService = true;
            }

            if (employeeUid)
            {
                const clinicUid = dataState.getClinicUidByEmployeeUid(employeeUid);
                const specialtyUid = dataState.getEmployeeFirstSpecialtyUid(employeeUid);
                if (clinicUid && specialtyUid)
                {
                    this.selected = {
                        clinic: {
                            name: dataState.getNameByUid('clinics', clinicUid),
                            uid: clinicUid
                        },
                        specialty: {
                            name: dataState.getNameByUid('specialties', specialtyUid),
                            uid: specialtyUid
                        },
                        employee: {
                            name: dataState.getNameByUid('employees', employeeUid),
                            uid: employeeUid
                        },
                        services: serviceUid ? [{
                            uid: serviceUid,
                            name: dataState.getNameByUid('services', serviceUid),
                            duration: dataState.getServiceDurationByUid(serviceUid)
                        }] : [],
                        dateTime: {
                            date: '',
                            timeBegin: '',
                            timeEnd: '',
                            formattedDate: '',
                            formattedTimeBegin: '',
                            formattedTimeEnd: '',
                        }
                    };

                    this.lastOpenedFormType = EAppFormTypes.personal;
                    this.activeStep = 1;
                    needReset = false;
                }
            }

            //Если не удалось найти все связи по выбранному доктору или услуге, то сброс выбранных данных на дефолтные
            if(needReset)
            {
                this.resetAppState();
            }
        }
        //Если
        else
        {
            //Если общая форма открывается после того, как были открыты формы выбора доктора или услуги, то сброс выбранных данных на дефолтные
            if (open && (this.lastOpenedFormType !== EAppFormTypes.common))
            {
                this.resetAppState();
            }
        }

        if (!open && this.activeStep === 3)
        {
            this.isNeedToLoad = true;
            this.activeStep = 0
            this.setSecondStepToDefaults()
        }
        this.isAppOpen = open;
    }

    set validityOfTextFields(val: {[key:string]:boolean}){
        this.validTextFields = {...this.validTextFields, ...val};
    }
    get validityOfTextFields() {
        return this.validTextFields;
    }
    checkTextFields(){
        let allValid = true;
        for (const key in this.validTextFields) {
            if (this.validityOfTextFields[key] === false){
                allValid = false;
                break;
            }
        }
        return allValid;
    }

    get selected(): ISelectedParams {
        return this.selectedValues;
    }
    set selected(selected: any){
        this.selectedValues = {...this.selectedValues, ...selected};
    }
    setSecondStepToDefaults(){
        this.selected = {
            services: [],
            employee: { uid: '', name: '', },
            dateTime: { date: '', timeBegin: '', timeEnd: '', formattedDate: '', formattedTimeBegin: '', formattedTimeEnd: ''}
        }
    }
    isServicesAvailable(): boolean{
        let allAvailable = true;
        for(let i=0; i < this.selectedValues.services.length; i++){
            const available = dataState.servicesList.find(
                (service: IService) => service.uid === this.selectedValues.services[i].uid
            );
            if (!available){
                allAvailable = false;
                break;
            }
        }
        return allAvailable;
    }

    get isSelectDoctorBeforeService(){
        return this.selectDoctorBeforeService;
    }
    set isSelectDoctorBeforeService(value: boolean){
        this.selectDoctorBeforeService = value;
    }

    get appResult() {
        return this.result;
    }
    set appResult(value: boolean){
        this.result = value;
    }

    get isDemoMode() {
        return this.DEMO_MODE;
    }
    get timeStepDuration(){
        return this.timeStepDurationMinutes;
    }
    get isUseTimeSteps(){
        return this.useTimeSteps;
    }
    get isStrictCheckingOfRelations(){
        return this.strictCheckingOfRelations;
    }
    get isShowDoctorsWithoutDepartment(){
        return this.showDoctorsWithoutDepartment;
    }
    get isUseMultipleServices(){
        return this.useMultipleServices;
    }
    get privacyPageUrl(){
        return this.privacyLink;
    }

    private resetAppState() {
        this.selected = {
            clinic: {
                name: dataState.getNameByUid('clinics', dataState.getDefaultClinicUid()),
                uid: dataState.getDefaultClinicUid()
            },
            specialty: {name: '', uid: ''},
            employee: {name: '', uid: ''},
            services: [],
            dateTime: {date: '', timeBegin: '', timeEnd: '', formattedDate: '', formattedTimeBegin: '', formattedTimeEnd: ''}
        };
        this.activeStep = 0;
        this.lastOpenedFormType = EAppFormTypes.common;
    }
}

const appState = new AppState();
export default appState;