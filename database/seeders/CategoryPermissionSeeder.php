<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserCategory;
use Illuminate\Database\Seeder;

class CategoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryPermissions = [
            'admin' => [
                'users.read',
                'users.write',
                'users.delete',
            ],
            'user' => [
                // 基本ユーザーは特別な権限なし
            ],
            'engineer' => [
                'systems.deploy',
                'code.review',
                'technical.read',
            ],
            'sales' => [
                'sales.read',
                'sales.export',
                'customers.manage',
            ],
            'accounting' => [
                'finance.read',
                'finance.write',
                'reports.generate',
            ],
        ];

        foreach ($categoryPermissions as $categoryCode => $permissionKeys) {
            $category = UserCategory::where('code', $categoryCode)->first();

            if ($category) {
                // 権限キーからPermissionのIDを取得
                $permissionIds = Permission::whereIn('key', $permissionKeys)->pluck('id');

                // カテゴリーに権限を関連付け
                $category->permissions()->sync($permissionIds);
            }
        }
    }
}
