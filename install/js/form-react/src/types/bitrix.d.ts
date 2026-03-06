export {};

declare global {
    interface Window {
        BX: {
            ready: (callback: () => void) => void;
            Extension: {
                getSettings: (extension: string) => any;
            };
            message: (code: string) => string
        };
    }
}