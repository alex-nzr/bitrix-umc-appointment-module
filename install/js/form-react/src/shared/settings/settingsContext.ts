import { createContext } from "react";
import {WidgetSettings} from "./widgetSettings";

export const SettingsContext = createContext<WidgetSettings | null>(null);