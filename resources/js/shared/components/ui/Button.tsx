import React from 'react';
import { Button as AriaButton, ButtonProps as AriaButtonProps } from 'react-aria-components';
import { tv, VariantProps } from 'tailwind-variants';

const buttonVariants = tv({
    base: 'inline-flex items-center justify-center font-medium rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
    variants: {
        variant: {
            primary: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
            secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
            danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
            ghost: 'bg-transparent hover:bg-gray-100 focus:ring-gray-500',
        },
        size: {
            sm: 'text-sm px-3 py-1.5',
            md: 'text-base px-4 py-2',
            lg: 'text-lg px-6 py-3',
        },
        fullWidth: {
            true: 'w-full',
        },
    },
    defaultVariants: {
        variant: 'primary',
        size: 'md',
    },
});

export interface ButtonProps extends AriaButtonProps, VariantProps<typeof buttonVariants> {
    isLoading?: boolean;
}

export const Button: React.FC<ButtonProps> = ({
    className,
    variant,
    size,
    fullWidth,
    isLoading,
    isDisabled,
    children,
    ...props
}) => {
    return (
        <AriaButton
            className={buttonVariants({ variant, size, fullWidth, className })}
            isDisabled={isDisabled || isLoading}
            {...props}
        >
            {isLoading ? (
                <div className="flex items-center gap-2">
                    <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" />
                    <span>処理中...</span>
                </div>
            ) : (
                children
            )}
        </AriaButton>
    );
};