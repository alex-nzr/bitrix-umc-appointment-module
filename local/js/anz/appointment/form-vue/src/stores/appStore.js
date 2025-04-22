import { defineStore } from "ui.vue3.pinia";
import {AppState} from "../Application/State/AppState";

const storeObject = new AppState();
export const appStore = defineStore(storeObject.getStoreName(), storeObject);