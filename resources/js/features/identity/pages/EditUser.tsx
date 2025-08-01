import React from 'react';
import { Head } from '@inertiajs/react';
import { EditUserContainer } from '../containers/EditUserContainer';

interface Props {
    user: {
        id: string;
        name: string;
        email: string;
        is_active: boolean;
        categories: Array<{
            id: string;
            name: string;
            isPrimary?: boolean;
        }>;
        roles: Array<{
            id: string;
            name: string;
        }>;
    };
}

export default function EditUser({ user }: Props) {
    return (
        <>
            <Head title={`ユーザー編集 - ${user.name}`} />
            <EditUserContainer user={user} />
        </>
    );
}