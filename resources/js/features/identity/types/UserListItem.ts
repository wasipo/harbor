import type { UserCategory } from './UserCategory';
import type { Role } from './Role';

export type UserListItem = {
    id: string;
    name: string;
    email: string;
    status: 'active' | 'inactive' | 'suspended';
    categories: UserCategory[];
    roles: Role[];
    createdAt: string;
    lastLoginAt: string | null;
};

export type UserListResponse = {
    data: UserListItem[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
};