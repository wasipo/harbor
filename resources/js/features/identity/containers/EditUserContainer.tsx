import React from 'react';
import { router } from '@inertiajs/react';
import { EditUserForm } from '../components/EditUserForm';
import type { UserEditData } from '../components/EditUserForm';
import { ArrowLeft, Shield, Building2, AlertCircle } from 'lucide-react';

interface EditUserContainerProps {
    user: UserEditData & {
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

export function EditUserContainer({ user }: EditUserContainerProps) {
    const handleCancel = () => {
        router.get(route('users.index'));
    };

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
                        <li>
                            <button
                                onClick={() => router.get(route('users.index'))}
                                className="text-gray-500 hover:text-gray-700 transition-colors"
                            >
                                ユーザー管理
                            </button>
                        </li>
                        <li className="text-gray-400">/</li>
                        <li className="text-gray-900 font-medium">ユーザー編集</li>
                    </ol>
                </nav>

                {/* Back Button */}
                <button
                    onClick={() => router.get(route('users.index'))}
                    className="mb-6 inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4 mr-1" />
                    ユーザー一覧に戻る
                </button>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {/* Edit Form */}
                    <div className="lg:col-span-8">
                        <EditUserForm user={user} onCancel={handleCancel} />
                    </div>

                    {/* Side Info */}
                    <div className="lg:col-span-4 space-y-4">
                        {/* Current Categories */}
                        <div className="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div className="px-4 py-3 border-b border-gray-100">
                                <div className="flex items-center gap-2">
                                    <Building2 className="w-4 h-4 text-gray-500" />
                                    <h3 className="text-sm font-semibold text-gray-900">所属カテゴリ</h3>
                                </div>
                            </div>
                            <div className="p-4">
                                {user.categories.length === 0 ? (
                                    <p className="text-sm text-gray-400 text-center py-3">カテゴリが設定されていません</p>
                                ) : (
                                    <div className="space-y-2">
                                        {user.categories.map((category) => (
                                            <div key={category.id} className="flex items-center justify-between py-1">
                                                <span className="text-sm text-gray-700">{category.name}</span>
                                                {category.isPrimary && (
                                                    <span className="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">主所属</span>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="px-4 py-3 bg-gray-50 border-t border-gray-100">
                                <button
                                    onClick={() => router.get(route('users.categories.edit', user.id))}
                                    className="w-full text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                                >
                                    カテゴリを編集
                                </button>
                            </div>
                        </div>

                        {/* Current Roles */}
                        <div className="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div className="px-4 py-3 border-b border-gray-100">
                                <div className="flex items-center gap-2">
                                    <Shield className="w-4 h-4 text-gray-500" />
                                    <h3 className="text-sm font-semibold text-gray-900">保有ロール</h3>
                                </div>
                            </div>
                            <div className="p-4">
                                {user.roles.length === 0 ? (
                                    <p className="text-sm text-gray-400 text-center py-3">ロールが設定されていません</p>
                                ) : (
                                    <div className="space-y-2">
                                        {user.roles.map((role) => (
                                            <div key={role.id} className="flex items-center py-1">
                                                <span className="text-sm text-gray-700">{role.name}</span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="px-4 py-3 bg-gray-50 border-t border-gray-100">
                                <button
                                    onClick={() => router.get(route('users.roles.edit', user.id))}
                                    className="w-full text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                                >
                                    ロールを編集
                                </button>
                            </div>
                        </div>

                        {/* Note */}
                        <div className="bg-blue-50 rounded-lg border border-blue-200 p-4">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <AlertCircle className="w-5 h-5 text-blue-600" />
                                </div>
                                <div className="ml-3">
                                    <h4 className="text-sm font-medium text-blue-900">ご注意</h4>
                                    <p className="mt-1 text-xs text-blue-700">
                                        ここではユーザーの基本情報のみ編集できます。
                                        カテゴリやロールの変更は、それぞれの編集ボタンから行ってください。
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
}