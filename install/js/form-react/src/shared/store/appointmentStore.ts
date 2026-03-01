import { create } from 'zustand'
import {Slot} from "../../entities/slot/model";
import {ContactInfo} from "../../entities/appointment/model";
import { Clinic } from '../../entities/clinic/model';
import { Doctor } from '../../entities/doctor/model';
import { Service } from '../../entities/service/model';

export type FlowMode = 'doctor-first' | 'service-first'

interface AppointmentStore {
    step: number;
    mode: FlowMode | null | undefined;
    isOpen: boolean;
    isLoading: boolean;

    bookingUid: string | null | undefined
    clinicUid: string | null | undefined
    specialtyUid: string | null | undefined
    doctorUid: string | null | undefined
    serviceUIDs: string[]

    slot: Slot | null | undefined
    contact: ContactInfo | null | undefined
    comment: string | null | undefined

    setMode: (mode: FlowMode) => void
    setIsOpen: (isOpen: boolean) => void
    setIsLoading: (isLoading: boolean) => void
    nextStep: () => void
    prevStep: () => void
    goToStep: (step: number) => void;
    reset: () => void

    setBookingUid: (uid: string) => void
    setClinic: (uid: string) => void
    setSpecialty: (uid: string) => void
    setDoctor: (uid: string) => void
    setServices: (UIDs: string[]) => void
    setSlot: (slot: Slot) => void
    setContact: (contact: ContactInfo) => void
    setComment: (comment: string) => void

    clinicsCache: Clinic[]
    doctorsCache: Doctor[]
    servicesCache: Record<string, Service[]>
    setClinicsCache: (data: Clinic[]) => void
    setDoctorsCache: (data: Doctor[]) => void
    setServicesCache: (clinicUid: string, data: Service[]) => void
}

export const useAppointmentStore = create<AppointmentStore>((set) => ({
    step: 0,
    mode: null,
    isOpen: false,
    isLoading: false,

    bookingUid: null,
    clinicUid: null,
    specialtyUid: null,
    doctorUid: null,
    serviceUIDs: [],

    slot: null,
    contact: null,
    comment: null,

    clinicsCache: [],
    doctorsCache: [],
    servicesCache: {},

    setMode: (mode) => set({
        mode,
        step: 1,
    }),

    setIsOpen: (isOpen) => set({ isOpen }),
    setIsLoading: (isLoading) => set({ isLoading }),

    setBookingUid: (uid: string) => set({
        bookingUid: uid,
    }),

    setClinic: (uid) => set({
        clinicUid: uid,
        specialtyUid: null,
        doctorUid: null,
        serviceUIDs: [],
    }),

    setSpecialty: (uid) => set({
        specialtyUid: uid,
        doctorUid: null,
        serviceUIDs: [],
    }),

    setDoctor: (uid) => set({
        doctorUid: uid,
    }),

    setServices: (UIDs) => set({
        serviceUIDs: UIDs,
    }),

    setSlot: (slot) => set({ slot }),

    setContact: (contact) => set({ contact }),

    setComment: (comment) => set({ comment }),

    setClinicsCache: (data) => set({ clinicsCache: data }),

    setDoctorsCache: (data) => set({ doctorsCache: data }),

    setServicesCache: (clinicUid, data) => set(state => ({
        servicesCache: {
            ...state.servicesCache,
            [clinicUid]: data
        }
    })),

    nextStep: () => set(state => ({ step: state.step + 1 })),
    prevStep: () => set(state => ({
        step: Math.max(0, state.step - 1)
    })),
    goToStep: (step) => set({ step }),
    reset: () => set({
        step: 0,
        mode: null,

        clinicUid: null,
        specialtyUid: null,
        doctorUid: null,
        serviceUIDs: [],

        slot: null,
        contact: null,
        comment: null,
    })
}))