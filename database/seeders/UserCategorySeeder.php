<?php

namespace Database\Seeders;

use App\Models\UserCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'admin',
                'name' => '管理者',
                'is_active' => true,
            ],
            [
                'code' => 'user',
                'name' => '一般ユーザー',
                'is_active' => true,
            ],
            [
                'code' => 'engineer',
                'name' => 'エンジニア',
                'is_active' => true,
            ],
            [
                'code' => 'sales',
                'name' => '営業',
                'is_active' => true,
            ],
            [
                'code' => 'accounting',
                'name' => '経理',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            UserCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                array_merge($categoryData, ['id' => Str::ulid()->toString()])
            );
        }
    }
}
