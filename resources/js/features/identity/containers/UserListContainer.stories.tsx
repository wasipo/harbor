import type { Meta, StoryObj } from '@storybook/react';
import { UserListContainer } from './UserListContainer';
import type { UserListResponse } from '../types/UserListItem';

const meta: Meta<typeof UserListContainer> = {
    title: 'Features/Identity/UserListContainer',
    component: UserListContainer,
    parameters: {
        layout: 'fullscreen',
    },
};

export default meta;
type Story = StoryObj<typeof UserListContainer>;

const sampleUserList: UserListResponse = {
    data: [
        {
            id: '01HZ1234567890ABCDEFGHIJK',
            name: '山田 太郎',
            email: 'yamada@example.com',
            status: 'active',
            categories: [
                { id: '1', code: 'sales_dept', name: '営業部', isPrimary: true },
                { id: '2', code: 'full_time', name: '正社員' }
            ],
            roles: [
                { id: '1', key: 'sales_manager', name: '営業部管理者権限' },
                { id: '2', key: 'report_viewer', name: 'レポート閲覧権限' }
            ],
            createdAt: '2024-01-15T09:00:00+09:00',
            lastLoginAt: '2024-06-21T14:30:00+09:00'
        },
        {
            id: '01HZ2345678901BCDEFGHIJKL',
            name: '佐藤 花子',
            email: 'sato@example.com',
            status: 'active',
            categories: [
                { id: '3', code: 'dev_dept', name: '開発部', isPrimary: true },
                { id: '2', code: 'full_time', name: '正社員' }
            ],
            roles: [
                { id: '3', key: 'developer_basic', name: '開発者基本権限' }
            ],
            createdAt: '2024-02-01T10:00:00+09:00',
            lastLoginAt: '2024-06-21T09:15:00+09:00'
        },
        {
            id: '01HZ3456789012CDEFGHIJKLM',
            name: '鈴木 一郎',
            email: 'suzuki@example.com',
            status: 'inactive',
            categories: [
                { id: '4', code: 'hr_dept', name: '人事部', isPrimary: true }
            ],
            roles: [],
            createdAt: '2023-12-01T11:00:00+09:00',
            lastLoginAt: '2024-05-01T16:00:00+09:00'
        },
        {
            id: '01HZ4567890123DEFGHIJKLMN',
            name: '田中 美咲',
            email: 'tanaka@example.com',
            status: 'suspended',
            categories: [],
            roles: [],
            createdAt: '2024-03-15T13:00:00+09:00',
            lastLoginAt: '2024-05-10T16:45:00+09:00'
        },
        {
            id: '01HZ5678901234EFGHIJKLMNO',
            name: '高橋 健太',
            email: 'takahashi@example.com',
            status: 'active',
            categories: [
                { id: '3', code: 'dev_dept', name: '開発部', isPrimary: true },
                { id: '5', code: 'contractor', name: '業務委託' }
            ],
            roles: [
                { id: '3', key: 'developer_basic', name: '開発者基本権限' },
                { id: '4', key: 'code_review', name: 'コードレビュー権限' }
            ],
            createdAt: '2024-04-01T09:00:00+09:00',
            lastLoginAt: null
        },
        {
            id: '01HZ6789012345FGHIJKLMNOP',
            name: '伊藤 真由美',
            email: 'ito@example.com',
            status: 'active',
            categories: [
                { id: '6', code: 'admin_dept', name: '管理部', isPrimary: true },
                { id: '2', code: 'full_time', name: '正社員' }
            ],
            roles: [
                { id: '5', key: 'system_admin', name: 'システム管理者権限' }
            ],
            createdAt: '2023-10-01T10:00:00+09:00',
            lastLoginAt: '2024-06-21T17:00:00+09:00'
        }
    ],
    meta: {
        current_page: 1,
        last_page: 5,
        per_page: 10,
        total: 48
    }
};

export const Default: Story = {
    args: {
        userList: sampleUserList,
    },
};

export const EmptyState: Story = {
    args: {
        userList: {
            data: [],
            meta: {
                current_page: 1,
                last_page: 0,
                per_page: 10,
                total: 0
            }
        },
    },
};

export const SinglePage: Story = {
    args: {
        userList: {
            data: sampleUserList.data.slice(0, 3),
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 3
            }
        },
    },
};

export const LastPage: Story = {
    args: {
        userList: {
            data: sampleUserList.data.slice(0, 8),
            meta: {
                current_page: 5,
                last_page: 5,
                per_page: 10,
                total: 48
            }
        },
    },
};