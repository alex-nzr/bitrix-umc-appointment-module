import { create } from 'zustand'
import {Slot} from "../../entities/slot/model";
import {ContactInfo} from "../../entities/appointment/model";
import { Clinic } from '../../entities/clinic/model';
import { Doctor } from '../../entities/doctor/model';
import { Service } from '../../entities/service/model';

export type FlowMode = 'doctor-first' | 'service-first'

interface AppointmentStore {
    step: number;
    mode: FlowMode | null;
    isOpen: boolean;
    isLoading: boolean;

    bookingUid: string | null
    clinicUid: string | null
    specialtyUid: string | null
    doctorUid: string | null
    serviceUIDs: string[]

    slot: Slot | null
    contact: ContactInfo
    code: string | null

    setMode: (mode: FlowMode) => void
    setIsOpen: (isOpen: boolean) => void
    setIsLoading: (isLoading: boolean) => void
    nextStep: () => void
    prevStep: () => void
    goToStep: (step: number) => void;
    reset: () => void

    setBookingUid: (uid: string | null) => void
    setClinic: (uid: string) => void
    setSpecialty: (uid: string) => void
    setDoctor: (uid: string) => void
    setServices: (UIDs: string[]) => void
    setSlot: (slot: Slot|null) => void
    setContact: (contact: ContactInfo) => void
    setCode: (code: string | null) => void

    clinicsCache: Clinic[]
    doctorsCache: Doctor[]
    servicesCache: Record<string, Service[]>
    slotsCache: Record<string, Slot[]>
    setClinicsCache: (data: Clinic[]) => void
    setDoctorsCache: (data: Doctor[]) => void
    setServicesCache: (clinicUid: string, data: Service[]) => void
    setSlotsCache: (key: string, slots: Slot[]) => void
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
    contact: {
        firstName: '',
        secondName: '',
        lastName: '',
        phone: '',
        birthday: ''
    },
    code: null,

    clinicsCache: [],
    doctorsCache: [],
    servicesCache: {},
    slotsCache: {},

    setMode: (mode) => set({ mode }),

    setIsOpen: (isOpen) => set({ isOpen }),
    setIsLoading: (isLoading) => set({ isLoading }),

    setBookingUid: (uid: string | null) => set({
        bookingUid: uid,
    }),

    setClinic: (uid) => set({
        clinicUid: uid,
        specialtyUid: null,
        doctorUid: null,
        serviceUIDs: [],
        slot: null
    }),

    setSpecialty: (uid) => set({
        specialtyUid: uid,
        doctorUid: null,
        serviceUIDs: [],
        slot: null
    }),

    setDoctor: (uid) => set({
        doctorUid: uid,
        slot: null
    }),

    setServices: (UIDs) => set({
        serviceUIDs: UIDs,
        slot: null
    }),

    setSlot: (slot) => set({ slot }),

    setContact: (contact) => set({ contact }),

    setCode: (code) => set({ code }),

    setClinicsCache: (data) => set({ clinicsCache: data }),

    setDoctorsCache: (data) => set({ doctorsCache: data }),

    setServicesCache: (clinicUid, data) => set(state => ({
        servicesCache: {
            ...state.servicesCache,
            [clinicUid]: data
        }
    })),

    setSlotsCache: (key, slots) =>
        set((state) => ({
            slotsCache: {
                ...state.slotsCache,
                [key]: slots
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
        bookingUid: null,

        clinicUid: null,
        specialtyUid: null,
        doctorUid: null,
        serviceUIDs: [],

        slot: null,
        code: null,
        contact: {
            firstName: '',
            secondName: '',
            lastName: '',
            phone: '',
            birthday: ''
        },
    })
}))
