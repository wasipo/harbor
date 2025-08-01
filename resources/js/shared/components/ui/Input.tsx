import React from 'react';
import { 
    TextField, 
    Label, 
    Input as AriaInput, 
    TextFieldProps,
    FieldError,
    Text
} from 'react-aria-components';
import { tv, VariantProps } from 'tailwind-variants';

const inputVariants = tv({
    slots: {
        root: 'flex flex-col gap-1.5',
        label: 'text-sm font-medium text-gray-700',
        input: 'w-full px-3 py-2 border rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-1',
        error: 'text-sm text-red-600',
        description: 'text-sm text-gray-500',
    },
    variants: {
        variant: {
            default: {
                input: 'border-gray-300 focus:border-blue-500 focus:ring-blue-500',
            },
            error: {
                input: 'border-red-300 focus:border-red-500 focus:ring-red-500',
            },
        },
        size: {
            sm: {
                input: 'text-sm px-2 py-1',
            },
            md: {
                input: 'text-base px-3 py-2',
            },
            lg: {
                input: 'text-lg px-4 py-3',
            },
        },
    },
    defaultVariants: {
        variant: 'default',
        size: 'md',
    },
});

interface InputProps extends Omit<TextFieldProps, 'children'>, VariantProps<typeof inputVariants> {
    label?: string;
    placeholder?: string;
    type?: string;
    error?: string;
    description?: string;
}

export const Input: React.FC<InputProps> = ({
    label,
    placeholder,
    type = 'text',
    error,
    description,
    variant,
    size,
    className,
    ...props
}) => {
    const styles = inputVariants({ 
        variant: error ? 'error' : variant, 
        size 
    });

    return (
        <TextField className={styles.root({ className })} {...props}>
            {label && <Label className={styles.label()}>{label}</Label>}
            <AriaInput 
                className={styles.input()} 
                placeholder={placeholder}
                type={type}
            />
            {description && !error && (
                <Text slot="description" className={styles.description()}>
                    {description}
                </Text>
            )}
            {error && (
                <FieldError className={styles.error()}>
                    {error}
                </FieldError>
            )}
        </TextField>
    );
};