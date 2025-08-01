<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'スーパー管理者',
                'permissions' => [
                    'users.read',
                    'users.write',
                    'users.delete',
                    'system.admin',
                    'profile.read',
                    'profile.write',
                ],
            ],
            [
                'name' => 'admin',
                'display_name' => '管理者',
                'permissions' => [
                    'users.read',
                    'users.write',
                    'profile.read',
                    'profile.write',
                ],
            ],
            [
                'name' => 'manager',
                'display_name' => 'マネージャー',
                'permissions' => [
                    'users.read',
                    'profile.read',
                    'profile.write',
                ],
            ],
            [
                'name' => 'basic',
                'display_name' => '一般ユーザー',
                'permissions' => [
                    'profile.read',
                    'profile.write',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            // ロールを作成または取得
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'id' => Str::ulid()->toString(),
                    'name' => $roleData['name'],
                    'display_name' => $roleData['display_name'],
                ]
            );

            // 権限を関連付け
            $permissionIds = Permission::whereIn('key', $roleData['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
