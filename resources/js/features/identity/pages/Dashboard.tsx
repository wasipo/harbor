import React from 'react';
import { DashboardContainer } from '../containers/DashboardContainer';

interface DashboardProps {
    user: {
        name: string;
        email: string;
        categories: Array<{
            id: string;
            code: string;
            name: string;
        }>;
        roles: Array<{
            id: string;
            key: string;
            name: string;
        }>;
        permissions: Array<{
            id: string;
            key: string;
            name: string;
        }>;
    };
}

// Inertiaページコンポーネント（薄いラッパー）
export default function Dashboard({ user }: DashboardProps) {
    return <DashboardContainer user={user} />;
}