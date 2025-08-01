import React from 'react';
import { Button } from 'react-aria-components';
import { ChevronLeft, ChevronRight, User, Shield, Building2, Edit2 } from 'lucide-react';
import type { UserListItem } from '../types/UserListItem';

export interface UserTableProps {
    users: UserListItem[];
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
    onUserClick?: (userId: string) => void;
}

export function UserTable({ 
    users, 
    currentPage, 
    totalPages, 
    onPageChange,
    onUserClick 
}: UserTableProps) {
    const getStatusBadge = (status: UserListItem['status']) => {
        const config = {
            active: {
                style: 'bg-green-100 text-green-700',
                icon: '●',
                label: 'アクティブ'
            },
            inactive: {
                style: 'bg-gray-100 text-gray-600',
                icon: '○',
                label: '非アクティブ'
            },
            suspended: {
                style: 'bg-red-100 text-red-700',
                icon: '×',
                label: '停止中'
            }
        };
        
        const { style, icon, label } = config[status];
        return (
            <span className={`inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium ${style}`}>
                <span className="text-[10px]">{icon}</span>
                {label}
            </span>
        );
    };

    const formatDate = (dateString: string | null) => {
        if (!dateString) return '未ログイン';
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now.getTime() - date.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 1) return '今日';
        if (diffDays === 1) return '昨日';
        if (diffDays < 7) return `${diffDays}日前`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)}週間前`;
        
        return date.toLocaleDateString('ja-JP', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    return (
        <div className="space-y-4">
            {/* User Cards */}
            {users.map((user) => (
                <div
                    key={user.id}
                    className="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition-all cursor-pointer"
                    onClick={() => onUserClick?.(user.id)}
                >
                    <div className="flex items-start justify-between">
                        {/* User Info */}
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <User className="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <div className="flex items-center gap-3 mb-1">
                                    <h3 className="text-base font-semibold text-gray-900">{user.name}</h3>
                                    {getStatusBadge(user.status)}
                                </div>
                                <p className="text-sm text-gray-500">{user.email}</p>
                                <p className="text-xs text-gray-400 mt-1">最終ログイン: {formatDate(user.lastLoginAt)}</p>
                                
                                {/* Categories and Roles */}
                                <div className="mt-3 space-y-2">
                                    {/* Categories */}
                                    {user.categories.length > 0 && (
                                        <div className="flex items-center gap-2">
                                            <Building2 className="w-4 h-4 text-gray-400 flex-shrink-0" />
                                            <div className="flex flex-wrap gap-1.5">
                                                {user.categories.map((category) => (
                                                    <span
                                                        key={category.id}
                                                        className={`inline-flex items-center text-xs px-2 py-0.5 rounded ${category.isPrimary ? 'bg-blue-100 text-blue-700 font-medium' : 'bg-gray-100 text-gray-600'}`}
                                                    >
                                                        {category.name}
                                                        {category.isPrimary && <span className="ml-1 text-[10px]">主</span>}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    
                                    {/* Roles */}
                                    {user.roles.length > 0 && (
                                        <div className="flex items-center gap-2">
                                            <Shield className="w-4 h-4 text-gray-400 flex-shrink-0" />
                                            <div className="flex flex-wrap gap-1.5">
                                                {user.roles.map((role) => (
                                                    <span
                                                        key={role.id}
                                                        className="inline-flex items-center text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded"
                                                    >
                                                        {role.name}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                        
                        {/* Actions */}
                        <div className="flex flex-col gap-2">
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    // TODO: ユーザー編集画面への遷移
                                    console.log('Edit user:', user.id);
                                }}
                                className="inline-flex items-center px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <Edit2 className="w-4 h-4 mr-1.5" />
                                編集
                            </button>
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    // TODO: ロール設定画面への遷移
                                    console.log('Manage roles:', user.id);
                                }}
                                className="inline-flex items-center px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <Shield className="w-4 h-4 mr-1.5" />
                                ロール設定
                            </button>
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    // TODO: カテゴリ設定画面への遷移
                                    console.log('Manage categories:', user.id);
                                }}
                                className="inline-flex items-center px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <Building2 className="w-4 h-4 mr-1.5" />
                                カテゴリ設定
                            </button>
                        </div>
                    </div>
                </div>
            ))}
            
            {/* Pagination */}
            {totalPages > 1 && (
                <div className="mt-6 bg-white rounded-lg border border-gray-200 px-4 py-3">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div className="text-sm text-gray-700">
                            <span className="font-medium">{users.length}</span> 件を表示中
                            （全 <span className="font-medium">{totalPages * 10}</span> 件中）
                        </div>
                        
                        <div className="flex items-center gap-2">
                            {/* First Page */}
                            <Button
                                onPress={() => onPageChange(1)}
                                isDisabled={currentPage === 1}
                                className="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                最初
                            </Button>
                            
                            {/* Previous */}
                            <Button
                                onPress={() => onPageChange(currentPage - 1)}
                                isDisabled={currentPage === 1}
                                className="p-1.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <ChevronLeft className="w-4 h-4" />
                            </Button>
                            
                            {/* Page Numbers */}
                            <div className="flex items-center gap-1">
                                {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                                    let pageNum;
                                    if (totalPages <= 5) {
                                        pageNum = i + 1;
                                    } else if (currentPage <= 3) {
                                        pageNum = i + 1;
                                    } else if (currentPage >= totalPages - 2) {
                                        pageNum = totalPages - 4 + i;
                                    } else {
                                        pageNum = currentPage - 2 + i;
                                    }
                                    
                                    return (
                                        <Button
                                            key={pageNum}
                                            onPress={() => onPageChange(pageNum)}
                                            className={`min-w-[32px] h-8 text-sm font-medium rounded-lg transition-colors ${
                                                currentPage === pageNum
                                                    ? 'bg-gray-900 text-white'
                                                    : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            {pageNum}
                                        </Button>
                                    );
                                })}
                            </div>
                            
                            {/* Next */}
                            <Button
                                onPress={() => onPageChange(currentPage + 1)}
                                isDisabled={currentPage === totalPages}
                                className="p-1.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <ChevronRight className="w-4 h-4" />
                            </Button>
                            
                            {/* Last Page */}
                            <Button
                                onPress={() => onPageChange(totalPages)}
                                isDisabled={currentPage === totalPages}
                                className="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                最後
                            </Button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}