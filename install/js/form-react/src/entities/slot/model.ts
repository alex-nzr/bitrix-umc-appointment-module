export interface Slot {
    clinicUid:          string
    doctorUid:          string
    date:               string
    timeBegin:          string
    timeEnd:            string
    formattedDate:      string
    formattedTimeBegin: string
    formattedTimeEnd:   string
    duration:           number
    typeOfTimeUid?:     string
    isAvailable:        boolean
    status:             string
}