import type { Meta, StoryObj } from '@storybook/react';
import { Button } from './Button';

const meta = {
    title: 'Shared/UI/Button',
    component: Button,
    parameters: {
        layout: 'centered',
    },
    tags: ['autodocs'],
    argTypes: {
        variant: {
            control: 'select',
            options: ['primary', 'secondary', 'danger', 'ghost'],
        },
        size: {
            control: 'select',
            options: ['sm', 'md', 'lg'],
        },
        isDisabled: {
            control: 'boolean',
        },
    },
} satisfies Meta<typeof Button>;

export default meta;
type Story = StoryObj<typeof meta>;

// Primary Button
export const Primary: Story = {
    args: {
        children: 'Primary Button',
        variant: 'primary',
    },
};

// Secondary Button
export const Secondary: Story = {
    args: {
        children: 'Secondary Button',
        variant: 'secondary',
    },
};

// Danger Button
export const Danger: Story = {
    args: {
        children: 'Danger Button',
        variant: 'danger',
    },
};

// Ghost Button
export const Ghost: Story = {
    args: {
        children: 'Ghost Button',
        variant: 'ghost',
    },
};

// Size Variations
export const Sizes: Story = {
    render: () => (
        <div className="flex items-center gap-4">
            <Button size="sm">Small</Button>
            <Button size="md">Medium</Button>
            <Button size="lg">Large</Button>
        </div>
    ),
};

// Disabled State
export const Disabled: Story = {
    args: {
        children: 'Disabled Button',
        isDisabled: true,
    },
};

// Loading State
export const Loading: Story = {
    args: {
        children: (
            <div className="flex items-center gap-2">
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Loading...</span>
            </div>
        ),
        isDisabled: true,
    },
};