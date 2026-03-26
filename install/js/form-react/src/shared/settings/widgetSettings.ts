export enum ConfirmationType{
    none = "none",
    phone = "phone",
    email = "email",
}

export interface WidgetSettings {
    mainColor: string|null;
    privacyPolicyUrl: string|null;
    defaultClinicUid: string|null;
    schedulePeriodDays: number;
    logoImageSrc: string|null;
    servicesEnabled: boolean;
    emailNotificationEnabled: boolean;
    confirmationType: ConfirmationType;
    useCustomButton: boolean;
    customButtonSelector: string|null;
    phoneInputMask: string;
    error?: string;
}
