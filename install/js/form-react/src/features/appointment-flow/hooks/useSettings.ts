import { useContext } from "react";
import {WidgetSettings} from "../../../shared/settings/widgetSettings";
import {SettingsContext} from "../../../shared/settings/settingsContext";

export const useSettings = (): WidgetSettings => {
    const ctx = useContext(SettingsContext);

    if (!ctx) {
        throw new Error("useSettings must be used inside Providers");
    }

    return ctx;
};