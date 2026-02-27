import { defineStore } from "ui.vue3.pinia";
import {OneCState} from "../Application/State/OneCState";


const storeObject = new OneCState();
export const oneCStore = defineStore(storeObject.getStoreName(), storeObject);