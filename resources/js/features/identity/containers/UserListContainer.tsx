import React from 'react';
import { router } from '@inertiajs/react';
import { UserTable } from '../components/UserTable';
import type { UserListResponse } from '../types/UserListItem';
import { 
    Users, 
    UserPlus, 
    CheckCircle, 
    XCircle, 
    Search, 
    Filter, 
    Download 
} from 'lucide-react';

interface UserListContainerProps {
    userList: UserListResponse;
}

export function UserListContainer({ userList }: UserListContainerProps) {
    const handlePageChange = (page: number) => {
        router.get(route('users.index'), { page }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleUserClick = (userId: string) => {
        router.get(route('users.show', userId));
    };

    const totalUsers = userList.meta.total;
    const activeUsers = userList.data.filter(u => u.status === 'active').length;

    return (
        <div className="min-h-screen bg-gray-100">
            {/* Header */}
            <header className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => router.get(route('dashboard'))}
                            className="w-8 h-8 bg-gray-900 rounded flex items-center justify-center hover:bg-gray-800 transition-colors"
                        >
                            <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1" />
                            </svg>
                        </button>
                        <span className="font-semibold text-gray-900">Harbor</span>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {/* Breadcrumb */}
                <nav className="mb-4">
                    <ol className="flex items-center space-x-2 text-sm">
                        <li>
                            <button
                                onClick={() => router.get(route('dashboard'))}
                                className="text-gray-500 hover:text-gray-700 transition-colors"
                            >
                                ダッシュボード
                            </button>
                        </li>
                        <li className="text-gray-400">/</li>
                        <li className="text-gray-900 font-medium">ユーザー管理</li>
                    </ol>
                </nav>
                
                {/* Page Title */}
                <div className="mb-6">
                    <h1 className="text-2xl font-bold text-gray-900">
                        ユーザー管理
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        システムに登録されているユーザーの管理を行います
                    </p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div className="bg-white rounded-lg border border-gray-200 p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 mb-1">総ユーザー数</p>
                                <p className="text-2xl font-bold text-gray-900">{totalUsers}</p>
                            </div>
                            <div className="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <Users className="w-5 h-5 text-gray-600" />
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg border border-gray-200 p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 mb-1">アクティブ</p>
                                <p className="text-2xl font-bold text-green-600">{activeUsers}</p>
                            </div>
                            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <CheckCircle className="w-5 h-5 text-green-600" />
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg border border-gray-200 p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 mb-1">非アクティブ</p>
                                <p className="text-2xl font-bold text-gray-500">{totalUsers - activeUsers}</p>
                            </div>
                            <div className="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <XCircle className="w-5 h-5 text-gray-500" />
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg border border-gray-200 p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 mb-1">新規登録（今月）</p>
                                <p className="text-2xl font-bold text-blue-600">+12</p>
                            </div>
                            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <UserPlus className="w-5 h-5 text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Search and Filters */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    {/* Search Box */}
                    <div className="lg:col-span-2">
                        <div className="bg-white rounded-lg border border-gray-200 p-4">
                            <div className="flex flex-col sm:flex-row gap-3">
                                <div className="flex-1">
                                    <div className="relative">
                                        <input
                                            type="text"
                                            placeholder="名前やメールアドレスで検索..."
                                            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        />
                                        <Search className="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                                    </div>
                                </div>
                                <button className="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    検索
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {/* Quick Actions */}
                    <div className="bg-white rounded-lg border border-gray-200 p-4">
                        <div className="flex items-center justify-between">
                            <button className="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <Filter className="w-4 h-4 mr-1.5" />
                                フィルター
                            </button>
                            <button className="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <Download className="w-4 h-4 mr-1.5" />
                                CSV出力
                            </button>
                            <button
                                onClick={() => router.get(route('users.create'))}
                                className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors"
                            >
                                <UserPlus className="w-4 h-4 mr-1.5" />
                                新規登録
                            </button>
                        </div>
                    </div>
                </div>
                
                {/* Filter Tags */}
                <div className="flex flex-wrap items-center gap-2 mb-4">
                    <span className="text-sm text-gray-500">フィルター:</span>
                    <span className="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                        アクティブユーザー
                        <button className="hover:text-blue-900">×</button>
                    </span>
                    <button className="text-sm text-gray-500 hover:text-gray-700">クリア</button>
                </div>

                {/* User Table */}
                <UserTable
                    users={userList.data}
                    currentPage={userList.meta.current_page}
                    totalPages={userList.meta.last_page}
                    onPageChange={handlePageChange}
                    onUserClick={handleUserClick}
                />
            </main>
        </div>
    );
}