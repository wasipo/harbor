import React from 'react';
import { 
    Button,
    Menu,
    MenuItem,
    MenuTrigger,
    Popover,
} from 'react-aria-components';
import { 
    Users, 
    Shield, 
    Key, 
    LogOut, 
    ArrowRight,
    Building2,
    Check,
    X,
    UserPlus
} from 'lucide-react';

export interface UserData {
    id: string;
    name: string;
    email: string;
    categories: Array<{
        id: string;
        code: string;
        name: string;
        isPrimary?: boolean;
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
}

export interface DashboardProps {
    user: UserData;
    onLogout?: () => void;
    onNavigate?: (path: string) => void;
}

export const Dashboard: React.FC<DashboardProps> = ({ user, onLogout, onNavigate }) => {
    // Group permissions by resource
    const groupedPermissions = React.useMemo(() => {
        const groups: Record<string, Array<{ key: string; name: string; allowed: boolean }>> = {};
        
        const allPermissions = {
            users: ['read', 'create', 'update', 'delete'],
            sales: ['view', 'export', 'targets.set', 'team.manage'],
            system: ['settings', 'backup', 'logs'],
            audit: ['logs', 'reports.create', 'compliance.check'],
        };

        Object.entries(allPermissions).forEach(([resource, actions]) => {
            groups[resource] = actions.map(action => {
                const permKey = `${resource}.${action}`;
                const userPerm = user.permissions.find(p => p.key === permKey);
                return {
                    key: permKey,
                    name: userPerm?.name || action,
                    allowed: !!userPerm
                };
            });
        });

        return groups;
    }, [user.permissions]);

    const totalPermissions = Object.values(groupedPermissions).flat().length;
    const allowedPermissions = Object.values(groupedPermissions).flat().filter(p => p.allowed).length;

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <header className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    {/* Logo */}
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-gray-900 rounded flex items-center justify-center">
                            <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1" />
                            </svg>
                        </div>
                        <span className="font-semibold text-gray-900">Harbor</span>
                    </div>

                    {/* User Menu */}
                    <MenuTrigger>
                        <Button className="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 transition-colors">
                            <span className="text-sm text-gray-600">{user.email}</span>
                            <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <span className="text-gray-700 text-sm font-semibold">
                                    {user.name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                        </Button>
                        <Popover>
                            <Menu className="bg-white rounded-lg shadow-lg py-1 min-w-[180px] border border-gray-200">
                                <MenuItem 
                                    onAction={onLogout}
                                    className="px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                                >
                                    <LogOut className="w-4 h-4" />
                                    <span>ログアウト</span>
                                </MenuItem>
                            </Menu>
                        </Popover>
                    </MenuTrigger>
                </div>
            </header>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {/* Welcome Section */}
                <div className="mb-8">
                    <h1 className="text-2xl font-semi-bold text-gray-800">
                        こんにちは、{user.name}さん
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        権限とアクセス制御の管理ダッシュボード
                    </p>
                </div>

                {/* Quick Links */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div
                            className="bg-white rounded-lg border border-gray-200 p-4 opacity-50 cursor-not-allowed text-left pointer-events-none"
                        >
                            <div className="flex items-center justify-between mb-3">
                                <Users className="w-5 h-5 text-gray-400" />
                                <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">未実装</span>
                            </div>
                            <h3 className="text-sm font-semibold text-gray-500">ユーザー一覧</h3>
                        </div>
                        
                        <div
                            className="bg-white rounded-lg border border-gray-200 p-4 opacity-50 cursor-not-allowed text-left pointer-events-none"
                        >
                            <div className="flex items-center justify-between mb-3">
                                <UserPlus className="w-5 h-5 text-gray-400" />
                                <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">未実装</span>
                            </div>
                            <h3 className="text-sm font-semibold text-gray-500">ユーザー作成</h3>
                        </div>
                        
                        <div
                            className="bg-white rounded-lg border border-gray-200 p-4 opacity-50 cursor-not-allowed text-left pointer-events-none"
                        >
                            <div className="flex items-center justify-between mb-3">
                                <Shield className="w-5 h-5 text-gray-400" />
                                <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">未実装</span>
                            </div>
                            <h3 className="text-sm font-semibold text-gray-500">ロール管理</h3>
                        </div>
                        
                        <div
                            className="bg-white rounded-lg border border-gray-200 p-4 opacity-50 cursor-not-allowed text-left pointer-events-none"
                        >
                            <div className="flex items-center justify-between mb-3">
                                <Building2 className="w-5 h-5 text-gray-400" />
                                <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">未実装</span>
                            </div>
                            <h3 className="text-sm font-semibold text-gray-500">カテゴリ設定</h3>
                        </div>
                </div>

                {/* Primary Info Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    {/* Categories */}
                    <div className="bg-white rounded-lg border border-gray-200 p-5">
                        <div className="flex items-center gap-2 mb-4">
                            <Building2 className="w-4 h-4 text-gray-400" />
                            <h2 className="text-base font-semibold text-gray-900">所属カテゴリ</h2>
                        </div>
                        {user.categories.length === 0 ? (
                            <p className="text-sm text-gray-400">カテゴリが設定されていません</p>
                        ) : (
                            <ul className="space-y-2">
                                {user.categories.map((category) => (
                                    <li 
                                        key={category.id}
                                        className="flex items-center gap-2 text-sm text-gray-700"
                                    >
                                        <span className="text-gray-400">•</span>
                                        <span>{category.name}</span>
                                        {category.isPrimary && (
                                            <span className="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                主所属
                                            </span>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>

                    {/* Roles */}
                    <div className="bg-white rounded-lg border border-gray-200 p-5">
                        <div className="flex items-center gap-2 mb-4">
                            <Shield className="w-4 h-4 text-gray-400" />
                            <h2 className="text-base font-semibold text-gray-900">保有ロール</h2>
                        </div>
                        {user.roles.length === 0 ? (
                            <p className="text-sm text-gray-400">ロールが設定されていません</p>
                        ) : (
                            <ul className="space-y-3">
                                {user.roles.map((role) => (
                                    <li key={role.id}>
                                        <div className="flex items-start gap-2">
                                            <span className="text-gray-400 text-sm mt-0.5">•</span>
                                            <div>
                                                <p className="text-sm font-semibold text-gray-700">
                                                    {role.name}
                                                </p>
                                                {role.description && (
                                                    <p className="text-xs text-gray-500 mt-0.5">{role.description}</p>
                                                )}
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>

                {/* Permissions Overview */}
                <div className="bg-white rounded-lg border border-gray-200 p-5">
                    <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center gap-2">
                            <Key className="w-4 h-4 text-gray-400" />
                            <h2 className="text-base font-semibold text-gray-900">権限概要</h2>
                        </div>
                        <span className="text-sm text-gray-500">
                            {allowedPermissions} / {totalPermissions} 権限
                        </span>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {Object.entries(groupedPermissions).map(([resource, perms]) => {
                            const allowed = perms.filter(p => p.allowed).length;
                            const total = perms.length;
                            
                            return (
                                <div key={resource} className="bg-gray-50 rounded-lg p-3">
                                    <div className="flex items-center justify-between mb-2">
                                        <h3 className="text-sm font-semibold text-gray-900">
                                            {resource === 'users' && 'ユーザー管理'}
                                            {resource === 'sales' && '売上管理'}
                                            {resource === 'system' && 'システム管理'}
                                            {resource === 'audit' && '監査管理'}
                                        </h3>
                                        <span className="text-xs text-gray-500">
                                            {allowed}/{total}
                                        </span>
                                    </div>
                                    <div className="space-y-2">
                                        {perms.map(perm => (
                                            <div key={perm.key} className="flex items-center gap-2">
                                                {perm.allowed ? (
                                                    <Check className="w-3 h-3 text-green-600 flex-shrink-0" />
                                                ) : (
                                                    <X className="w-3 h-3 text-gray-300 flex-shrink-0" />
                                                )}
                                                <span className={`text-xs ${
                                                    perm.allowed ? 'text-gray-700' : 'text-gray-400'
                                                }`}>
                                                    {perm.name}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

            </main>
        </div>
    );
};