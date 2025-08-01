import React from 'react';
import { Head } from '@inertiajs/react';
import { UserListContainer } from '../containers/UserListContainer';
import type { UserListResponse } from '../types/UserListItem';

interface Props {
    userList: UserListResponse;
}

export default function UserList({ userList }: Props) {
    return (
        <>
            <Head title="ユーザー一覧" />
            <UserListContainer userList={userList} />
        </>
    );
}