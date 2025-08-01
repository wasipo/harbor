<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserCategoryAssignment;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 基本テストユーザー作成
        /** @var array<int, array{name: string, email: string, password: string, is_active: bool, email_verified_at: \Illuminate\Support\Carbon, category_code: string, role_name: string}> */
        $users = [
            [
                'name' => 'Harbor Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'category_code' => 'admin',
                'role_name' => 'super_admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'category_code' => 'user',
                'role_name' => 'basic',
            ],
            [
                'name' => 'Test Engineer',
                'email' => 'engineer@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'category_code' => 'engineer',
                'role_name' => 'basic',
            ],
            [
                'name' => 'Test Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'category_code' => 'sales',
                'role_name' => 'manager',
            ],
        ];

        foreach ($users as $userData) {
            // ユーザー作成
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'id' => Str::ulid()->toString(),
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'is_active' => $userData['is_active'],
                    'email_verified_at' => $userData['email_verified_at'],
                ]
            );

            // カテゴリ割り当て
            $category = UserCategory::where('code', $userData['category_code'])->first();
            if ($category) {
                UserCategoryAssignment::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                    ],
                    [
                        'id' => Str::ulid()->toString(),
                        'is_primary' => true,
                        'effective_from' => now()->toDateString(),
                        'effective_until' => null,
                    ]
                );
            }

            // ロール割り当て
            $role = Role::where('name', $userData['role_name'])->first();
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
    }
}
