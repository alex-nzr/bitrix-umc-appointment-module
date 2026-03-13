import React from "react";
import {IMaskInput} from "react-imask";

interface DateMaskProps {
    onChange: (event: { target: { name: string; value: string } }) => void;
    name: string;
}

export const DateMask = React.forwardRef<HTMLInputElement, DateMaskProps>(
    function DateMask(props, ref) {

        const { onChange, ...other } = props;

        return (
            <IMaskInput
                {...other}
                mask="00.00.0000"
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