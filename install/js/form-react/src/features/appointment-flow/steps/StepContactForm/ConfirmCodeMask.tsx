import React from "react";
import {IMaskInput} from "react-imask";

interface ConfirmCodeMaskProps {
    onChange: (event: { target: { name: string; value: string } }) => void;
    name: string;
}

export const ConfirmCodeMask = React.forwardRef<HTMLInputElement, ConfirmCodeMaskProps>(
    function ConfirmCodeMask(props, ref)
    {
        const { onChange, ...other } = props;
        return (
            <IMaskInput
                {...other}
                mask={"0 - 0 - 0 - 0"}
                definitions={{
                    0: /[0-9]/
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