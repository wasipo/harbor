import React from 'react';
import { Head, router } from '@inertiajs/react';
import { Dashboard, UserData } from '../components/Dashboard';

interface DashboardProps {
    user: {
        id: string;
        name: string;
        email: string;
        categories: Array<{
            id: string;
            code: string;
            name: string;
            is_primary?: boolean;
        }>;
        roles: Array<{
            id: string;
            key: string;
            name: string;
            description?: string;
        }>;
        permissions: Array<{
            id: string;
            key: string;
            name: string;
        }>;
    };
}

// Inertia依存を持つコンテナコンポーネント
export function DashboardContainer({ user }: DashboardProps) {
    const handleLogout = () => {
        router.post(route('logout'));
    };

    const handleNavigate = (path: string) => {
        router.visit(path);
    };

    // Transform data for component
    const userData: UserData = {
        id: user.id,
        name: user.name,
        email: user.email,
        categories: user.categories.map(cat => ({
            id: cat.id,
            code: cat.code,
            name: cat.name,
            isPrimary: cat.is_primary || false,
        })),
        roles: user.roles,
        permissions: user.permissions,
    };

    return (
        <>
            <Head title="ダッシュボード - Harbor" />
            <Dashboard 
                user={userData} 
                onLogout={handleLogout}
                onNavigate={handleNavigate}
            />
        </>
    );
}