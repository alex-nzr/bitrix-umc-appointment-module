import {useSettings} from "../../hooks/useSettings";
import React from "react";
import {IMaskInput} from "react-imask";

interface PhoneMaskProps {
    onChange: (event: { target: { name: string; value: string } }) => void;
    name: string;
}

export const PhoneMask = React.forwardRef<HTMLInputElement, PhoneMaskProps>(
    function PhoneMask(props, ref)
    {
        const {phoneInputMask} = useSettings();
        const { onChange, ...other } = props;

        return (
            <IMaskInput
                {...other}
                mask={phoneInputMask}
                definitions={{
                    0: /[0-9]/,
                }}
                inputRef={ref}
                onAccept={(value: any) =>
                    onChange({
                        target: {
                            name: props.name,
                            value
                        }
                    })
                }
                overwrite
            />
        );
    }
);