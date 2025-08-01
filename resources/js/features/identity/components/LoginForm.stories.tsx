import type { Meta, StoryObj } from '@storybook/react';
import { LoginForm } from './LoginForm';

const meta: Meta<typeof LoginForm> = {
    title: 'Features/Identity/LoginForm',
    component: LoginForm,
    parameters: {
        layout: 'fullscreen',
    },
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof meta>;

// デフォルトの状態
export const Default: Story = {
    args: {
        data: {
            email: '',
            password: '',
            remember: false,
        },
        onSubmit: (data) => console.log('Submit:', data),
        onChange: (data) => console.log('Change:', data),
    },
};

// 入力済みの状態
export const FilledForm: Story = {
    args: {
        data: {
            email: 'user@example.com',
            password: 'password123',
            remember: true,
        },
        onSubmit: (data) => console.log('Submit:', data),
        onChange: (data) => console.log('Change:', data),
    },
};

// エラー表示
export const WithErrors: Story = {
    args: {
        data: {
            email: 'invalid-email',
            password: '',
            remember: false,
        },
        errors: {
            email: '有効なメールアドレスを入力してください',
            password: 'パスワードは必須です',
        },
        onSubmit: (data) => console.log('Submit:', data),
        onChange: (data) => console.log('Change:', data),
    },
};

// 処理中の状態
export const Processing: Story = {
    args: {
        data: {
            email: 'user@example.com',
            password: 'password123',
            remember: false,
        },
        processing: true,
        onSubmit: (data) => console.log('Submit:', data),
        onChange: (data) => console.log('Change:', data),
    },
};