<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * 画面確認用のマスターデータを投入
     */
    public function run(): void
    {
        // カテゴリーマスタ
        $categories = [
            ['code' => 'MGMT', 'name' => '経営管理', 'description' => '経営層向けの管理機能'],
            ['code' => 'SALES', 'name' => '営業部門', 'description' => '営業活動に関する機能'],
            ['code' => 'DEV', 'name' => '開発部門', 'description' => '開発・技術に関する機能'],
            ['code' => 'HR', 'name' => '人事部門', 'description' => '人事・労務に関する機能'],
            ['code' => 'ACCT', 'name' => '経理部門', 'description' => '経理・財務に関する機能'],
            ['code' => 'MKTG', 'name' => 'マーケティング', 'description' => 'マーケティング活動に関する機能'],
            ['code' => 'CS', 'name' => 'カスタマーサポート', 'description' => '顧客対応に関する機能'],
            ['code' => 'QA', 'name' => '品質管理', 'description' => '品質管理・QAに関する機能'],
        ];

        foreach ($categories as $categoryData) {
            UserCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                [
                    'id' => Str::ulid()->toString(),
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                ]
            );
        }

        // 権限マスタ（より実用的な権限を追加）
        $permissions = [
            // ユーザー管理
            ['key' => 'users.view_all', 'resource' => 'users', 'action' => 'view_all', 'display_name' => '全ユーザー閲覧', 'description' => '全てのユーザー情報を閲覧する権限'],
            ['key' => 'users.view_own', 'resource' => 'users', 'action' => 'view_own', 'display_name' => '自分の情報閲覧', 'description' => '自分のユーザー情報のみ閲覧する権限'],
            ['key' => 'users.create', 'resource' => 'users', 'action' => 'create', 'display_name' => 'ユーザー作成', 'description' => '新規ユーザーを作成する権限'],
            ['key' => 'users.update_all', 'resource' => 'users', 'action' => 'update_all', 'display_name' => '全ユーザー更新', 'description' => '全てのユーザー情報を更新する権限'],
            ['key' => 'users.update_own', 'resource' => 'users', 'action' => 'update_own', 'display_name' => '自分の情報更新', 'description' => '自分のユーザー情報のみ更新する権限'],
            ['key' => 'users.delete', 'resource' => 'users', 'action' => 'delete', 'display_name' => 'ユーザー削除', 'description' => 'ユーザーを削除する権限'],
            ['key' => 'users.assign_role', 'resource' => 'users', 'action' => 'assign_role', 'display_name' => 'ロール割当', 'description' => 'ユーザーにロールを割り当てる権限'],

            // ロール管理
            ['key' => 'roles.view', 'resource' => 'roles', 'action' => 'view', 'display_name' => 'ロール閲覧', 'description' => 'ロール情報を閲覧する権限'],
            ['key' => 'roles.create', 'resource' => 'roles', 'action' => 'create', 'display_name' => 'ロール作成', 'description' => '新規ロールを作成する権限'],
            ['key' => 'roles.update', 'resource' => 'roles', 'action' => 'update', 'display_name' => 'ロール更新', 'description' => 'ロール情報を更新する権限'],
            ['key' => 'roles.delete', 'resource' => 'roles', 'action' => 'delete', 'display_name' => 'ロール削除', 'description' => 'ロールを削除する権限'],

            // カテゴリー管理
            ['key' => 'categories.view', 'resource' => 'categories', 'action' => 'view', 'display_name' => 'カテゴリー閲覧', 'description' => 'カテゴリー情報を閲覧する権限'],
            ['key' => 'categories.manage', 'resource' => 'categories', 'action' => 'manage', 'display_name' => 'カテゴリー管理', 'description' => 'カテゴリーを管理する権限'],

            // レポート
            ['key' => 'reports.view_all', 'resource' => 'reports', 'action' => 'view_all', 'display_name' => '全レポート閲覧', 'description' => '全てのレポートを閲覧する権限'],
            ['key' => 'reports.view_own', 'resource' => 'reports', 'action' => 'view_own', 'display_name' => '自分のレポート閲覧', 'description' => '自分に関連するレポートのみ閲覧する権限'],
            ['key' => 'reports.export', 'resource' => 'reports', 'action' => 'export', 'display_name' => 'レポートエクスポート', 'description' => 'レポートをエクスポートする権限'],

            // システム管理
            ['key' => 'system.config', 'resource' => 'system', 'action' => 'config', 'display_name' => 'システム設定', 'description' => 'システム設定を変更する権限'],
            ['key' => 'system.audit', 'resource' => 'system', 'action' => 'audit', 'display_name' => '監査ログ閲覧', 'description' => 'システムの監査ログを閲覧する権限'],
            ['key' => 'system.maintenance', 'resource' => 'system', 'action' => 'maintenance', 'display_name' => 'メンテナンス', 'description' => 'システムメンテナンスを実行する権限'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['key' => $permissionData['key']],
                [
                    'id' => Str::ulid()->toString(),
                    'resource' => $permissionData['resource'],
                    'action' => $permissionData['action'],
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                ]
            );
        }

        // ロールマスタ
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'スーパー管理者',
                'permissions' => ['*'], // 全権限
            ],
            [
                'name' => 'admin',
                'display_name' => '管理者',
                'permissions' => [
                    'users.view_all', 'users.create', 'users.update_all', 'users.delete', 'users.assign_role',
                    'roles.view', 'roles.create', 'roles.update', 'roles.delete',
                    'categories.view', 'categories.manage',
                    'reports.view_all', 'reports.export',
                    'system.audit',
                ],
            ],
            [
                'name' => 'manager',
                'display_name' => 'マネージャー',
                'permissions' => [
                    'users.view_all', 'users.update_all',
                    'roles.view',
                    'categories.view',
                    'reports.view_all', 'reports.export',
                ],
            ],
            [
                'name' => 'leader',
                'display_name' => 'リーダー',
                'permissions' => [
                    'users.view_all', 'users.update_own',
                    'categories.view',
                    'reports.view_all',
                ],
            ],
            [
                'name' => 'member',
                'display_name' => '一般メンバー',
                'permissions' => [
                    'users.view_own', 'users.update_own',
                    'reports.view_own',
                ],
            ],
            [
                'name' => 'guest',
                'display_name' => 'ゲスト',
                'permissions' => [
                    'users.view_own',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'id' => Str::ulid()->toString(),
                    'display_name' => $roleData['display_name'],
                ]
            );

            // 権限の関連付け
            if ($roleData['permissions'][0] === '*') {
                // 全権限を付与
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    \DB::table('role_permissions')->insertOrIgnore([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // 指定された権限のみ付与
                $permissions = Permission::whereIn('key', $roleData['permissions'])->get();
                foreach ($permissions as $permission) {
                    \DB::table('role_permissions')->insertOrIgnore([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // サンプルユーザー
        $users = [
            [
                'name' => 'スーパー管理者',
                'email' => 'super@example.com',
                'password' => 'password',
                'role' => 'super_admin',
                'categories' => ['経営管理'],
            ],
            [
                'name' => '山田太郎（管理者）',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
                'categories' => ['経営管理', '人事部門'],
            ],
            [
                'name' => '鈴木花子（営業マネージャー）',
                'email' => 'suzuki@example.com',
                'password' => 'password',
                'role' => 'manager',
                'categories' => ['営業部門', 'マーケティング'],
            ],
            [
                'name' => '佐藤次郎（開発リーダー）',
                'email' => 'sato@example.com',
                'password' => 'password',
                'role' => 'leader',
                'categories' => ['開発部門', '品質管理'],
            ],
            [
                'name' => '田中美咲（経理担当）',
                'email' => 'tanaka@example.com',
                'password' => 'password',
                'role' => 'member',
                'categories' => ['経理部門'],
            ],
            [
                'name' => '高橋健（カスタマーサポート）',
                'email' => 'takahashi@example.com',
                'password' => 'password',
                'role' => 'member',
                'categories' => ['カスタマーサポート'],
            ],
            [
                'name' => '伊藤さくら（人事担当）',
                'email' => 'ito@example.com',
                'password' => 'password',
                'role' => 'member',
                'categories' => ['人事部門'],
            ],
            [
                'name' => '渡辺大輔（開発メンバー）',
                'email' => 'watanabe@example.com',
                'password' => 'password',
                'role' => 'member',
                'categories' => ['開発部門'],
            ],
            [
                'name' => '中村優子（マーケティング）',
                'email' => 'nakamura@example.com',
                'password' => 'password',
                'role' => 'member',
                'categories' => ['マーケティング'],
            ],
            [
                'name' => 'ゲストユーザー',
                'email' => 'guest@example.com',
                'password' => 'password',
                'role' => 'guest',
                'categories' => [],
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'id' => Str::ulid()->toString(),
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            // カテゴリー割り当て
            $categories = UserCategory::whereIn('name', $userData['categories'])->get();
            foreach ($categories as $index => $category) {
                \DB::table('user_category_assignments')->insertOrIgnore([
                    'id' => Str::ulid()->toString(),
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'is_primary' => $index === 0, // 最初のカテゴリーを主種別とする
                    'effective_from' => now()->toDateString(),
                    'effective_until' => null, // 無期限
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ロール割り当て
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                UserRole::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                    ],
                    [
                        'id' => Str::ulid()->toString(),
                        'assigned_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('マスターデータの投入が完了しました。');
        $this->command->info('ログイン情報:');
        $this->command->info('  - super@example.com / password (スーパー管理者)');
        $this->command->info('  - admin@example.com / password (管理者)');
        $this->command->info('  - suzuki@example.com / password (営業マネージャー)');
        $this->command->info('  - その他のユーザーも全て password でログイン可能');
    }
}
