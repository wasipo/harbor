import React from 'react';
import { tv, VariantProps } from 'tailwind-variants';

const badgeVariants = tv({
    base: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    variants: {
        variant: {
            default: 'bg-gray-100 text-gray-800',
            primary: 'bg-blue-100 text-blue-800',
            success: 'bg-green-100 text-green-800',
            warning: 'bg-yellow-100 text-yellow-800',
            danger: 'bg-red-100 text-red-800',
            info: 'bg-purple-100 text-purple-800',
        },
        size: {
            sm: 'text-xs px-2 py-0.5',
            md: 'text-sm px-2.5 py-0.5',
            lg: 'text-base px-3 py-1',
        },
    },
    defaultVariants: {
        variant: 'default',
        size: 'md',
    },
});

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement>, VariantProps<typeof badgeVariants> {
    children: React.ReactNode;
}

export const Badge: React.FC<BadgeProps> = ({
    className,
    variant,
    size,
    children,
    ...props
}) => {
    return (
        <span
            className={badgeVariants({ variant, size, className })}
            {...props}
        >
            {children}
        </span>
    );
};