import type { Meta, StoryObj } from '@storybook/react';
import { EditUserForm } from './EditUserForm';

const meta: Meta<typeof EditUserForm> = {
    title: 'Features/Identity/EditUserForm',
    component: EditUserForm,
    parameters: {
        layout: 'centered',
    },
    decorators: [
        (Story) => (
            <div className="w-full max-w-2xl">
                <Story />
            </div>
        ),
    ],
};

export default meta;
type Story = StoryObj<typeof EditUserForm>;

export const ActiveUser: Story = {
    args: {
        user: {
            id: '01HZ1234567890ABCDEFGHIJK',
            name: '山田 太郎',
            email: 'yamada@example.com',
            is_active: true,
        },
        onCancel: () => console.log('Cancel clicked'),
    },
};

export const InactiveUser: Story = {
    args: {
        user: {
            id: '01HZ2345678901BCDEFGHIJKL',
            name: '鈴木 花子',
            email: 'suzuki@example.com',
            is_active: false,
        },
        onCancel: () => console.log('Cancel clicked'),
    },
};

export const LongName: Story = {
    args: {
        user: {
            id: '01HZ3456789012CDEFGHIJKLM',
            name: 'とても長い名前のユーザーさんの表示テスト太郎',
            email: 'very-long-email-address-for-testing@example-company.co.jp',
            is_active: true,
        },
        onCancel: () => console.log('Cancel clicked'),
    },
};