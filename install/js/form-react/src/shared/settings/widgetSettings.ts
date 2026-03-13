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
    servicesEnabled: boolean,
    confirmationType: ConfirmationType,
    phoneInputMask: string,
    error?: string;
}